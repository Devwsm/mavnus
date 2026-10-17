<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    // Ganti bahasa lalu kembali ke halaman sebelumnya (kalau masih satu domain), selain itu ke beranda.
    public function switch(Request $request, string $locale): RedirectResponse
    {
        abort_unless(array_key_exists($locale, config('mavnus.locales')), 404);

        $request->session()->put('locale', $locale);

        return $this->backOrHome($request)
            ->withCookie(cookie('locale', $locale, 60 * 24 * 365));
    }

    private function backOrHome(Request $request): RedirectResponse
    {
        $previous = (string) $request->headers->get('referer');
        $host = parse_url($previous, PHP_URL_HOST);
        $path = (string) parse_url($previous, PHP_URL_PATH);

        // Hanya terima referer dari domain sendiri (cegah open redirect) dan bukan dari /lang/* lagi
        if ($previous !== '' && $host === $request->getHost() && ! str_starts_with($path, '/lang/')) {
            return redirect()->to($previous);
        }

        return redirect()->route('home');
    }
}