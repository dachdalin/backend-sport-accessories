---
paths:
  - 'resources/js/pages/**,resources/js/components/ui/checkbox/**'
---

# Checkbox

## Checkbox needs value="1" or its submit breaks the `boolean` validation rule
reka-ui's `CheckboxRoot` (used by `resources/js/components/ui/checkbox/Checkbox.vue`) renders a hidden native `<input type="checkbox">` whose `value` prop defaults to the literal string `"on"`. When a user actually checks the box in the browser and submits, that's what gets sent — and Laravel's `boolean` validation rule rejects `"on"` (only accepts true/false/1/0/"1"/"0"), so a real checked-checkbox submit throws "The {field} field must be true or false." This only surfaces on a real browser submit — feature tests that POST `'status' => '1'` directly never exercise the component and stay green, which is how this went unnoticed across ~73 files using `<Checkbox name="...">`.

Fix: always add `value="1"` explicitly alongside `:default-value`, e.g. `<Checkbox id="status" name="status" value="1" :default-value="true" />` — already done this way in `users/Create.vue`/`Edit.vue`, `products/Create.vue`, `help-topics/Create.vue`, and (2026-08-30) `return-policies/Index.vue` and `flash-deals/Create.vue`/`Edit.vue`. Any other page with a submitted `<Checkbox>` bound to a Laravel `boolean` rule likely has this same latent bug — fix it opportunistically whenever you touch one, and actually click it checked in a real browser (not just a feature test) to verify.
