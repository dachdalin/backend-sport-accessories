---
paths:
  - 'app/Models/*.php,app/Http/**,bootstrap/app.php'
---

# Http

## Role/permission system: spatie/laravel-permission
Roles/permissions use spatie/laravel-permission (v8). User model has HasRoles trait. Middleware aliases registered in bootstrap/app.php: `role`, `permission`, `role_or_permission`. Seed roles/permissions in database/seeders/RolePermissionSeeder.php (called from DatabaseSeeder). Only `admin` role exists so far — add new roles/permissions there, not ad hoc in controllers.
