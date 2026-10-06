<?php

namespace App\Providers;

use App\Models\CartItem;
use App\Support\CartSession;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\View;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Kalau APP_URL sudah https, paksa semua URL buatan Laravel (route(), asset(), redirect)
        // jadi https. Tanpa ini, di balik proxy/hosting tertentu link & aset bisa keluar sebagai
        // http dan browser memblokirnya sebagai mixed content.
        if (str_starts_with((string) config('app.url'), 'https://')) {
            URL::forceHttps();
        }

        // Jumlah isi keranjang dirender langsung di HTML (badge navbar). Dulu dihitung lewat
        // request fetch() tambahan di SETIAP halaman yang dibuka.
        View::composer('components.cart', function ($view) {
            $key = CartSession::peek();

            $view->with('cartCount', $key
                ? (int) CartItem::where('session_id', $key)->sum('quantity')
                : 0);
        });
    }
}