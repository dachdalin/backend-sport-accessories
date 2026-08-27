---
paths:
  - app/Http/Controllers/Backend/ColorController.php
---

# Controllers Backend

## Colors is the permission-enforcement pilot (2026-08-27)
ColorController is the first (and so far only) controller with real permission enforcement, added as a pilot after confirming the app-wide gap: permissions/roles were fully seeded and manageable via the UI, but no route anywhere actually checked them. Pattern used: implement `Illuminate\Routing\Controllers\HasMiddleware` on the controller with a static `middleware(): array` method returning per-action `new Middleware('permission:{action} colors', only: [...])` entries (view→index, create→store, edit→update, delete→destroy) — NOT `Route::resource(...)->middleware()` in routes/web.php, since a single route-level middleware call can't vary the required permission per HTTP verb the way controller-level `HasMiddleware` can.

tests/Feature/ColorControllerTest.php gained a `actingAsUserWithPermission(string $permission)` helper (creates a bare User, `givePermissionTo(Permission::findOrCreate($permission))`, calls actingAs) that replaced the old bare `User::factory()->create()` + `actingAs($user)` pattern in every test — plain factory users have zero permissions and now get 403'd. Added 4 new tests asserting 403 for each action when the permission is missing.

This is NOT yet rolled out to any other resource — every other Backend controller still has zero enforcement (see [[backend]] permission-feature-checked rule for the full list/context). Rolling this pattern out further is a separate, explicitly-scoped task per resource (each one needs its existing controller test file updated the same way ColorControllerTest was), not something to do opportunistically while touching an unrelated feature.
