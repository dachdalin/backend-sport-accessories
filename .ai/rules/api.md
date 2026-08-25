---
paths:
  - 'app/Http/Controllers/Api/**,app/Http/Requests/Api/**,app/Http/Resources/Api/**,routes/api.php'
---

# Api

## Customer-facing API: Sanctum tokens, versioned under api/v1, mirrors Backend layering
This app now serves a stateless customer API (mobile app + future web client) alongside the Inertia admin panel. Key decisions:

- Auth: Laravel Sanctum (personal access tokens), installed via `php artisan install:api`. Guard is `auth:sanctum`. No session/cookie auth for this API — always issue/require a bearer token.
- Versioning: all routes live in `routes/api.php` under `Route::prefix('v1')->name('api.v1.')`, giving `api/v1/...` URLs. Add new versions as a new prefix group (`v2`), don't mutate v1 routes/contracts once shipped.
- Namespacing mirrors Backend: `App\Http\Controllers\Api\V1\{Area}`, `App\Http\Requests\Api\V1\{Area}`, `App\Http\Resources\Api\V1\{Model}Resource` — so Backend (Inertia) and Api namespaces never collide, per [[actions]].
- Layering: Controller (thin, returns JsonResponse) + Form Request + Action class for writes (`App\Actions\{Model}\...`), same as Backend CRUD. Reuse the same Action class from both Backend and Api controllers where the write logic is identical.
- Errors: `bootstrap/app.php` already has `shouldRenderJsonWhen` for `api/*` — Form Request validation failures and thrown exceptions render as JSON automatically, no manual error mapping needed in controllers.
- Rate limiting: named limiters defined in `AppServiceProvider::configureRateLimiting()` (not FortifyServiceProvider, which is web-only), applied via `->middleware('throttle:{name}')` on routes. `customer-auth` limiter = 5/min by IP, covers register/login.
- Customer auth: `Customer` model is `Authenticatable` + `HasApiTokens` (see app/Models/Customer.php). `password` column is nullable (admin-created customers via Backend\CustomerController have no password until they self-register/set one via the API) — always null-check `$customer->password` before `Hash::check()` in login flows.
