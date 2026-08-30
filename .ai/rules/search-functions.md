---
paths:
  - 'app/Http/Controllers/Backend/SearchFunctionController.php,resources/js/pages/search-functions/**,routes/web.php'
---

# Search Functions

## Search functions converted to Dialog-based CRUD (2026-08-30)
Rebuilt from dedicated Create/Edit pages to the modal pattern (see [[controllers]], reference resources/js/pages/colors/Index.vue): 3 real fields (key, url, visible_for) qualifies as a lookup-style resource. Create/Edit.vue deleted; everything now lives in Index.vue as a create Dialog (`createOpen` ref, `reset-on-success` + `@success="createOpen = false"`) and a per-row edit Dialog (`editingSearchFunction` ref, opened via `openEdit(row)`), each with a `Select` for `visible_for` fed by the `visibilities` prop.

Routes: switched from `$gateCrudWithForms(...->except(['show']))` to `$gateCrud(...->only(['index','store','update','destroy']))` in routes/web.php — no more `create`/`edit` GET routes. `SearchFunctionController::create()`/`edit()` methods removed; `index()` now also returns `visibilities` (previously only on the create/edit pages) since the Select lives on the index page for both dialogs.

After any such controller/route signature change, run `php artisan wayfinder:generate --with-form` — the generated `resources/js/actions/.../SearchFunctionController.ts` and `resources/js/routes/search-functions/index.ts` must drop `create`/`edit` too, or stale helpers linger.

Index.vue gates the add/edit/delete affordances with `usePermissions().can('create|edit|delete search functions')`, matching colors — the old Create/Edit pages had no permission checks at all (relied solely on route middleware), so this is an upgrade, not a behavior removal.

Verified: Pint/types/lint/build clean, `SearchFunctionControllerTest` 10/10 pass (removed the now-invalid create-page test, added a `visibilities` assertion to the index test), `php artisan route:list --path=search-functions` shows exactly 4 routes, and a full live-browser round trip (create → edit including changing the Select audience → delete) with correct toasts at each step.
