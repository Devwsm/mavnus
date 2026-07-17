<?php

use App\Http\Controllers\cartController;
use App\Http\Controllers\clothesController;
use App\Http\Controllers\dashboardController;
use App\Http\Controllers\homeController;
use App\Http\Controllers\importExportController;
use App\Http\Controllers\loginController;
use App\Http\Controllers\orderController;
use App\Http\Controllers\searchController;
use Illuminate\Support\Facades\Route;

Route::prefix('/')->group(function () {
    Route::get('/login', [loginController::class, 'login'])->name('login');
    Route::post('/login', [loginController::class, 'prosesLogin'])->name('login.proses')->middleware('throttle:5,1');
    Route::get('/logout', [loginController::class, 'logout'])->name('logout');
});

Route::prefix('/dashboard')->middleware('cekLogin')->group(function () {
    Route::get('/', [dashboardController::class, 'dashboard'])->name('dashboard');

    Route::prefix('/orders')->group(function () {
        Route::get('/', [orderController::class, 'dashboardIndex'])->name('dashboard.orders');
        Route::get('/{order}', [orderController::class, 'dashboardShow'])->name('dashboard.orders.show');
        Route::patch('/{order}/status', [orderController::class, 'updateStatus'])->name('dashboard.orders.updateStatus');
    });

    Route::prefix('/clothes')->group(function () {
        Route::get('/', [clothesController::class, 'clothes'])->name('dashboard.clothes');
        Route::post('/store', [clothesController::class, 'store'])->name('clothes.store');
        Route::delete('/{product}', [clothesController::class, 'destroy'])->name('clothes.destroy');
        Route::put('/{product}', [clothesController::class, 'update'])->name('clothes.update');
    });

    Route::prefix('/import-export')->middleware('cekLogin')->group(function () {
        Route::get('/', [importExportController::class, 'index'])->name('dashboard.import-export');
        
        Route::get('/orders/export', [importExportController::class, 'exportOrders'])->name('export.orders');
        Route::get('/orders/invoice/preview', [importExportController::class, 'ordersInvoicePreview'])->name('export.orders.preview');
        Route::get('/orders/invoice/pdf', [importExportController::class, 'exportOrdersPdf'])->name('export.orders.pdf');
        
        Route::get('/products/export-sql', [importExportController::class, 'exportProductsSql'])->name('export.products.sql');
        Route::get('/orders/export-sql', [importExportController::class, 'exportOrdersSql'])->name('export.orders.sql');
        
        Route::get('/database/export', [importExportController::class, 'exportDatabase'])->name('export.database');
        Route::get('/storage/export', [importExportController::class, 'exportStorage'])->name('export.storage');
    });
});

Route::prefix('/')->group(function () {
    Route::get('/', [homeController::class, 'home'])->name('home');
    Route::get('/search', [searchController::class, 'search'])->name('search');

    Route::prefix('/cart')->group(function () {
        Route::get('/', [cartController::class, 'index'])->name('cart.index');
        Route::post('/add', [cartController::class, 'add'])->name('cart.add');
        Route::patch('/{cartItem}', [cartController::class, 'update'])->name('cart.update');
        Route::delete('/{cartItem}', [cartController::class, 'destroy'])->name('cart.destroy');
    });

    Route::prefix('/order')->group(function () {
        Route::get('/checkout', [orderController::class, 'checkout'])->name('order.checkout');
        Route::post('/', [orderController::class, 'store'])->name('order.store');
        Route::get('/{order}/success', [orderController::class, 'success'])->name('order.success');
    });

    Route::prefix('/clothes')->group(function () {
        Route::get('/', [homeController::class, 'clothes'])->name('clothes');
        Route::get('/{slug}', [clothesController::class, 'show'])->name('product_detail.clothes');
    });

    Route::get('/accessoris', [homeController::class, 'accessoris'])->name('accessoris');
    Route::get('/albums', [homeController::class, 'albums'])->name('albums');

    Route::get('/info', [homeController::class, 'footerInfo'])->name('footer');
});