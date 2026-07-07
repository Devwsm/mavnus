<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class product extends Model
{
    //
    use HasFactory;

    protected $primaryKey = 'id_product';

    protected $fillable = [
        'category',
        'name',
        'slug',
        'price',
        'description',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'integer',
    ];

    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id_product')->orderBy('sort_order');
    }

    public function clothes()
    {
        return $this->hasOne(Clothes::class, 'product_id', 'id_product');
    }

    public function scopeClothesCategory($query)
    {
        return $query->where('category', 'clothes');
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function getFormattedPriceAttribute(): string
    {
        return 'Rp' . number_format($this->price, 0, ',', '.');
    }
}