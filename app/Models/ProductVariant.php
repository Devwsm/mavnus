<?php

namespace App\Models;

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
        return $this->belongsTo(Product::class, 'product_id', 'id_product');
    }
}