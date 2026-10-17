<?php

namespace App\Http\Middleware;

use Carbon\Carbon;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\App;
use Symfony\Component\HttpFoundation\Response;

/**
 * Menentukan bahasa tampilan untuk setiap request.
 * Urutan: pilihan di session -> cookie 'locale' (tersimpan 1 tahun) -> bahasa default.
 * Pilihan diubah lewat LocaleController (route /lang/{locale}).
 */
class SetLocale
{
    public function handle(Request $request, Closure $next): Response
    {
        $supported = array_keys(config('mavnus.locales'));

        $locale = $request->session()->get('locale');

        if (! is_string($locale) || ! in_array($locale, $supported, true)) {
            $cookie = $request->cookie('locale');
            $locale = is_string($cookie) && in_array($cookie, $supported, true)
                ? $cookie
                : config('mavnus.default_locale');
        }

        App::setLocale($locale);
        // Supaya tanggal ($order->created_at->translatedFormat(...)) ikut berganti bahasa
        Carbon::setLocale($locale);

        return $next($request);
    }
}