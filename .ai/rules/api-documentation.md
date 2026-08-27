---
paths:
  - 'app/Http/Controllers/Backend/ApiDocumentationController.php,app/Services/ApiDocumentationService.php,resources/js/pages/api-documentation/**'
---

# Api Documentation

## API documentation page built (2026-08-27) — live docs + test console for api/v1
Built an admin-only page documenting and letting staff live-test the customer-facing REST API (routes/api.php, api/v1/*) — no Scribe/L5-Swagger/OpenAPI package added (none installed, and CLAUDE.md forbids new deps without approval).

App\Services\ApiDocumentationService::groups() is the source of truth and derives almost everything from the live app rather than hand-duplicated docs, so it can't drift:
- Walks Route::getRoutes(), keeps only routes named `api.v1.*`.
- method/uri/path-params/auth ('sanctum' if `auth:sanctum` middleware present)/throttle name: read straight off the Route object.
- "used to" purpose text + extra usage notes ("instructions"): reflected from each controller method's docblock via ReflectionMethod::getDocComment() — split on the first blank line into summary (1st paragraph) vs details (rest). This only works because controllers in Api/V1 already have real one-or-two-paragraph docblocks (see ProductController::index's "Pass `discounted=1`..." line) — if a new API endpoint ships without a docblock, its docs page entry will just have no summary, not an error.
- Request-body field lists (name/type/required/example/notes) are NOT reflected at runtime — several FormRequest::rules() methods call `$this->user()`/`$this->route()` which throws outside a real HTTP request. They're a hand-maintained `REQUEST_FIELDS` const array keyed by route name, mirroring each FormRequest 1:1 as of 2026-08-27. Update that array by hand whenever a write endpoint's FormRequest fields change — it will NOT auto-detect drift.
- Endpoints are grouped via a curated `GROUP_LABELS` map (route-name resource segment → label: Authentication/Catalog/Cart & checkout/Account/Support), ordered by `GROUP_ORDER`. Gotcha hit and fixed: `array_search(...) ?: 99` is wrong because index `0` (Authentication, first in GROUP_ORDER) is falsy in PHP — silently sorted it last. Must use `=== false` to detect "not found", not a truthiness check. Covered by tests/Unit/Services/ApiDocumentationServiceTest.php.

Frontend: single resources/js/pages/api-documentation/Index.vue — left sidebar (search + grouped endpoint list, method-colored tags using direct Tailwind hues since this app's shadcn theme is otherwise monochrome: sky=GET, emerald=POST, amber=PUT/PATCH, the existing `destructive` token=DELETE) and a right detail pane with a "Console" — path-param inputs, optional query string, optional bearer-token input (paste a Sanctum customer token to test authenticated endpoints; admin Users have no API tokens of their own), a JSON body editor pre-filled from REQUEST_FIELDS examples, and a real `fetch()` straight from the browser to the live API (not routed through Inertia) showing status/timing/pretty-printed response in a dark monospace console panel. "Copy as cURL" builds a real curl command from current inputs and copies via the clipboard, toasted with the app's existing `toast` from 'vue-sonner' (see resources/js/lib/flashToast.ts for the established import). Route: GET `api-documentation` (named `api-documentation.index`, not a resource — single index action). Sidebar entry in the "Administrator" group after Business settings. Don't rebuild — extend the existing files instead.
