<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use Illuminate\Support\Facades\Route;

Route::prefix('v1')->name('api.v1.')->group(function () {
    Route::prefix('auth')->name('auth.')->group(function () {
        Route::post('register', [AuthController::class, 'register'])
            ->middleware('throttle:customer-auth')
            ->name('register');

        Route::post('login', [AuthController::class, 'login'])
            ->middleware('throttle:customer-auth')
            ->name('login');

        Route::post('logout', [AuthController::class, 'logout'])
            ->middleware('auth:sanctum')
            ->name('logout');
    });
});
