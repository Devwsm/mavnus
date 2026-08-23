<?php

namespace App\Support;

use App\Models\Order;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class OrderCleanup
{
    // Key cache buat throttle, biar gak query berat tiap request
    protected const LOCK_KEY = 'mavnus_order_cleanup_lock';

    /**
     * Jalanin cleanup kalau belum pernah jalan dalam beberapa menit terakhir.
     * Aman dipanggil di tiap request (lewat middleware) karena Cache::add()
     * atomic - cuma 1 request yang bakal lolos throttle-nya.
     */
    public static function runIfDue(): void
    {
        $throttleMinutes = (int) env('ORDER_CLEANUP_THROTTLE_MINUTES', 5);

        if (! Cache::add(self::LOCK_KEY, true, now()->addMinutes($throttleMinutes))) {
            return; // baru aja jalan, skip biar hemat
        }

        self::run();
    }

    /**
     * Jalanin cleanup langsung, tanpa throttle. Dipakai command manual buat testing.
     */
    public static function run(): int
    {
        $expireMinutes = (int) env('ORDER_EXPIRE_MINUTES', 60);
        $cutoff = now()->subMinutes($expireMinutes);

        $expiredOrders = Order::where('status', 'pending')
            ->where('payment_status', 'unpaid')
            ->where('created_at', '<=', $cutoff)
            ->with('items.variant.product')
            ->get();

        if ($expiredOrders->isEmpty()) {
            return 0;
        }

        foreach ($expiredOrders as $order) {
            DB::transaction(function () use ($order) {
                // Kembalikan stok tiap item yang punya variant
                foreach ($order->items as $item) {
                    if ($item->variant_id && $item->variant) {
                        $item->variant->increment('stock', $item->quantity);
                        $item->variant->product?->syncActiveStatus();
                    }
                }

                // Hapus folder snapshot gambar order ini kalau ada
                Storage::disk('public')->deleteDirectory('orders/' . $order->order_number);

                // Hapus order (order_items ikut kehapus karena cascadeOnDelete)
                $order->delete();
            });
        }

        $count = $expiredOrders->count();
        Log::info("[OrderCleanup] {$count} order pending dibatalkan & dihapus otomatis (lewat {$expireMinutes} menit tanpa pembayaran).");

        return $count;
    }
}