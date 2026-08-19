<?php

use App\Http\Controllers\Backend\BrandController;
use App\Http\Controllers\Backend\ColorController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::resource('colors', ColorController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('brands', BrandController::class)->except(['show']);
});

require __DIR__.'/settings.php';
