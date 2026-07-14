<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CartItem extends Model
{
    //
    protected $primaryKey = 'id_cart_item';
    protected $fillable = [
        'session_id',
        'product_id',
        'variant_id',
        'quantity',
    ];

    public function product()
    {
        return $this->belongsTo(product::class, 'product_id', 'id_product');
    }

    public function variant()
    {
        return $this->belongsTo(ProductVariant::class, 'variant_id', 'id_variant');
    }

    public function getSubtotalAttribute(): int
    {
        return $this->product->price * $this->quantity;
    }
}