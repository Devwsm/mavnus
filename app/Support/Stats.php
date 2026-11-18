<?php

namespace App\Support;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;

class Stats
{
    /**
     * Jumlah baris per hari untuk N hari terakhir (hari ini ikut), satu query saja.
     * Hari tanpa data tetap muncul dengan total 0, jadi langsung bisa dipakai untuk grafik batang.
     *
     * @return Collection<int, array{label: string, date: string, total: int}>
     */
    public static function dailyCounts(Builder $query, int $days = 7, string $labelFormat = 'd/m'): Collection
    {
        $raw = (clone $query)
            ->where('created_at', '>=', now()->subDays($days - 1)->startOfDay())
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        return collect(range($days - 1, 0))->map(function (int $daysAgo) use ($raw, $labelFormat) {
            $date = now()->subDays($daysAgo);

            return [
                'label' => $date->translatedFormat($labelFormat),
                'date'  => $date->format('Y-m-d'),
                'total' => (int) $raw->get($date->format('Y-m-d'), 0),
            ];
        })->values();
    }

    /** Tinggi maksimum untuk skala grafik batang (minimal 1 supaya tidak bagi nol). */
    public static function max(Collection $series, string $key = 'total'): int|float
    {
        return max(1, $series->max($key) ?? 0);
    }
}