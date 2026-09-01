<?php

use App\Http\Controllers\accountController;
use App\Http\Controllers\authController;
use App\Http\Controllers\cartController;
use App\Http\Controllers\productController;
use App\Http\Controllers\dashboardController;
use App\Http\Controllers\homeController;
use App\Http\Controllers\importExportController;
use App\Http\Controllers\loginController;
use App\Http\Controllers\orderController;
use App\Http\Controllers\searchController;
use App\Http\Controllers\ShippingController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\visitorController;
use Illuminate\Support\Facades\Route;

Route::prefix('/')->group(function () {
    // Login & daftar CUSTOMER - pakai Laravel Auth bawaan (tabel users)
    Route::get('/login', [loginController::class, 'login'])->name('login');
    Route::post('/login', [authController::class, 'processLogin'])->name('login.proses')->middleware('throttle:5,1,login');

    Route::get('/register', [authController::class, 'register'])->name('register');
    Route::post('/register', [authController::class, 'processRegister'])->name('register.proses')->middleware('throttle:5,1,register');

    // Login pakai Google (customer) - lewat Laravel Socialite
    Route::get('/login/google', [authController::class, 'redirectToGoogle'])->name('login.google');
    Route::get('/login/google/callback', [authController::class, 'handleGoogleCallback'])->name('login.google.callback');

    Route::get('/logout', [authController::class, 'logout'])->name('logout');

    // Halaman akun customer - kalau belum login otomatis dilempar ke /login,
    // abis berhasil login balik lagi ke sini (bawaan Laravel, gak perlu logic tambahan)
    Route::get('/account', [accountController::class, 'index'])->name('account')->middleware('auth');
    Route::get('/account/edit', [accountController::class, 'edit'])->name('account.edit')->middleware('auth');
    Route::post('/account', [accountController::class, 'update'])->name('account.update')->middleware(['auth', 'throttle:10,1,account-update']);
    Route::delete('/account', [accountController::class, 'destroy'])->name('account.destroy')->middleware(['auth', 'throttle:5,1,account-destroy']);
    Route::get('/account/orders', [accountController::class, 'orders'])->name('account.orders')->middleware('auth');
    Route::get('/account/orders/{order}', [accountController::class, 'orderDetail'])->name('account.orders.show')->middleware('auth');

    // Login STAFF/crew - fungsional, otentikasi ke tabel accounts. Gak dilink di halaman customer.
    Route::get('/crew-portal', [loginController::class, 'crewLogin'])->name('crew.login');
    Route::post('/crew-portal', [loginController::class, 'prosesLogin'])->name('crew.login.proses')->middleware('throttle:5,1,crew-login');
    Route::get('/crew-portal/logout', [loginController::class, 'logout'])->name('crew.logout');
});

