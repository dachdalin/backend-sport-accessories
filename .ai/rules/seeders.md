---
paths:
  - 'database/seeders/RolePermissionSeeder.php,database/seeders/AdminSeeder.php'
---

# Seeders

## Two more demo roles seeded (2026-08-27) — catalog manager + support
Audited the roles/permissions feature before adding data: permissions can be created and assigned to roles via the Roles UI, but nothing enforces them anywhere yet — no route uses `permission:`/`role:` middleware (only `auth`+`verified` gates every backend route), no controller calls `authorize()`/Gate, and AppSidebar shows every nav item to every logged-in user regardless of role. User explicitly chose (via AskUserQuestion) to seed more role data only, NOT wire up enforcement — that's still open if ever requested.

RolePermissionSeeder now defines 4 roles (admin unchanged — gets every permission):
- `manager` (pre-existing, built by a concurrent session) — read-only, `view *` on every crudResource except users/roles/business-settings.
- `catalog manager` (new) — full CRUD on catalog/merchandising/sales resources only (products, categories, attributes/colors/sizes/materials/brands/tags, coupons, flash/feature deals, deal-of-the-days, most-demandeds, stock-clearance-setups, gift-cards, orders, refund-requests, reviews, shipping-methods, delivery-zones) + view dashboard. No Users/Roles/Customers/content/support access. Named "catalog manager" not "manager" specifically to avoid colliding with the existing read-only `manager` role's meaning — don't rename either without checking both are intentionally distinct.
- `support` (new) — full CRUD on support-tickets + contacts (their workflow), view-only on customers + orders (context, not editing), + view dashboard.

AdminSeeder now creates 4 demo users to match: admin@example.com, manager@example.com (pre-existing), catalog-manager@example.com, support@example.com — all password `password`, all `email_verified_at` set at creation. Both seeders are idempotent (`findOrCreate`/`firstOrCreate`/`syncPermissions`/`syncRoles`), safe to re-run against an existing DB — confirmed by running against the real dev DB, not just tests. Covered by tests/Feature/RolePermissionTest.php. Don't rebuild — extend the existing seeders instead.
