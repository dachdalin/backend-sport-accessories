<?php

use App\Http\Controllers\Backend\AnalyticScriptController;
use App\Http\Controllers\Backend\ApiDocumentationController;
use App\Http\Controllers\Backend\AttributeController;
use App\Http\Controllers\Backend\BannerController;
use App\Http\Controllers\Backend\BlogCategoryController;
use App\Http\Controllers\Backend\BlogController;
use App\Http\Controllers\Backend\BrandController;
use App\Http\Controllers\Backend\BusinessSettingController;
use App\Http\Controllers\Backend\CategoryController;
use App\Http\Controllers\Backend\ColorController;
use App\Http\Controllers\Backend\ContactController;
use App\Http\Controllers\Backend\CouponController;
use App\Http\Controllers\Backend\CurrencyController;
use App\Http\Controllers\Backend\CustomerController;
use App\Http\Controllers\Backend\DashboardController;
use App\Http\Controllers\Backend\DealOfTheDayController;
use App\Http\Controllers\Backend\DeliveryZoneController;
use App\Http\Controllers\Backend\EmailTemplateController;
use App\Http\Controllers\Backend\FaqController;
use App\Http\Controllers\Backend\FeatureDealController;
use App\Http\Controllers\Backend\FlashDealController;
use App\Http\Controllers\Backend\GiftCardController;
use App\Http\Controllers\Backend\HelpTopicController;
use App\Http\Controllers\Backend\JobOpeningController;
use App\Http\Controllers\Backend\LoyaltyTierController;
use App\Http\Controllers\Backend\MaterialController;
use App\Http\Controllers\Backend\MessageController;
use App\Http\Controllers\Backend\MostDemandedController;
use App\Http\Controllers\Backend\NewsletterSubscriberController;
use App\Http\Controllers\Backend\NotificationController;
use App\Http\Controllers\Backend\OfflinePaymentMethodController;
use App\Http\Controllers\Backend\OrderController;
use App\Http\Controllers\Backend\PageController;
use App\Http\Controllers\Backend\ProductController;
use App\Http\Controllers\Backend\ProductImageController;
use App\Http\Controllers\Backend\RefundRequestController;
use App\Http\Controllers\Backend\ReturnPolicyController;
use App\Http\Controllers\Backend\ReviewController;
use App\Http\Controllers\Backend\RoleController;
use App\Http\Controllers\Backend\SearchFunctionController;
use App\Http\Controllers\Backend\ShippingAddressController;
use App\Http\Controllers\Backend\ShippingMethodController;
use App\Http\Controllers\Backend\SizeController;
use App\Http\Controllers\Backend\SocialMediaController;
use App\Http\Controllers\Backend\SoftCredentialController;
use App\Http\Controllers\Backend\StockClearanceSetupController;
use App\Http\Controllers\Backend\StoreLocationController;
use App\Http\Controllers\Backend\SupplierController;
use App\Http\Controllers\Backend\SupportTicketController;
use App\Http\Controllers\Backend\TagController;
use App\Http\Controllers\Backend\TaxRateController;
use App\Http\Controllers\Backend\TeamMemberController;
use App\Http\Controllers\Backend\TestimonialController;
use App\Http\Controllers\Backend\UserController;
use App\Http\Controllers\Backend\WarehouseController;
use App\Http\Controllers\Backend\WishlistController;
use Illuminate\Routing\PendingResourceRegistration;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/login')->name('home');

/**
 * Gate a modal-CRUD resource (index/store/update/destroy only, no dedicated
 * create/edit form routes) behind its view/create/edit/delete permissions.
 */
$gateCrud = function (PendingResourceRegistration $resource, string $permission): PendingResourceRegistration {
    return $resource
        ->middlewareFor('index', "permission:view {$permission}")
        ->middlewareFor('store', "permission:create {$permission}")
        ->middlewareFor('update', "permission:edit {$permission}")
        ->middlewareFor('destroy', "permission:delete {$permission}");
};

/**
 * Gate a full CRUD resource (with dedicated create/edit form pages, i.e. all
 * actions except show) behind its view/create/edit/delete permissions.
 */
