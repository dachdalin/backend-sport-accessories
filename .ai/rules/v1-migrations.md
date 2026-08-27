---
paths:
  - 'app/Http/Controllers/Api/V1/TrendingController.php,app/Services/TrendingProductService.php,app/Actions/ProductSearches/**,app/Http/Controllers/Api/V1/ProductController.php,database/migrations/**'
---

# V1 Migrations

## Trending products API built (2026-08-27) — search-driven weekly ranking
Built GET api/v1/trending — ranks active products by search volume over the last 7 days, each with growth vs. the 7 days before that (matches a "Trending Now / Popular in Cambodia" storefront widget mockup: rank, product, search count, +/-% growth). Single-vendor store, so no country/region dimension was added — the whole catalog IS the Cambodia-market catalog, "Popular in Cambodia" is just storefront copy, not a filter.

Data source: new `product_searches` table (product_id FK cascadeOnDelete, created_at only — `const UPDATED_AT = null` on App\Models\ProductSearch, a search event never changes). Rows are logged as a side effect of App\Http\Controllers\Api\V1\ProductController::index — whenever `search` is filled, every product on that results page is logged via App\Actions\ProductSearches\LogProductSearchAction (bulk insert). Browsing without a `search` term logs nothing. This was a deliberate choice over logging on product show()/view — "most searched" ties directly to the existing `search` query param already on that endpoint, and only page 1 fields get logged per call (no cross-page loop), so counts approximate rather than exhaustively track every impression.

App\Services\TrendingProductService::trending(int $limit = 10) does the ranking: counts this-week vs last-week per product_id (two grouped queries, no raw SQL — kept portable between MySQL/SQLite same as [[messages-migrations]]'s MessageService), over-fetches `$limit * 2` candidate IDs before filtering to active products only (so a since-deactivated product doesn't leave a gap in the top N). `growth_percent` is `null` (not 0 or 100) when there's no prior-week baseline to compute a percentage from — a product with searches this week but none last week is "new," not "+100%"; the frontend should render that as a "New" badge rather than a number.

Controller returns a bespoke `{data: [...]}` JsonResponse (not a Resource class) wrapping ProductResource per entry — follows the same pattern as CouponController::apply/GiftCardController::check for composite non-CRUD shapes, not the apiResource convention. Route is a bare top-level `GET api/v1/trending` (not nested under `products/`) specifically to avoid colliding with `products/{product}` route-model-binding order. Don't rebuild — extend the existing files instead.

Gotcha also fixed in App\Services\ApiDocumentationService (used by the admin API-docs page, see [[api-documentation]]): its route-name-to-group-key derivation assumed every `api.v1.*` route name has a resource segment plus a trailing action segment, and unconditionally popped the last segment. A bare single-segment name like `trending` (no `.index`/`.show` suffix) had its only segment popped, leaving an empty resource key and no group — the fallback title-casing silently produced `""`. Fixed to only pop when there's more than one segment. Covered by a regression test in tests/Unit/Services/ApiDocumentationServiceTest.php. Watch for this again if a future endpoint gets a bare route name.
