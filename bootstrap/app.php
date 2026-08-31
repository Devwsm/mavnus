<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;
use Illuminate\Http\Request;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__ . '/../routes/web.php',
        commands: __DIR__ . '/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware): void {
        //
        $middleware->alias([
            'cekLogin' => \App\Http\Middleware\cekLogin::class,
            'role' => \App\Http\Middleware\cekRole::class,
        ]);

        $middleware->web(append: [
            \App\Http\Middleware\TrackVisit::class,
            \App\Http\Middleware\AutoCancelExpiredOrders::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions): void {
        // Sebelumnya cuma cek prefix '/api/*' — padahal app ini gak punya
        // routes/api.php sama sekali (full Blade + fetch() biasa di bawah
        // web.php). Akibatnya, tiap kali ada error di endpoint AJAX (search,
        // cart, shipping/ongkir, dll), Laravel balikin HALAMAN HTML lengkap
        // (bukan JSON) sebagai response fetch() — makanya kalau dibuka
        // langsung di browser, yang muncul themed page bawaan Laravel/
        // Ignition (putih), bukan halaman error custom punya Mavnus (hitam).
        // Sekarang juga dicek expectsJson(), biar semua request AJAX yang
        // ngirim header Accept: application/json dibales JSON, apa pun
        // path-nya.
        $exceptions->shouldRenderJsonWhen(
            fn(Request $request, \Throwable $e) => $request->is('api/*') || $request->expectsJson(),
        );
    })->create();