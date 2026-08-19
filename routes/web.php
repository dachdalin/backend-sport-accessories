<?php

use App\Http\Controllers\Backend\BrandController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\ColorController;
use App\Http\Controllers\Backend\CurrencyController;
use App\Http\Controllers\Backend\HelpTopicController;
use App\Http\Controllers\Backend\SearchFunctionController;
use App\Http\Controllers\Backend\ShippingMethodController;
use App\Http\Controllers\Backend\TagController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::resource('colors', ColorController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('brands', BrandController::class)->except(['show']);
    Route::resource('tags', TagController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('currencies', CurrencyController::class)->except(['show']);
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('help-topics', HelpTopicController::class)->except(['show']);
    Route::resource('shipping-methods', ShippingMethodController::class)->except(['show']);
    Route::resource('search-functions', SearchFunctionController::class)->except(['show']);
});

require __DIR__.'/settings.php';
