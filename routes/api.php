<?php

use App\Http\Controllers\Api\V1\Auth\AuthController;
use App\Http\Controllers\Api\V1\BannerController;
use App\Http\Controllers\Api\V1\BlogCategoryController;
use App\Http\Controllers\Api\V1\BlogController;
use App\Http\Controllers\Api\V1\BrandController;
use App\Http\Controllers\Api\V1\CartItemController;
use App\Http\Controllers\Api\V1\CategoryController;
use App\Http\Controllers\Api\V1\ContactController;
use App\Http\Controllers\Api\V1\CouponController;
use App\Http\Controllers\Api\V1\CurrencyController;
use App\Http\Controllers\Api\V1\DeliveryZoneController;
use App\Http\Controllers\Api\V1\FaqController;
use App\Http\Controllers\Api\V1\FlashDealController;
use App\Http\Controllers\Api\V1\GiftCardController;
use App\Http\Controllers\Api\V1\HelpTopicController;
use App\Http\Controllers\Api\V1\JobOpeningController;
use App\Http\Controllers\Api\V1\NewsletterSubscriberController;
use App\Http\Controllers\Api\V1\OfflinePaymentMethodController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\PageController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ReviewController;
use App\Http\Controllers\Api\V1\ShippingAddressController;
use App\Http\Controllers\Api\V1\ShippingMethodController;
use App\Http\Controllers\Api\V1\SocialMediaController;
use App\Http\Controllers\Api\V1\StoreLocationController;
use App\Http\Controllers\Api\V1\SupportTicketController;
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

    Route::apiResource('banners', BannerController::class)->only(['index', 'show']);
    Route::apiResource('brands', BrandController::class)->only(['index', 'show']);
    Route::apiResource('categories', CategoryController::class)->only(['index', 'show']);
    Route::apiResource('products', ProductController::class)->only(['index', 'show']);
    Route::apiResource('flash-deals', FlashDealController::class)->only(['index', 'show']);
    Route::apiResource('blog-categories', BlogCategoryController::class)->only(['index', 'show']);
    Route::apiResource('blogs', BlogController::class)->only(['index', 'show']);
    Route::apiResource('testimonials', TestimonialController::class)->only(['index', 'show']);
    Route::apiResource('pages', PageController::class)->only(['index', 'show']);
    Route::apiResource('faqs', FaqController::class)->only(['index', 'show']);
    Route::apiResource('help-topics', HelpTopicController::class)->only(['index', 'show']);
    Route::apiResource('store-locations', StoreLocationController::class)->only(['index', 'show']);
    Route::apiResource('job-openings', JobOpeningController::class)->only(['index', 'show']);
    Route::apiResource('shipping-methods', ShippingMethodController::class)->only(['index', 'show']);
    Route::apiResource('delivery-zones', DeliveryZoneController::class)->only(['index', 'show']);
    Route::apiResource('offline-payment-methods', OfflinePaymentMethodController::class)->only(['index', 'show']);
    Route::apiResource('social-medias', SocialMediaController::class)->only(['index', 'show']);
    Route::apiResource('currencies', CurrencyController::class)->only(['index', 'show']);
    Route::apiResource('products.reviews', ReviewController::class)->only(['index']);

    Route::post('coupons/apply', [CouponController::class, 'apply'])
        ->middleware('throttle:coupon-apply')
        ->name('coupons.apply');

    Route::post('gift-cards/check', [GiftCardController::class, 'check'])
        ->middleware('throttle:gift-card-check')
        ->name('gift-cards.check');

    Route::post('contacts', [ContactController::class, 'store'])
        ->middleware('throttle:contact-store')
        ->name('contacts.store');

    Route::post('newsletter-subscribers', [NewsletterSubscriberController::class, 'store'])
        ->middleware('throttle:newsletter-subscribe')
        ->name('newsletter-subscribers.store');

    Route::middleware('auth:sanctum')->group(function () {
        Route::apiResource('wishlists', WishlistController::class)->only(['index', 'store', 'destroy']);

        Route::apiResource('cart-items', CartItemController::class)->only(['index', 'store', 'destroy']);

        Route::apiResource('products.reviews', ReviewController::class)->only(['store'])
            ->middleware('throttle:review-store');

        Route::apiResource('support-tickets', SupportTicketController::class)->only(['index', 'store', 'show']);

        Route::apiResource('shipping-addresses', ShippingAddressController::class)->only(['index', 'store', 'update', 'destroy']);

        Route::apiResource('orders', OrderController::class)->only(['index', 'show']);

        Route::apiResource('orders', OrderController::class)->only(['store'])
            ->middleware('throttle:order-store');
    });
});
