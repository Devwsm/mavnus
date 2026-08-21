<?php

namespace App\Http\Middleware;

use App\Models\Visit;
use Closure;
use Illuminate\Http\Request;
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

    protected function record(Request $request): void
    {
        $userAgent = (string) $request->userAgent();

        Visit::create([
            'ip_address'  => (string) $request->ip(),
            'session_id'  => $request->session()->getId(),
            'url'         => '/' . ltrim($request->path(), '/'),
            'referrer'    => $request->headers->get('referer'),
            'user_agent'  => $userAgent,
            'device_type' => Visit::detectDeviceType($userAgent),
            'browser'     => Visit::detectBrowser($userAgent),
        ]);
    }
}