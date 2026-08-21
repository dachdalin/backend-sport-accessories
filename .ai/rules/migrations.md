---
paths:
  - 'app/Http/Controllers/Backend/**,database/migrations/**'
---

# Migrations

## Reference schema for new standalone-table admin features
`database/backend-sport-accessorie.sql` is a full reference schema dump (looks like a 6amMart/GetIR-style multi-vendor marketplace) for this app's original design. Before inventing fields for a new admin CRUD table, grep it for the matching `CREATE TABLE` block — it gives real column names/types to mirror instead of guessing.

Multiple sessions build these standalone-table admin CRUD features in parallel against the same repo. Before scaffolding a new one: check `app/Models/`, `grep "Route::resource" routes/web.php`, and `git status` for in-flight work from other sessions, and re-read shared files (routes/web.php, AppSidebar.vue) immediately before editing them since they change from under you mid-task. Already-built (as of 2026-08-20): colors, sizes, materials, brands, banners, tags, currencies, categories, help-topics, shipping-methods, search-functions, offline-payment-methods, social-medias, credentials, testimonials, faqs, coupons, newsletter-subscribers, tax-rates, warehouses, return-policies, suppliers, pages, store-locations, gift-cards, team-members, loyalty-tiers, job-openings, withdrawal-methods.

## This is a single-vendor store, not the marketplace the reference schema implies
Correction to the reference-schema note: `database/backend-sport-accessorie.sql` is a multi-vendor marketplace dump (6amMart/GetIR-style — has `shops`, `shop_followers`, per-seller `withdrawal_methods`/`shipping_types`, etc.). This app is single-vendor. Use the dump for column/field naming ideas on generic tables, but skip or adapt anything modeling multiple sellers (seller_id, shop_id, per-vendor payouts) — there is one storefront, not a marketplace. `withdrawal_methods` (built 2026-08-20) leans marketplace-flavored; treat as vendor/supplier payout config for the single business, not per-seller payouts, if revisited.

## This is a single-vendor store, not the marketplace the reference schema implies
`database/backend-sport-accessorie.sql` is a multi-vendor marketplace dump (6amMart/GetIR-style — has `shops`, `shop_followers`, per-seller `withdrawal_methods`/`shipping_types`, etc.). This app is single-vendor: one storefront, not a marketplace. Use the dump for column-naming ideas on generic tables, but skip or adapt anything modeling multiple sellers (seller_id, shop_id, per-vendor payouts).

`withdrawal-methods` (vendor payout methods) was built 2026-08-20 and then removed the same day once flagged as marketplace-scoped — don't rebuild it. Already-built and current (check `grep "Route::resource" routes/web.php` for the live list before adding another): colors, sizes, materials, brands, banners, tags, currencies, categories, help-topics, shipping-methods, search-functions, offline-payment-methods, social-medias, credentials, testimonials, faqs, coupons, newsletter-subscribers, tax-rates, warehouses, return-policies, suppliers, pages, store-locations, gift-cards, team-members, loyalty-tiers, job-openings.

## Orders CRUD already built (2026-08-21)
Full order management built: orders + order_items tables, App\Models\Order/OrderItem, App\Http\Controllers\Backend\OrderController (except show), App\Actions\Orders\{Create,Update,Delete}OrderAction, App\Services\OrderService, App\Enums\OrderStatus/OrderPaymentStatus (discount_type reuses existing App\Enums\TaxType). No customer/user FK — orders store guest-style contact fields (customer_name/email/phone) directly since this app has no customer accounts, only the admin User model. order_items snapshot product_name and unit_price at order time and belong to Product via nullable nullOnDelete FK. Frontend: resources/js/pages/orders/{Index,Create,Edit}.vue with a dynamic add/remove line-item repeater (reactive array + array-indexed input names, not useForm). Don't rebuild — extend the existing files instead.
