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
        // Pakai config(), bukan env(): env() mengembalikan null setelah `config:cache`
        $throttleMinutes = max(1, (int) config('mavnus.order_cleanup_throttle_minutes', 5));

        if (! Cache::add(self::LOCK_KEY, true, now()->addMinutes($throttleMinutes))) {
            return; // baru aja jalan, skip biar hemat
        }

        self::run();
    }

    /**
     * Kembalikan stok semua item pesanan. Clothes balik ke variannya, accessories
     * balik ke kolom stock produknya. Dipakai saat pesanan kedaluwarsa DAN saat staf
     * membatalkan pesanan. Harus dipanggil di dalam transaksi.
     */
    public static function restock(Order $order): void
    {
        $order->loadMissing('items.variant.product', 'items.product');

        foreach ($order->items as $item) {
            if ($item->variant_id && $item->variant) {
                $item->variant->increment('stock', $item->quantity);
                $item->variant->product?->syncActiveStatus();
            } elseif (! $item->variant_id && $item->product && $item->product->category === 'accessories') {
                // Catatan: produk clothes yang variannya sudah dihapus TIDAK dikembalikan ke
                // kolom stock produk (kolom itu null untuk clothes, stok clothes ada di varian)
                $item->product->increment('stock', $item->quantity);
                $item->product->syncActiveStatus();
            }
        }
    }

    /**
     * Jalanin cleanup langsung, tanpa throttle. Dipakai command manual buat testing.
     */
    public static function run(): int
    {
        $expireMinutes = (int) config('mavnus.order_expire_minutes', 60);
        $cutoff = now()->subMinutes($expireMinutes);

        // Keranjang yang sudah lama ditinggal (tiap pengunjung punya satu "kunci" keranjang,
        // jadi tabelnya bakal terus membengkak kalau tidak dibersihkan)
        $cartDays = (int) config('mavnus.cart_retention_days', 30);
        DB::table('cart_items')->where('updated_at', '<', now()->subDays($cartDays))->delete();

        $expiredIds = Order::where('status', 'pending')
            ->where('payment_status', 'unpaid')
            ->where('created_at', '<=', $cutoff)
            ->pluck('id_order');

        if ($expiredIds->isEmpty()) {
            return 0;
        }

        $count = 0;

        foreach ($expiredIds as $orderId) {
            $deleted = DB::transaction(function () use ($orderId, $cutoff) {
                // Kunci & cek ulang statusnya: staf bisa saja baru saja memproses pesanan ini
                // beberapa detik setelah daftar kedaluwarsa di atas diambil.
                $order = Order::whereKey($orderId)
                    ->where('status', 'pending')
                    ->where('payment_status', 'unpaid')
                    ->where('created_at', '<=', $cutoff)
                    ->lockForUpdate()
                    ->first();

                if (! $order) {
                    return false;
                }

                self::restock($order);

                // Hapus folder snapshot gambar order ini kalau ada
                Storage::disk('public')->deleteDirectory('orders/' . $order->order_number);

                // Hapus order (order_items ikut kehapus karena cascadeOnDelete)
                $order->delete();

                return true;
            });

            if ($deleted) {
                $count++;
            }
        }

        if ($count > 0) {
            Log::info("[OrderCleanup] {$count} order pending dibatalkan & dihapus otomatis (lewat {$expireMinutes} menit tanpa pembayaran).");
        }

        return $count;
    }
}