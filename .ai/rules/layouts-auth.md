---
paths:
  - 'app/Http/Middleware/HandleInertiaRequests.php,resources/js/components/AppLogo.vue,resources/js/layouts/auth/**'
---

# Layouts Auth

## Real shop identity (name/logo) is shared globally — don't hardcode config('app.name') or a static icon again
HandleInertiaRequests::share() now derives 'name' and 'logoUrl' from BusinessSettingService (site_name/logo columns), not config('app.name'). 'logoUrl' is null when logo is still the 'def.png' sentinel (no upload yet) — components must handle that null (AppLogo.vue and AuthSimpleLayout.vue both fall back to the amber icon badge in that case, never render a broken <img>).

Gotcha (cost real debugging time): AppSidebar/AppLogo is a persistent Inertia layout component — it mounts once and survives client-side visits. Reading `page.props.name` / `page.props.logoUrl` into a plain `const` at setup() freezes it at first mount; a later business-settings save (same tab, no full reload) silently doesn't update the sidebar. Always wrap shared reactive page props in `computed(() => page.props.x)` in any component that lives inside a persistent layout.

Also: this dev environment's APP_URL must match whatever port Chrome/curl actually reaches (Storage::url() builds absolute URLs from it) — if a Storage-backed <img> ever shows naturalWidth 0, check APP_URL before suspecting the component.
