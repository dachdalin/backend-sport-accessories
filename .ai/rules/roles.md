---
paths:
  - 'app/Http/Controllers/Backend/RoleController.php,resources/js/pages/roles/**'
---

# Roles

## Roles create/edit are full pages (roles/Create.vue, roles/Edit.vue), not Dialogs
Migrated 2026-08-28 from inline Dialogs to $gateCrudWithForms + dedicated create()/edit() controller actions, matching every other admin resource. roles/Create.vue and roles/Edit.vue use the same two-Card layout as users/ pages: "Name" (Tag icon) and "Access" (ShieldCheck icon, same copy "Choose what this role can access" as users/Create.vue), with Save/Cancel in the Access card's CardFooter. roles/Index.vue keeps only the destroy confirmation Dialog — Edit is a Link to the edit page. The roles table has a "Created" column using the same short-date `formatDate` helper as users/Index.vue.

## Role permission IDs must be resolved to models before syncPermissions(), never passed as raw array
CreateRoleAction/UpdateRoleAction call `Permission::query()->whereKey($data['permissions'] ?? [])->get()` and pass that to `syncPermissions()` — never pass the raw `permissions` array of IDs directly. Reason: a real browser form submits checkbox values as strings ("134"), and Spatie's `syncPermissions()`/`getStoredPermission()` treats a numeric *string* as a permission **name** lookup (not an ID lookup), throwing `PermissionDoesNotExist`. `RoleControllerTest`'s original tests never caught this because the HTTP test client keeps PHP int arrays as ints — only an actual form POST reproduces it. Covered by `test_role_can_be_created_with_permission_ids_sent_as_strings` / `..._updated_with_...`.
