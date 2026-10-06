<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Order extends Model
{
    //
    protected $primaryKey = 'id_order';
    protected $fillable = [
        'order_number',
        'user_id',
        'customer_name',
        'customer_phone',
        'customer_address',
        'shipping_courier',
        'shipping_service',
        'shipping_cost',
        'total_weight',
        'subtotal',
        'total',
        'status',
        'payment_status',
        'payment_method',
        'midtrans_order_id',
        'midtrans_transaction_id',
        'paid_at',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function getRouteKeyName()
    {
        return 'order_number';
    }

    public function items()
    {
        return $this->hasMany(OrderItem::class, 'order_id', 'id_order');
    }

    // Relasi ke akun customer - nullable, order guest gak punya user_id
    public function user()
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id');
    }

    /**
     * Status tujuan yang boleh dipilih dari status sekarang (termasuk status itu sendiri).
     * 'cancelled' tidak bisa dibuka lagi (stok sudah dikembalikan), dan tidak ada jalan
     * balik ke 'pending' karena pesanan pending lama langsung kena pembatalan otomatis.
     */
    public static function nextStatuses(string $current): array
    {
        return match ($current) {
            'pending'    => ['pending', 'processing', 'cancelled'],
            'processing' => ['processing', 'shipped', 'cancelled'],
            'shipped'    => ['shipped', 'processing', 'completed'],
            'completed'  => ['completed', 'shipped'],
            default      => [$current], // cancelled & status tak dikenal: terkunci
        };
    }

    public static function generateOrderNumber(): string
    {
        return 'MVN-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }
}