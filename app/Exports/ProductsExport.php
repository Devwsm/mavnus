<?php

namespace App\Exports;

use App\Models\product;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class ProductsExport implements FromCollection, WithHeadings, WithMapping
{
    public function collection()
    {
        return Product::with(['clothes', 'variants'])->get();
    }

    public function headings(): array
    {
        return ['Nama', 'Kategori', 'Harga', 'Berat (gram)', 'Deskripsi', 'Warna', 'Material', 'Status', 'Total Stok', 'Rincian Ukuran & Stok'];
    }

    public function map($product): array
    {
        $variantDetail = $product->variants
            ->map(fn($v) => "{$v->label}: {$v->stock}")
            ->implode(', ');
        return [
            $product->name,
            $product->category,
            $product->price,
            $product->weight,
            $product->description,
            $product->clothes->color ?? '-',
            $product->clothes->material ?? '-',
            $product->is_active ? 'Tersedia' : 'Sold Out',
            $product->variants->sum('stock'),
            $variantDetail ?: '-',
        ];
    }
}