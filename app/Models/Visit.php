<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    //
    protected $fillable = [
        'ip_address',
        'session_id',
        'url',
        'referrer',
        'user_agent',
        'device_type',
        'browser',
    ];

    /**
     * Deteksi ringan tipe perangkat dari user agent (tanpa package tambahan).
     */
    public static function detectDeviceType(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'unknown';
        }

        if (preg_match('/bot|crawl|spider|slurp|bingpreview|facebookexternalhit/i', $userAgent)) {
            return 'bot';
        }

        if (preg_match('/ipad|tablet(?!.*mobile)/i', $userAgent)) {
            return 'tablet';
        }

        if (preg_match('/mobile|android|iphone|ipod/i', $userAgent)) {
            return 'mobile';
        }

        return 'desktop';
    }

    /**
     * Deteksi ringan nama browser dari user agent.
     */
    public static function detectBrowser(?string $userAgent): string
    {
        if (!$userAgent) {
            return 'Unknown';
        }

        return match (true) {
            (bool) preg_match('/edg\//i', $userAgent) => 'Edge',
            (bool) preg_match('/opr\/|opera/i', $userAgent) => 'Opera',
            (bool) preg_match('/chrome|crios/i', $userAgent) => 'Chrome',
            (bool) preg_match('/firefox|fxios/i', $userAgent) => 'Firefox',
            (bool) preg_match('/safari/i', $userAgent) => 'Safari',
            default => 'Lainnya',
        };
    }

    /**
     * Pola URL (regex) => nama halaman yang mudah dibaca.
     * Urutan penting: pola yang lebih spesifik ditaruh lebih dulu.
     */
    protected static function urlLabelPatterns(): array
    {
        return [
            '#^/$#'                      => 'Home',
            '#^/order/checkout/?$#'      => 'Checkout',
            '#^/order/[^/]+/success/?$#' => 'Pesanan Sukses',
            '#^/clothes/[^/]+/?$#'       => 'Detail Produk Clothes',
            '#^/clothes/?$#'             => 'Daftar Clothes',
            '#^/accessoris/?$#'          => 'Daftar Accessories',
            '#^/info/?$#'                => 'Info',
        ];
    }

    /**
     * Ubah path teknis (mis. /order/MVN-xxx/success) jadi nama halaman
     * yang mudah dipahami (mis. "Pesanan Sukses").
     */
    public static function friendlyLabel(string $url): string
    {
        foreach (static::urlLabelPatterns() as $pattern => $label) {
            if (preg_match($pattern, $url)) {
                return $label;
            }
        }

        // Fallback: url tidak dikenali, tampilkan apa adanya
        return $url;
    }
}