Route::prefix('/dashboard')->middleware('cekLogin')->group(function () {
    // Landing gak dikunci per role — controller yang milih view mana
    // yang cocok buat role staff yang login (lihat dashboardController).
    Route::get('/', [dashboardController::class, 'dashboard'])->name('dashboard');

    // Pesanan: Owner + Staff Pesanan
    Route::prefix('/orders')->middleware('role:owner,staff_pesanan')->group(function () {
        Route::get('/', [orderController::class, 'dashboardIndex'])->name('dashboard.orders');
        Route::get('/{order}', [orderController::class, 'dashboardShow'])->name('dashboard.orders.show');
        Route::patch('/{order}/status', [orderController::class, 'updateStatus'])->name('dashboard.orders.updateStatus');
    });

    // Produk (Clothes & Accessories digabung jadi 1 halaman): Owner + Admin Produk
    Route::prefix('/produk')->middleware('role:owner,admin_produk')->group(function () {
        Route::get('/', [productController::class, 'produk'])->name('dashboard.produk');
        Route::post('/store', [productController::class, 'store'])->name('produk.store');
        Route::delete('/{product}', [productController::class, 'destroy'])->name('produk.destroy');
        Route::put('/{product}', [productController::class, 'update'])->name('produk.update');
    });

    // Statistik pengunjung: Owner only
    Route::prefix('/visitors')->middleware('role:owner')->group(function () {
        Route::get('/', [visitorController::class, 'dashboardIndex'])->name('dashboard.visitors');
        Route::get('/pages', [visitorController::class, 'pages'])->name('dashboard.visitors.pages');
    });

    Route::prefix('/import-export')->group(function () {
        // Halaman index-nya sendiri dibuka buat 3 role (isinya beda-beda
        // tombol tergantung role — diatur di view). Yang benar-benar
        // dikunci ketat adalah tiap endpoint export di bawah ini.
        Route::get('/', [importExportController::class, 'index'])
            ->name('dashboard.import-export')
            ->middleware('role:owner,admin_produk,staff_pesanan');

        // Invoice & data pesanan: Owner + Staff Pesanan
        Route::middleware('role:owner,staff_pesanan')->group(function () {
            Route::get('/orders/export', [importExportController::class, 'exportOrders'])->name('export.orders');
            Route::get('/orders/invoice/preview', [importExportController::class, 'ordersInvoicePreview'])->name('export.orders.preview');
            Route::get('/orders/invoice/pdf', [importExportController::class, 'exportOrdersPdf'])->name('export.orders.pdf');
        });

        // Data produk (SQL): Owner + Admin Produk
        Route::middleware('role:owner,admin_produk')->group(function () {
            Route::get('/products/export-sql', [importExportController::class, 'exportProductsSql'])->name('export.products.sql');
        });

        // Backup mentah (SQL pesanan, full database, storage): Owner only —
        // ini yang paling sensitif karena isinya semua data customer.
        Route::middleware('role:owner')->group(function () {
            Route::get('/orders/export-sql', [importExportController::class, 'exportOrdersSql'])->name('export.orders.sql');
            Route::get('/database/export', [importExportController::class, 'exportDatabase'])->name('export.database');
            Route::get('/storage/export', [importExportController::class, 'exportStorage'])->name('export.storage');
        });
    });
});

Route::prefix('/')->group(function () {
    Route::get('/', [homeController::class, 'home'])->name('home');

    // Endpoint pencarian live suggestion - rawan disalahgunakan buat spam request
    Route::get('/search', [searchController::class, 'search'])->name('search')->middleware('throttle:30,1,search');

    Route::prefix('/shipping')->group(function () {
        Route::get('/search', [ShippingController::class, 'searchDestination'])->name('shipping.search')->middleware('throttle:30,1,shipping-search');
        Route::post('/cost', [ShippingController::class, 'calculateCost'])->name('shipping.cost')->middleware('throttle:20,1,shipping-cost');
    });

    Route::prefix('/cart')->group(function () {
        Route::get('/', [cartController::class, 'index'])->name('cart.index')->middleware('throttle:60,1,cart-index');
        Route::post('/add', [cartController::class, 'add'])->name('cart.add')->middleware('throttle:20,1,cart-add');
        Route::patch('/{cartItem}', [cartController::class, 'update'])->name('cart.update')->middleware('throttle:30,1,cart-update');
        Route::delete('/{cartItem}', [cartController::class, 'destroy'])->name('cart.destroy')->middleware('throttle:30,1,cart-destroy');
    });

    Route::prefix('/order')->group(function () {
        Route::get('/checkout', [orderController::class, 'checkout'])->name('order.checkout');
        // Pembuatan order - paling sensitif, dibatasi ketat biar ga dipakai spam/bot checkout
        Route::post('/', [orderController::class, 'store'])->name('order.store')->middleware('throttle:5,1,checkout');
        Route::get('/{order}/success', [orderController::class, 'success'])->name('order.success');
    });

    Route::prefix('/clothes')->group(function () {
        Route::get('/', [homeController::class, 'clothes'])->name('clothes');
        Route::get('/{slug}', [productController::class, 'show'])->name('product_detail.clothes');
    });

    Route::prefix('/accessoris')->group(function () {
        Route::get('/', [homeController::class, 'accessoris'])->name('accessoris');
        Route::get('/{slug}', [productController::class, 'show'])->name('product_detail.accessories');
    });

    Route::get('/info', [homeController::class, 'footerInfo'])->name('footer');

    Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');
});