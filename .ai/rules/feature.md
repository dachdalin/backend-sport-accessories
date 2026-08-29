---
paths:
  - 'tests/Feature/**'
---

# Feature

## UserFactory::create() always creates/assigns the "admin" role — account for it in role-count assertions
Every `User::factory()->create()` triggers `UserFactory::configure()`'s `afterCreating` hook, which calls `Role::findOrCreate('admin')` and assigns it — even in tests that never mention roles. This is a real DB side effect (not test pollution), so any test that then does `Role::create(['name' => 'admin'])` will hit Spatie's `RoleAlreadyExists`; use `Role::findOrCreate('admin')` instead. Any test asserting an exact `roles` count/listing size after `User::factory()->create()` must add 1 for that auto-created admin role (see the fix in `RoleControllerTest::test_roles_index_page_is_displayed`, which also avoids asserting a fixed array position since sqlite's `created_at` timestamp ties don't guarantee `->latest()` ordering between two rows created in the same second — assert membership instead, e.g. `collect($roles)->pluck('id')->contains($role->id)`).
