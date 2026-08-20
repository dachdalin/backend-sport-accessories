<?php

use App\Http\Controllers\Backend\BannerController;
use App\Http\Controllers\Backend\BrandController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\ColorController;
use App\Http\Controllers\Backend\CouponController;
use App\Http\Controllers\Backend\CurrencyController;
use App\Http\Controllers\Backend\FaqController;
use App\Http\Controllers\Backend\GiftCardController;
use App\Http\Controllers\Backend\HelpTopicController;
use App\Http\Controllers\Backend\JobOpeningController;
use App\Http\Controllers\Backend\LoyaltyTierController;
use App\Http\Controllers\Backend\MaterialController;
use App\Http\Controllers\Backend\NewsletterSubscriberController;
use App\Http\Controllers\Backend\OfflinePaymentMethodController;
use App\Http\Controllers\Backend\PageController;
use App\Http\Controllers\Backend\ReturnPolicyController;
use App\Http\Controllers\Backend\SearchFunctionController;
use App\Http\Controllers\Backend\ShippingMethodController;
use App\Http\Controllers\Backend\SizeController;
use App\Http\Controllers\Backend\SocialMediaController;
use App\Http\Controllers\Backend\SoftCredentialController;
use App\Http\Controllers\Backend\StoreLocationController;
use App\Http\Controllers\Backend\SupplierController;
use App\Http\Controllers\Backend\TagController;
use App\Http\Controllers\Backend\TaxRateController;
use App\Http\Controllers\Backend\TeamMemberController;
use App\Http\Controllers\Backend\TestimonialController;
use App\Http\Controllers\Backend\WarehouseController;
use App\Http\Controllers\Backend\WithdrawalMethodController;
use Illuminate\Support\Facades\Route;

Route::inertia('/', 'Welcome')->name('home');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::inertia('dashboard', 'Dashboard')->name('dashboard');

    Route::resource('colors', ColorController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('sizes', SizeController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('materials', MaterialController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('brands', BrandController::class)->except(['show']);
    Route::resource('banners', BannerController::class)->except(['show']);
    Route::resource('tags', TagController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('currencies', CurrencyController::class)->except(['show']);
    Route::resource('categories', CategoryController::class)->except(['show']);
    Route::resource('help-topics', HelpTopicController::class)->except(['show']);
    Route::resource('shipping-methods', ShippingMethodController::class)->except(['show']);
    Route::resource('search-functions', SearchFunctionController::class)->except(['show']);
    Route::resource('offline-payment-methods', OfflinePaymentMethodController::class)->except(['show']);
    Route::resource('social-medias', SocialMediaController::class)->except(['show']);
    Route::resource('credentials', SoftCredentialController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('testimonials', TestimonialController::class)->except(['show']);
    Route::resource('faqs', FaqController::class)->except(['show']);
    Route::resource('coupons', CouponController::class)->except(['show']);
    Route::resource('newsletter-subscribers', NewsletterSubscriberController::class)->only(['index', 'store', 'update', 'destroy']);
    Route::resource('tax-rates', TaxRateController::class)->except(['show']);
    Route::resource('warehouses', WarehouseController::class)->except(['show']);
    Route::resource('return-policies', ReturnPolicyController::class)->except(['show']);
    Route::resource('suppliers', SupplierController::class)->except(['show']);
    Route::resource('pages', PageController::class)->except(['show']);
    Route::resource('store-locations', StoreLocationController::class)->except(['show']);
    Route::resource('gift-cards', GiftCardController::class)->except(['show']);
    Route::resource('team-members', TeamMemberController::class)->except(['show']);
    Route::resource('loyalty-tiers', LoyaltyTierController::class)->except(['show']);
    Route::resource('job-openings', JobOpeningController::class)->except(['show']);
    Route::resource('withdrawal-methods', WithdrawalMethodController::class)->except(['show']);
});

require __DIR__.'/settings.php';
