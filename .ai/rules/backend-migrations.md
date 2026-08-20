---
paths:
  - 'app/Models/*.php,app/Http/Controllers/Backend/**,database/migrations/**'
---

# Backend Migrations

## This app is single-vendor, not multi-vendor
database/backend-sport-accessorie.sql is a reference schema from a multi-vendor marketplace dump (seller_id, admin_commission, seller_amount, deliveryman roles, etc). This app is a single-store admin panel — do not port vendor/seller/marketplace concepts from it (seller payout/withdrawal methods, vendor commission, per-seller order splitting, deliveryman roles). A WithdrawalMethod feature (seller payout configs) was built by mistake and removed for this reason. SearchFunctionVisibility::Seller was kept as-is since it's an established, tested visibility scope, not new marketplace scope creep — don't expand on it though.
