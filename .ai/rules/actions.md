---
paths:
  - 'app/Http/Controllers/**,app/Http/Requests/**,app/Actions/**'
---

# Actions

## Backend CRUD: folder-scoped controllers, Action classes, strict validation, try/catch
Admin/backend CRUD controllers live in `app/Http/Controllers/Backend/` (namespace `App\Http\Controllers\Backend`), Form Requests in `app/Http/Requests/Backend/`. When an API is added, mirror this as `Controllers/Api/` + `Requests/Api/` so namespaces never collide.

Controllers stay thin: no direct `Model::create/update/delete` calls. Each write delegates to a single-purpose Action class in `app/Actions/{Model}/` (e.g. `App\Actions\Brands\CreateBrandAction`), injected via method injection. Actions own the transaction boundary (`DB::transaction()`) and any multi-step / multi-table logic, so the same action can later be reused by an Api controller.

Controller action methods (store/update/destroy) wrap the action call in try/catch(Throwable): `report($e)`, flash an error toast via `Inertia::flash('toast', ['type' => 'error', ...])`, `return back()->withInput()`. Actions that touch both DB and external state (e.g. file storage) must compensate on failure — e.g. delete an uploaded file if the DB write inside the transaction throws (see `App\Actions\Brands\CreateBrandAction`).

Form Requests use strict rules: `bail` first, explicit `regex` for constrained formats (hex color codes, name charsets), and `mimes` (not just `image`) for uploads, per [[laravel-security]] file-upload guidance.
