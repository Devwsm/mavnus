<?php

use App\Http\Controllers\clothesController;
use App\Http\Controllers\dashboardController;
use App\Http\Controllers\homeController;
use App\Http\Controllers\loginController;
use App\Http\Controllers\searchController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('/')->group(function () {
    Route::get('/login', [loginController::class, 'login'])->name('login');
    Route::post('/login', [loginController::class, 'prosesLogin'])->name('login.proses')->middleware('throttle:5,1');
    Route::get('/logout', [loginController::class, 'logout'])->name('logout');
});

Route::prefix('/dashboard')->middleware('cekLogin')->group(function () {
    Route::get('/', [dashboardController::class, 'dashboard'])->name('dashboard');

    Route::prefix('/clothes')->middleware('cekLogin')->group(function () {
        Route::get('/', [clothesController::class, 'clothes'])->name('dashboard.clothes');
        Route::post('/store', [clothesController::class, 'store'])->name('clothes.store');
        Route::delete('/{product}', [clothesController::class, 'destroy'])->name('clothes.destroy');
        Route::put('/{product}', [clothesController::class, 'update'])->name('clothes.update');
    });
});

Route::prefix('/')->group(function () {
    Route::get('/', [homeController::class, 'home'])->name('home');
    Route::get('/search', [searchController::class, 'search'])->name('search');

    Route::prefix('/clothes')->middleware('cekLogin')->group(function () {
        Route::get('/', [homeController::class, 'clothes'])->name('clothes');
        Route::get('/{slug}', [clothesController::class, 'show'])->name('product_detail.clothes');
    });
    Route::get('/accessoris', [homeController::class, 'accessoris'])->name('accessoris');
    Route::get('/albums', [homeController::class, 'albums'])->name('albums');
});