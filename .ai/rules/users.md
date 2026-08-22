---
paths:
  - 'app/Http/Controllers/Backend/UserController.php,resources/js/pages/users/**'
---

# Users

## Users CRUD is full resource (show included), not the usual except(['show'])
Unlike most admin CRUD in this app (which use ->except(['show']) since list tables cover enough detail), Users got a dedicated Show page (resources/js/pages/users/Show.vue) added 2026-08-22 per explicit user request for "full users crud". Route::resource('users', UserController::class) has no ->except — includes show(). Index.vue links the name and a "View" button to it. If adding show pages becomes a pattern elsewhere, this file is the reference (read-only dl/dt/dd detail layout + Edit/Delete actions inline).
