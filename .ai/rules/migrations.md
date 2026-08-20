---
paths:
  - 'app/Http/Controllers/Backend/**,database/migrations/**'
---

# Migrations

## Reference schema for new standalone-table admin features
`database/backend-sport-accessorie.sql` is a full reference schema dump (looks like a 6amMart/GetIR-style multi-vendor marketplace) for this app's original design. Before inventing fields for a new admin CRUD table, grep it for the matching `CREATE TABLE` block — it gives real column names/types to mirror instead of guessing.

Multiple sessions build these standalone-table admin CRUD features in parallel against the same repo. Before scaffolding a new one: check `app/Models/`, `grep "Route::resource" routes/web.php`, and `git status` for in-flight work from other sessions, and re-read shared files (routes/web.php, AppSidebar.vue) immediately before editing them since they change from under you mid-task. Already-built (as of 2026-08-20): colors, sizes, materials, brands, banners, tags, currencies, categories, help-topics, shipping-methods, search-functions, offline-payment-methods, social-medias, credentials, testimonials, faqs, coupons, newsletter-subscribers, tax-rates, warehouses, return-policies, suppliers, pages, store-locations, gift-cards, team-members, loyalty-tiers, job-openings, withdrawal-methods.
