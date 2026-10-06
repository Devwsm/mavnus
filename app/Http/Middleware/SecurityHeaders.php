<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SecurityHeaders
{
    public function handle(Request $request, Closure $next): Response
    {
        $response = $next($request);

        $response->headers->set('X-Content-Type-Options', 'nosniff');
        $response->headers->set('X-Frame-Options', 'SAMEORIGIN');
        $response->headers->set('Referrer-Policy', 'strict-origin-when-cross-origin');
        $response->headers->set('Permissions-Policy', 'camera=(), microphone=(), geolocation=(), payment=(self)');
        // same-origin-allow-popups: aman dipakai bareng popup pembayaran (mis. Midtrans) nanti
        $response->headers->set('Cross-Origin-Opener-Policy', 'same-origin-allow-popups');

        // HSTS cuma dikirim lewat HTTPS (kalau dikirim lewat HTTP, diabaikan browser)
        if ($request->isSecure()) {
            $response->headers->set('Strict-Transport-Security', 'max-age=31536000');
        }

        // CSP dilewati HANYA saat development dengan Vite dev server (file public/hot ada),
        // karena dev server memakai origin & websocket lain (http://[::1]:5173) yang akan diblokir.
        // Syarat environment 'local' mencegah CSP hilang di production kalau file hot ikut ter-deploy.
        $usingViteDevServer = app()->environment('local') && file_exists(public_path('hot'));

        if (! $usingViteDevServer) {
            // 'unsafe-inline' masih perlu karena view memakai <script>/onclick inline.
            // Selebihnya (gambar, font, fetch, form, iframe, <base>, <object>) dikunci ke origin sendiri.
            $response->headers->set('Content-Security-Policy', implode('; ', [
                "default-src 'self'",
                "script-src 'self' 'unsafe-inline'",
                "style-src 'self' 'unsafe-inline'",
                "img-src 'self' data: blob:",
                "font-src 'self' data:",
                "connect-src 'self'",
                "object-src 'none'",
                "base-uri 'self'",
                "form-action 'self'",
                "frame-ancestors 'self'",
            ]));
        }

        return $response;
    }
}