$gateCrudWithForms = function (PendingResourceRegistration $resource, string $permission): PendingResourceRegistration {
    return $resource
        ->middlewareFor('index', "permission:view {$permission}")
        ->middlewareFor(['create', 'store'], "permission:create {$permission}")
        ->middlewareFor(['edit', 'update'], "permission:edit {$permission}")
        ->middlewareFor('destroy', "permission:delete {$permission}");
};

Route::middleware(['auth', 'verified'])->group(function () use ($gateCrud, $gateCrudWithForms) {
    Route::get('dashboard', [DashboardController::class, 'index'])
        ->middleware('permission:view dashboard')
        ->name('dashboard');

    $gateCrud(Route::resource('attributes', AttributeController::class)->only(['index', 'store', 'update', 'destroy']), 'attributes');
    $gateCrud(Route::resource('colors', ColorController::class)->only(['index', 'store', 'update', 'destroy']), 'colors');
    $gateCrud(Route::resource('sizes', SizeController::class)->only(['index', 'store', 'update', 'destroy']), 'sizes');
    $gateCrud(Route::resource('materials', MaterialController::class)->only(['index', 'store', 'update', 'destroy']), 'materials');
    $gateCrudWithForms(Route::resource('brands', BrandController::class)->except(['show']), 'brands');
    $gateCrudWithForms(Route::resource('banners', BannerController::class)->except(['show']), 'banners');
    $gateCrud(Route::resource('tags', TagController::class)->only(['index', 'store', 'update', 'destroy']), 'tags');
    $gateCrudWithForms(Route::resource('currencies', CurrencyController::class)->except(['show']), 'currencies');
    $gateCrudWithForms(Route::resource('customers', CustomerController::class)->except(['show']), 'customers');
    $gateCrudWithForms(Route::resource('categories', CategoryController::class)->except(['show']), 'categories');
    $gateCrud(Route::resource('blog-categories', BlogCategoryController::class)->only(['index', 'store', 'update', 'destroy']), 'blog categories');
    $gateCrudWithForms(Route::resource('blogs', BlogController::class)->except(['show']), 'blogs');
    $gateCrudWithForms(Route::resource('help-topics', HelpTopicController::class)->except(['show']), 'help topics');
    $gateCrudWithForms(Route::resource('shipping-methods', ShippingMethodController::class)->except(['show']), 'shipping methods');
    $gateCrudWithForms(Route::resource('shipping-addresses', ShippingAddressController::class)->except(['show']), 'shipping addresses');
    $gateCrudWithForms(Route::resource('search-functions', SearchFunctionController::class)->except(['show']), 'search functions');
    $gateCrudWithForms(Route::resource('offline-payment-methods', OfflinePaymentMethodController::class)->except(['show']), 'offline payment methods');
    $gateCrudWithForms(Route::resource('social-medias', SocialMediaController::class)->except(['show']), 'social medias');
    $gateCrud(Route::resource('credentials', SoftCredentialController::class)->only(['index', 'store', 'update', 'destroy']), 'credentials');
    $gateCrudWithForms(Route::resource('testimonials', TestimonialController::class)->except(['show']), 'testimonials');
    $gateCrudWithForms(Route::resource('faqs', FaqController::class)->except(['show']), 'faqs');
    $gateCrudWithForms(Route::resource('coupons', CouponController::class)->except(['show']), 'coupons');
    $gateCrud(Route::resource('newsletter-subscribers', NewsletterSubscriberController::class)->only(['index', 'store', 'update', 'destroy']), 'newsletter subscribers');
    $gateCrudWithForms(Route::resource('tax-rates', TaxRateController::class)->except(['show']), 'tax rates');
    $gateCrudWithForms(Route::resource('warehouses', WarehouseController::class)->except(['show']), 'warehouses');
    $gateCrudWithForms(Route::resource('return-policies', ReturnPolicyController::class)->except(['show']), 'return policies');
    $gateCrudWithForms(Route::resource('suppliers', SupplierController::class)->except(['show']), 'suppliers');
    $gateCrudWithForms(Route::resource('pages', PageController::class)->except(['show']), 'pages');
    $gateCrudWithForms(Route::resource('store-locations', StoreLocationController::class)->except(['show']), 'store locations');
    $gateCrudWithForms(Route::resource('gift-cards', GiftCardController::class)->except(['show']), 'gift cards');
    $gateCrudWithForms(Route::resource('team-members', TeamMemberController::class)->except(['show']), 'team members');
    $gateCrudWithForms(Route::resource('loyalty-tiers', LoyaltyTierController::class)->except(['show']), 'loyalty tiers');
    $gateCrudWithForms(Route::resource('job-openings', JobOpeningController::class)->except(['show']), 'job openings');
    $gateCrudWithForms(Route::resource('analytic-scripts', AnalyticScriptController::class)->except(['show']), 'analytic scripts');
    $gateCrudWithForms(Route::resource('delivery-zones', DeliveryZoneController::class)->except(['show']), 'delivery zones');
    $gateCrudWithForms(Route::resource('products', ProductController::class)->except(['show']), 'products');

    Route::resource('products.images', ProductImageController::class)
        ->shallow()
        ->only(['store', 'destroy'])
        ->middlewareFor(['store', 'destroy'], 'permission:edit products');

    $gateCrudWithForms(Route::resource('orders', OrderController::class), 'orders')
        ->middlewareFor('show', 'permission:view orders');
    $gateCrudWithForms(Route::resource('reviews', ReviewController::class)->except(['show']), 'reviews');
    $gateCrud(Route::resource('wishlists', WishlistController::class)->only(['index', 'store', 'update', 'destroy']), 'wishlists');
    $gateCrudWithForms(Route::resource('contacts', ContactController::class)->except(['show']), 'contacts');
    $gateCrudWithForms(Route::resource('support-tickets', SupportTicketController::class)->except(['show']), 'support tickets');
    $gateCrudWithForms(Route::resource('email-templates', EmailTemplateController::class)->except(['show']), 'email templates');
    $gateCrudWithForms(Route::resource('flash-deals', FlashDealController::class)->except(['show']), 'flash deals');
    $gateCrudWithForms(Route::resource('feature-deals', FeatureDealController::class)->except(['show']), 'feature deals');
    $gateCrudWithForms(Route::resource('stock-clearance-setups', StockClearanceSetupController::class)->except(['show']), 'stock clearance setups');
    $gateCrudWithForms(Route::resource('deal-of-the-days', DealOfTheDayController::class)->except(['show']), 'deal of the days');
    $gateCrudWithForms(Route::resource('most-demandeds', MostDemandedController::class)->except(['show']), 'most demandeds');
    $gateCrudWithForms(Route::resource('refund-requests', RefundRequestController::class)->except(['show']), 'refund requests');
    $gateCrudWithForms(Route::resource('roles', RoleController::class)->except(['show']), 'roles');

    Route::resource('users', UserController::class)
        ->middlewareFor(['index', 'show'], 'permission:view users')
        ->middlewareFor(['create', 'store'], 'permission:create users')
        ->middlewareFor(['edit', 'update'], 'permission:edit users')
        ->middlewareFor('destroy', 'permission:delete users');

    Route::resource('messages', MessageController::class)
        ->only(['index', 'store'])
        ->middlewareFor('index', 'permission:view messages')
        ->middlewareFor('store', 'permission:create messages');

    Route::get('business-settings', [BusinessSettingController::class, 'edit'])
        ->middleware('permission:view business settings')
        ->name('business-settings.edit');
    Route::patch('business-settings', [BusinessSettingController::class, 'update'])
        ->middleware('permission:edit business settings')
        ->name('business-settings.update');

    Route::post('notifications/read-all', [NotificationController::class, 'readAll'])->name('notifications.read-all');
    Route::post('notifications/{notification}/read', [NotificationController::class, 'read'])->name('notifications.read');

    Route::get('api-documentation', [ApiDocumentationController::class, 'index'])
        ->middleware('permission:view api documentation')
        ->name('api-documentation.index');
});

require __DIR__.'/settings.php';
