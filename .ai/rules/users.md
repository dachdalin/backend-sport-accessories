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

## Index.vue and Show.vue use an initials Avatar, and Index's actions are icon buttons (redesigned 2026-08-31)
Index.vue merged the separate Name/Email columns into one "User" column: `<Avatar><AvatarFallback>{{ initials(user.name) }}</AvatarFallback></Avatar>` (same `initials()` helper as `messages/Index.vue`) next to a stacked name/email, the whole thing wrapped in one `<Link :href="show(user)">`. The Status badge got a `size-1.5 rounded-full bg-current` dot before the label. The Actions column is now icon-only ghost buttons (`Eye`/`Pencil`/`Trash2` from `@lucide/vue`, `size="icon-sm"`, each with `aria-label`) instead of the old three text buttons — this is a deliberate deviation from Products/Banners (which still use text buttons); don't "fix" it back without asking, and don't copy the icon-button pattern onto other list pages as if it were now the house style. Show.vue's header got a matching `size-12` Avatar to the left of the `Heading`, in a `flex items-start` row — use `items-start` (not `items-center`) when pairing an Avatar with `Heading`, since `Heading`'s own `mb-8` on its root `<header>` throws off vertical centering against a same-height sibling.
