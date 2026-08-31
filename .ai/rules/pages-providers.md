---
paths:
  - 'resources/js/app.ts,resources/js/pages/ErrorPage.vue,app/Providers/AppServiceProvider.php'
---

# Pages Providers

## Branded Inertia error pages: ErrorPage.vue, wired via handleExceptionsUsing
403/404/419/429/500/503 render `resources/js/pages/ErrorPage.vue` (sport/scoreboard themed) via `Inertia::handleExceptionsUsing()` in `AppServiceProvider::configureErrorPages()`. 500/503 fall through to Laravel's debug page when `app()->hasDebugModeEnabled()` so local stack traces still show.

`resources/js/app.ts`'s `layout` switch wraps every page in `AppLayout` by default — 'ErrorPage' (like 'Welcome') is listed as a `return null` exception so it renders standalone (no sidebar/topbar). Any other standalone/full-bleed page must be added to that same exception list or it silently inherits the admin shell.
