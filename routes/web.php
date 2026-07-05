<?php

use App\Http\Controllers\homeController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Route::prefix('/')->group(function () {
    Route::get('/', [homeController::class, 'home'])->name('home');
    Route::get('/clothes', [homeController::class, 'clothes'])->name('clothes');
    Route::get('/accessoris', [homeController::class, 'accessoris'])->name('accessoris');
    Route::get('/albums', [homeController::class, 'albums'])->name('albums');
});