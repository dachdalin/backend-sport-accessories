---
paths:
  - 'app/Http/Controllers/Backend/UserController.php,resources/js/pages/users/**,app/Providers/FortifyServiceProvider.php'
---

# Providers

## User ban/status added (2026-08-30)
Added users.status (boolean, default true) so admins can ban other admins. Migration 2026_08_30_062841_add_status_to_users_table. User model: status in #[Fillable], cast boolean, default $attributes status=>true. StoreUserRequest/UpdateUserRequest validate status as sometimes|boolean. UserController@store/update sets $data['status'] = $request->boolean('status'[, true for store]) — same unchecked-checkbox-means-false convention as Customer status (see [[pages-customers]]). UserController@update blocks an admin from banning their own account (mirrors the existing self-delete guard in destroy()). CreateUserAction/UpdateUserAction accept status in $data. Login block lives in FortifyServiceProvider::configureActions() — Fortify::authenticateUsing() closure looks up the user by email, checks the password hash itself, then throws a ValidationException("This account has been banned.") if status is false; this replaces Fortify's default guard->attempt() entirely, so any future auth tweak (2FA, passkeys) must go through this closure too. Frontend: users/{Index,Create,Edit,Show}.vue show an Active/Banned Badge and a status Checkbox in the "Access" card (same pattern as customers/Edit.vue). Don't rebuild — extend these files instead.
