---
paths:
  - 'app/Http/Controllers/**,app/Actions/**,app/Services/**'
---

# Actions Services

## Backend CRUD layering: Controller (Backend NS) + FormRequest + Action + Service
Standalone-table CRUD in this app follows: `App\Http\Controllers\Backend\{X}Controller` (resource controller, thin — injects Actions/Services per method), `App\Http\Requests\Backend\Store{X}Request`/`Update{X}Request`, `App\Actions\{X}s\Create/Update/Delete{X}Action` (single-purpose, `handle()` method) for writes, and `App\Services\{X}Service` for shared read/query logic (e.g. `list()`) and any cross-cutting normalization multiple layers need.

Gotcha (hit building Currency): if a Service normalizes input (e.g. upper-casing a `code`) and only the Action applies it, uniqueness validation runs against the *raw* un-normalized value and a same-value-different-case duplicate slips past validation, then throws an uncaught DB unique-constraint exception. Fix: normalize inside the FormRequest's `rules()` method (which supports container method injection, e.g. `rules(CurrencyService $service)`) via `$this->merge(...)` *before* returning the rule set, so the `unique` rule checks the normalized value. Actions then receive already-normalized `validated()` data and stay trivial.

See colors/brands/currencies for the pattern.
