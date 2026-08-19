---
paths:
  - 'app/Services/**'
---

# Services

## Use a Service when write logic is reused outside the Action (e.g. by validation)
Add `app/Services/{Model}/{Model}Service` only when there's business logic that more than one layer needs — not as a default wrapper around an Action. Example: `App\Services\Tags\TagService` (normalize + case-insensitive findDuplicate) is used both by `StoreTagRequest`/`UpdateTagRequest::after()` (to reject duplicates with a field-level error before the DB unique constraint would) and by `CreateTagAction`/`UpdateTagAction` (to store the same normalized value). Form Requests can constructor-inject services — Laravel resolves FormRequest through the container, so `public function __construct(private readonly XService $x) { parent::__construct(); }` works normally.

If the logic is only ever called from one Action, keep it in the Action — don't add a Service for its own sake. See [[actions]] for the Controller → Action → transaction pattern this sits on top of.
