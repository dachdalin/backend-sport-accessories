<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\BlogCategoryController;
use App\Http\Controllers\Api\V1\BlogController;
use App\Http\Controllers\Api\V1\BrandController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\CouponController;
use App\Http\Controllers\Api\V1\FlashDealController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\TestimonialController;
use App\Http\Controllers\Api\V1\WishlistController;
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

    Route::apiResource('brands', BrandController::class)->only(['index', 'show']);
    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
    Route::apiResource('products', ProductController::class)->only(['index', 'show']);
    Route::apiResource('flash-deals', FlashDealController::class)->only(['index', 'show']);
    Route::apiResource('blog-categories', BlogCategoryController::class)->only(['index', 'show']);
    Route::apiResource('blogs', BlogController::class)->only(['index', 'show']);
    Route::apiResource('testimonials', TestimonialController::class)->only(['index', 'show']);
    Route::apiResource('products.reviews', ReviewController::class)->only(['index']);

    Route::post('coupons/apply', [CouponController::class, 'apply'])
        ->middleware('throttle:coupon-apply')
        ->name('coupons.apply');

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('wishlists', WishlistController::class)->only(['index', 'store', 'destroy']);

        Route::apiResource('products.reviews', ReviewController::class)->only(['store'])
            ->middleware('throttle:review-store');
    });
});
