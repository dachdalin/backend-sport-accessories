---
paths:
  - 'database/seeders/**,app/Http/Controllers/Backend/RoleController.php,app/Http/Controllers/Backend/UserController.php'
---

# Backend

## Permission feature checked + manager role seeded (2026-08-27)
Verified the Spatie roles/permissions feature end-to-end: RolePermissionSeeder's `$crudResources` list is in sync with every `Route::resource()` in routes/web.php (205 permissions total — 4 actions × 50 resources + view dashboard/business settings/messages/create messages + edit business settings). Role/User CRUD (RoleController, UserController, their Actions) correctly manage permissions and role assignment.

Added a second role: `manager` (read-only) — synced to every `view *` permission EXCEPT `view users`, `view roles`, `view business settings` (kept admin-only). AdminSeeder now also creates a `manager@example.com` / password demo user with the `manager` role, alongside the existing `admin@example.com`. Both use `syncRoles()` (not `assignRole()`) so re-running the seeder can't stack duplicate role rows.

IMPORTANT GAP (found during the check, not fixed — flagged to the user, needs its own decision before touching): despite the full permission-management UI being built and seeded, **no route in routes/web.php actually applies the `role`/`permission`/`role_or_permission` middleware** — every admin route is gated only by `['auth','verified']`, so any authenticated User (regardless of assigned role) can currently reach every admin page/action. The middleware itself works and is tested (tests/Feature/RolePermissionTest.php), it's just never attached to a real route. Wiring real enforcement across ~50 resources would touch every existing controller test (most create a bare `User::factory()->create()` with no role) — treat as a separate, explicitly-scoped task, not something to retrofit opportunistically.
