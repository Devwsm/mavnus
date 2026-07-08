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

    // Relasi: satu produk bisa punya banyak foto, diurutkan sesuai sort_order
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id_product')->orderBy('sort_order');
    }

    // Relasi: satu produk (kategori clothes) punya satu detail warna/material
    public function clothes()
    {
        return $this->hasOne(Clothes::class, 'product_id', 'id_product');
    }

    public function accessories()
    {
        return $this->hasOne(Accessoris::class, 'product_id', 'id_product');
    }

    public function albums()
    {
        return $this->hasOne(Albums::class, 'product_id', 'id_product');
    }

    

    // Sync status
    public function syncActiveStatus(): void
    {
        $totalStock = $this->clothes?->variants->sum('stock') ?? 0;

        $this->update([
            'is_active' => $totalStock > 0,
        ]);
    }

    // Scope: filter produk yang statusnya aktif (stok tersedia)
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    // Scope: filter produk yang kategorinya "clothes" saja
    public function scopeClothesCategory($query)
    {
        return $query->where('category', 'clothes');
    }

    // Accessor: format harga jadi "Rp250.000", dipanggil lewat $product->formatted_price
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp' . number_format($this->price, 0, ',', '.');
    }
}