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

    public static function generateOrderNumber(): string
    {
        return 'MVN-' . now()->format('Ymd') . '-' . strtoupper(Str::random(6));
    }
}