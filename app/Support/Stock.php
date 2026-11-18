<?php

namespace App\Support;

use App\Models\product;
use App\Models\ProductVariant;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * Ringkasan kondisi stok. Clothes punya stok per varian ukuran, accessories punya satu
 * stok di produknya sendiri, jadi keduanya disatukan sebagai "unit stok" di sini.
 * Semua angka hanya menghitung produk yang masih aktif.
 */
class Stock
{
    /** Stok 1..LOW_THRESHOLD dianggap menipis. */
    public const LOW_THRESHOLD = product::LOW_STOCK_THRESHOLD;

    /**
     * @return array{units: int, low: int, out: int, safe: int}
     */
    public static function summary(): array
    {
        // Disimpan di request yang sedang berjalan: dipakai badge navbar DAN kartu dashboard,
        // jadi dihitung sekali per halaman (dan tidak "nyangkut" antar request).
        $attributes = request()->attributes;

        if (! $attributes->has('stock.summary')) {
            $attributes->set('stock.summary', self::compute());
        }

        return $attributes->get('stock.summary');
    }

    public static function lowCount(): int
    {
        return self::summary()['low'];
    }

    /**
     * Item paling kritis (stok habis dulu, lalu yang menipis), gabungan varian clothes dan accessories.
     *
     * @return Collection<int, array{product: ?product, label: string, stock: int}>
     */
    public static function critical(int $limit = 5): Collection
    {
        $clothes = ProductVariant::with('product.images')
            ->forActiveProduct()
            ->where('stock', '<=', self::LOW_THRESHOLD)
            ->orderBy('stock')
            ->limit($limit)
            ->get()
            ->map(fn(ProductVariant $variant) => [
                'product' => $variant->product,
                'label'   => 'Ukuran ' . $variant->label,
                'stock'   => $variant->stock,
            ]);

        $accessories = product::accessoriesCategory()
            ->with(['images', 'accessories'])
            ->where('is_active', true)
            ->where('stock', '<=', self::LOW_THRESHOLD)
            ->orderBy('stock')
            ->limit($limit)
            ->get()
            ->map(fn(product $item) => [
                'product' => $item,
                'label'   => $item->accessories?->type_label ?? 'Aksesoris',
                'stock'   => (int) $item->stock,
            ]);

        return $clothes->concat($accessories)->sortBy('stock')->take($limit)->values();
    }

    private static function compute(): array
    {
        $low = self::LOW_THRESHOLD;
        $aggregate = "COUNT(*) as units,
            SUM(CASE WHEN stock = 0 THEN 1 ELSE 0 END) as out_count,
            SUM(CASE WHEN stock > 0 AND stock <= {$low} THEN 1 ELSE 0 END) as low_count";

        // Dua query agregat (varian clothes + accessories), bukan satu query per angka.
        $variants = ProductVariant::forActiveProduct()
            ->select(DB::raw($aggregate))
            ->first();
        $accessories = product::accessoriesCategory()
            ->where('is_active', true)
            ->select(DB::raw($aggregate))
            ->first();

        $units = (int) $variants->units + (int) $accessories->units;
        $out = (int) $variants->out_count + (int) $accessories->out_count;
        $lowCount = (int) $variants->low_count + (int) $accessories->low_count;

        return [
            'units' => $units,
            'low'   => $lowCount,
            'out'   => $out,
            'safe'  => max(0, $units - $lowCount - $out),
        ];
    }
}