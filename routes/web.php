<?php

use App\Http\Controllers\dashboardController;
use App\Http\Controllers\homeController;
use App\Http\Controllers\loginController;
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
});

Route::prefix('/')->group(function () {
    Route::get('/', [homeController::class, 'home'])->name('home');
    Route::get('/clothes', [homeController::class, 'clothes'])->name('clothes');
    Route::get('/accessoris', [homeController::class, 'accessoris'])->name('accessoris');
    Route::get('/albums', [homeController::class, 'albums'])->name('albums');
});