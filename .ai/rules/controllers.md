---
paths:
  - 'resources/js/pages/**,app/Http/Controllers/**'
---

# Controllers

## Modal vs page for simple CRUD resources
For a lookup-style table with 2 real fields beyond id/timestamps (e.g. `colors`: name, code), do create/edit/delete in Dialog modals on one index page (see resources/js/pages/colors/Index.vue), not separate create/edit pages. Reserve dedicated pages for resources with more fields. Backend: resource controller (index/store/update/destroy only), Form Requests per action, `Inertia::flash('toast', ...)` + `to_route()`. Frontend: `<Form v-bind="Controller.action.form()">` (wayfinder `formVariants: true` in vite.config.ts — run `php artisan wayfinder:generate` after adding routes so `.form()` helpers exist).
