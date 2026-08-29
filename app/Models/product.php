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
        'weight',
        'description',
        'is_active',
        'published_at',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'price' => 'integer',
        'published_at' => 'datetime',
    ];

    // Relasi: satu produk bisa punya banyak foto, diurutkan sesuai sort_order
    public function images()
    {
        return $this->hasMany(ProductImage::class, 'product_id', 'id_product')->orderBy('sort_order');
    }

    // Relasi: satu produk (kategori cl othes) punya satu detail warna/material
    public function clothes()
    {
        return $this->hasOne(clothes::class, 'product_id', 'id_product');
    }

    // Relasi: satu produk (kategori accessoris) punya satu detail warna/material
    public function accessories()
    {
        return $this->hasOne(accessoris::class, 'product_id', 'id_product');
    }

    public function variants()
    {
        return $this->hasMany(ProductVariant::class, 'product_id', 'id_product')
            ->orderByRaw("FIELD(label, 'S', 'M', 'L', 'XL')");
    }

    // Sync status
    public function syncActiveStatus(): void
    {
        $totalStock = $this->variants()->sum('stock');

        $this->update([
            'is_active' => $totalStock > 0,
        ]);
    }

    // Scope: filter produk yang statusnya aktif (stok tersedia) DAN udah lewat waktu rilisnya
    // (dipakai di semua query storefront - home, listing, search, sitemap - jadi produk
    // yang masih dijadwalkan otomatis kesembunyi tanpa perlu ubah tiap controller)
    public function scopeActive($query)
    {
        return $query->where('is_active', true)
            ->whereNotNull('published_at')
            ->where('published_at', '<=', now());
    }

    // Accessor: true kalau produk masih dijadwalkan (belum waktunya tayang)
    public function getIsScheduledAttribute(): bool
    {
        return $this->published_at && $this->published_at->isFuture();
    }

    // Scope: filter produk yang kategorinya "clothes" saja
    public function scopeClothesCategory($query)
    {
        return $query->where('category', 'clothes');
    }

    // Scope: filter produk yang kategorinya "accessoris" saja
    public function scopeAccessoriesCategory($query)
    {
        return $query->where('category', 'accessories');
    }

    // Accessor: format harga jadi "Rp250.000", dipanggil lewat $product->formatted_price
    public function getFormattedPriceAttribute(): string
    {
        return 'Rp' . number_format($this->price, 0, ',', '.');
    }
}