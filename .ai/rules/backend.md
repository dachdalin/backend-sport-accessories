---
paths:
  - 'database/seeders/**,app/Http/Controllers/Backend/RoleController.php,app/Http/Controllers/Backend/UserController.php'
---

# Backend

## Permission feature checked + manager role seeded (2026-08-27)
Verified the Spatie roles/permissions feature end-to-end: RolePermissionSeeder's `$crudResources` list is in sync with every `Route::resource()` in routes/web.php (205 permissions total — 4 actions × 50 resources + view dashboard/business settings/messages/create messages + edit business settings). Role/User CRUD (RoleController, UserController, their Actions) correctly manage permissions and role assignment.

Added a second role: `manager` (read-only) — synced to every `view *` permission EXCEPT `view users`, `view roles`, `view business settings` (kept admin-only). AdminSeeder now also creates a `manager@example.com` / password demo user with the `manager` role, alongside the existing `admin@example.com`. Both use `syncRoles()` (not `assignRole()`) so re-running the seeder can't stack duplicate role rows.

(The gap flagged here on 2026-08-27 — no route enforcing permissions — was closed 2026-08-27 by commit 6a485ec; see below.)

## Permission enforcement now wired everywhere
Commit 6a485ec (2026-08-27) closed the previously-flagged gap: every Route::resource() in routes/web.php now uses ->middlewareFor(action, "permission:{verb} {resource}") via the $gateCrud/$gateCrudWithForms helpers, so all ~50 admin resources are actually permission-gated (not just seeded). Gate::before in AppServiceProvider lets 'admin' role bypass all checks. Verified 2026-08-28: catalog manager role's routes/sidebar/composable all consistent, RolePermissionTest passing. Do not re-flag "no route enforces permissions" — that's stale.
