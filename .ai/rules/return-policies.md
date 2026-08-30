---
paths:
  - 'app/Http/Controllers/Backend/ReturnPolicyController.php,routes/web.php,resources/js/pages/return-policies/**'
---

# Return Policies

## Return policies converted to modal-CRUD (2026-08-30)
Removed the dedicated Create.vue/Edit.vue pages; add/edit/delete are now Dialogs on `return-policies/Index.vue`, following the exact pattern already established in `resources/js/pages/colors/Index.vue` (createOpen ref + `v-model:open`, `reset-on-success` + `@success="createOpen = false"` on the create Form; `editingPolicy` ref<T|null> + `openEdit()` + `:open="editingPolicy !== null"` for edit). Controller no longer has `create()`/`edit()` methods. Route changed from `$gateCrudWithForms(...)->except(['show'])` to `$gateCrud(...)->only(['index','store','update','destroy'])` — see the doc comments above those two closures in routes/web.php, they exist specifically for this modal-vs-page distinction. After any such route/controller change, re-run `php artisan wayfinder:generate --with-form` (the store/update/destroy action helpers regenerate automatically; stale `create()`/`edit()` disappear on their own). Also removed the two now-nonexistent-route tests (`return-policies.create`/`.edit` page-display tests) from ReturnPolicyControllerTest.

Don't rebuild — extend the existing files instead.
