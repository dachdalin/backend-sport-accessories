---
paths:
  - 'app/Http/Controllers/Api/**,app/Actions/Customers/**,app/Http/Requests/Api/**,routes/api.php'
---

# Http Requests Api

## Customer delete-account API built (2026-08-27)
Added DELETE api/v1/profile (auth:sanctum) to the existing singleton ProfileController (show/update/destroy — same controller as the profile GET/PUT built earlier, not a separate controller). App\Http\Requests\Api\V1\DeleteAccountRequest requires `password` and confirms it in `withValidator()` via a manual Hash::check against $this->user()->password (same null-check pattern as AuthController::login — a customer with no password set, e.g. admin-created via Backend\CustomerController, can never pass this check and so cannot self-delete via the API; that's intentional, not a bug).

Reuses the existing App\Actions\Customers\DeleteCustomerAction (shared with Backend\CustomerController::destroy) — extended it to also `$customer->tokens()->delete()` before `$customer->delete()`, since Sanctum's personal_access_tokens table is polymorphic with no FK/cascade, so deleting a Customer left orphaned token rows before this fix. This affects admin-initiated deletion too (also revokes that customer's tokens now), which is correct/desired for both flows.

Cascade behavior on delete (unchanged, just documented here): cart_items and shipping_addresses are FK cascadeOnDelete (removed with the customer); orders.customer_id is nullOnDelete (order history survives, customer_id just goes null — matches [[orders-migrations]] design of orders not strictly needing a live customer); reviews/wishlists are guest-style with no customer_id at all, unaffected either way. Don't rebuild — extend ProfileController/DeleteAccountRequest/DeleteCustomerAction instead.
