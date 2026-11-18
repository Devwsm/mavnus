<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ProductVariant extends Model
{
    //
    protected $primaryKey = 'id_variant';
    protected $fillable = [
        'product_id',
        'label',
        'stock',
    ];
    protected $casts = [
        'stock' => 'integer',
    ];
    public function product()
    {
        return $this->belongsTo(product::class, 'product_id', 'id_product');
    }

    // Scope: hanya varian dari produk yang masih aktif (yang sudah nonaktif tidak perlu diperhatikan stoknya)
    public function scopeForActiveProduct(Builder $query): Builder
    {
        return $query->whereHas('product', fn(Builder $q) => $q->where('is_active', true));
    }

    // Scope: stok menipis (1 sampai batas product::LOW_STOCK_THRESHOLD)
    public function scopeLowStock(Builder $query): Builder
    {
        return $query->where('stock', '>', 0)->where('stock', '<=', product::LOW_STOCK_THRESHOLD);
    }
}