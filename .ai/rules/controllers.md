---
paths:
  - 'resources/js/pages/**,app/Http/Controllers/**'
---

# Controllers

## Modal vs page for simple CRUD resources
For a lookup-style table with 2 real fields beyond id/timestamps (e.g. `colors`: name, code), do create/edit/delete in Dialog modals on one index page (see resources/js/pages/colors/Index.vue), not separate create/edit pages. Reserve dedicated pages for resources with more fields. Backend: resource controller under `App\Http\Controllers\Backend` (index/store/update/destroy only), Form Requests under `App\Http\Requests\Backend`, business logic delegated to an Action class per [[actions]], `Inertia::flash('toast', ...)` + `to_route()`. Frontend: `<Form v-bind="Controller.action.form()">` importing from `@/actions/App/Http/Controllers/Backend/{Name}Controller` (wayfinder `formVariants: true` in vite.config.ts — run `php artisan wayfinder:generate --with-form` after adding routes so `.form()` helpers exist).
