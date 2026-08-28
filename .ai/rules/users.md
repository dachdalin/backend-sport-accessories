---
paths:
  - 'app/Http/Controllers/Backend/UserController.php,resources/js/pages/users/**'
---

# Users

## Users CRUD is full resource (show included), not the usual except(['show'])
Unlike most admin CRUD in this app (which use ->except(['show']) since list tables cover enough detail), Users got a dedicated Show page (resources/js/pages/users/Show.vue) added 2026-08-22 per explicit user request for "full users crud". Route::resource('users', UserController::class) has no ->except — includes show(). Index.vue links the name and a "View" button to it. If adding show pages becomes a pattern elsewhere, this file is the reference (read-only dl/dt/dd detail layout + Edit/Delete actions inline).

## Users index is paginated, unlike other admin lists
UserController@index uses ->paginate(15)->withQueryString() (added 2026-08-23), not ->get() like other Backend index controllers. users/Index.vue expects `users` prop as `{ data: User[], links: {url,label,active}[] }`, not a plain array — renders page links via `users.links` at the bottom. If other admin lists grow large, this is the reference for adding pagination.

## Users pages use sectioned Cards, not a flat dl/dt/dd box
Create/Edit/Show (redesigned 2026-08-28) each split their content into `Card`s by concern — Identity (name/email), Credentials (password on Create/Edit, verification status on Show), Access (roles) — with an icon + CardTitle/CardDescription header per card. Show adds a 4th "Activity" card for created_at/updated_at. Create/Edit put form actions in the last card's CardFooter instead of floating below the form. If adding a show/form page elsewhere, this trio (not the old single bordered dl/dt/dd box) is the reference. Index.vue's `User` type and table now include a "Created" column formatted with a local `formatDate` (short date, not full timestamp).
