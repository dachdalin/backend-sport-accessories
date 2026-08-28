---
paths:
  - app/Http/Controllers/Backend/ColorController.php
---

# Controllers Backend

tests/Feature/ColorControllerTest.php has an `actingAsUserWithPermission(string $permission)` helper (creates a bare User, `givePermissionTo(Permission::findOrCreate($permission))`, calls actingAs) — plain factory users have zero permissions and get 403'd. This pattern was rolled out to every Backend controller test, not just colors.

## Route-level middlewareFor replaced the HasMiddleware pilot pattern
ColorController no longer uses HasMiddleware/static middleware(). Commit 6a485ec moved enforcement to route-level: Route::resource(...)->middlewareFor(action, "permission:{verb} colors") in routes/web.php, applied uniformly to every Backend resource (not just colors). The old note claiming route-level middleware "can't vary permission per verb" is wrong — Laravel's middlewareFor() does exactly that. Don't reintroduce controller-level HasMiddleware; follow the routes/web.php $gateCrud/$gateCrudWithForms pattern instead.
