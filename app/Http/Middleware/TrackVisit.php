<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\Response;

class TrackVisit
{
    /**
     * Prefix path yang tidak perlu dicatat sebagai kunjungan:
     * - area admin & aset (dashboard, login, storage, build)
     * - endpoint data yang selalu dipanggil via fetch() oleh JS dan selalu
     *   balikin JSON, bukan halaman yang benar-benar dibuka pengunjung
     *   (cart = badge keranjang, search = live suggestion, shipping = autocomplete tujuan)
     */
    protected array $excludedPrefixes = [
        'dashboard',
        'login',
        'logout',
        'storage',
        'build',
        'cart',
        'search',
        'shipping',
        'sitemap.xml',
        'robots.txt',
        'newsletter',
        'lang',
    ];

    public function handle(Request $request, Closure $next): Response
    {
        if ($this->shouldTrack($request)) {
            $this->record($request);
        }

        return $next($request);
    }

    protected function shouldTrack(Request $request): bool
    {
        // Hanya catat kunjungan halaman (GET), bukan submit form / aksi AJAX
        if (!$request->isMethod('get')) {
            return false;
        }

        if ($request->ajax() || $request->wantsJson()) {
            return false;
        }

        $path = ltrim($request->path(), '/');

        foreach ($this->excludedPrefixes as $prefix) {
            if ($path === $prefix || str_starts_with($path, $prefix . '/')) {
                return false;
            }
        }

        return true;
    }

    /**
     * Mencatat kunjungan. Kegagalan mencatat (mis. database sedang bermasalah) TIDAK boleh
     * membuat halaman gagal dibuka: statistik hanya pelengkap, bukan bagian dari transaksi.
     * Nilai yang berasal dari browser (alamat, referrer) dipotong dulu supaya muat di kolom
     * tabel visits (255 karakter); header Referer dari mesin pencari sering lebih panjang.
     */
    protected function record(Request $request): void
    {
        try {
            $userAgent = (string) $request->userAgent();
            $referrer = $request->headers->get('referer');

            Visit::create([
                'ip_address'  => Str::limit((string) $request->ip(), 45, ''),
                'session_id'  => Str::limit((string) $request->session()->getId(), 250, ''),
                'url'         => Str::limit('/' . ltrim($request->path(), '/'), 250, ''),
                'referrer'    => $referrer ? Str::limit($referrer, 250, '') : null,
                'user_agent'  => $userAgent,
                'device_type' => Visit::detectDeviceType($userAgent),
                'browser'     => Visit::detectBrowser($userAgent),
            ]);
        } catch (\Throwable $e) {
            report($e);
        }
    }
}