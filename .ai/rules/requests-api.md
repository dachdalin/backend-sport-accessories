---
paths:
  - 'app/Http/Controllers/Api/**,app/Http/Requests/Api/**,routes/api.php'
---

# Requests Api

## Customer profile API built (2026-08-27)
Built GET/PUT api/v1/profile (auth:sanctum) — singleton pattern like Backend BusinessSettingController, not an apiResource. App\Http\Controllers\Api\V1\ProfileController::show/update, App\Http\Requests\Api\V1\UpdateProfileRequest (name/email/phone/address; email unique ignoring $request->user()->id). Reuses existing App\Actions\Customers\UpdateCustomerAction (same one Backend\CustomerController uses) — controller force-sets $data['status'] = $customer->status before calling it, so a customer can never toggle their own account's active/inactive flag through this endpoint even though the shared Action accepts it. No password-change endpoint here — out of scope, would be a separate route if needed. Don't rebuild — extend the existing files instead.
