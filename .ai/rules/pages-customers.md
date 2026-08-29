---
paths:
  - 'app/Models/Customer.php,app/Http/Controllers/Backend/CustomerController.php,app/Http/Requests/Backend/*CustomerRequest.php,resources/js/pages/customers/**'
---

# Pages Customers

## Customer wallet balance + read-only provider identity
Added 2026-08-29, redesigned Create/Edit into Card-sectioned responsive layout (same lg:grid-cols-3 pattern as products):
- `customers.balance` (decimal 10,2, default 0.00) is a real, admin-editable column — fillable, cast `decimal:2`, validated `nullable|numeric|min:0` in Store/UpdateCustomerRequest.
- `provider` ('manual'|'google'|'telegram') and `provider_id` are NOT columns — they're read-only accessors on Customer (Attribute::make) derived from existing google_id/telegram_id, so there's no second source of truth alongside the social-login columns documented in [[customers]]. CustomerController::edit() must call `$customer->append(['provider', 'provider_id'])` before rendering — they don't serialize by default. Never make these admin-editable; a customer's provider is set by how they signed up, not by the back office.
- Edit.vue's identity header (avatar initials via `useInitials`, provider Badge) and the amber (`#ff8904`) wallet tile reuse the sidebar's brand accent color (`--sidebar-primary` in resources/css/app.css) — the only non-neutral color in this admin theme — deliberately, so don't introduce another accent hue for similar "highlight" UI elsewhere; reuse this same amber for consistency.
