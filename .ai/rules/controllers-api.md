---
paths:
  - 'app/Http/Controllers/Api/**,routes/api.php'
---

# Controllers Api

## Order summary API built (2026-08-27)
GET api/v1/orders/summary (auth:sanctum) returns the authenticated customer's order totals: total_orders (count, all statuses), total_received (count where order_status=delivered), total_pending (count where order_status=pending), total_spend (sum of order_amount where payment_status=paid — deliberately only paid orders, not all order_amount, since "spend" means money actually paid). Single aggregate query in OrderController::summary() via selectRaw + case-when, no Resource class (plain JsonResponse, matches CouponController::apply's convention for computed/aggregate responses). Route MUST be registered before Route::apiResource('orders', ...) in routes/api.php — apiResource's GET orders/{order} would otherwise swallow "orders/summary" as an {order} id and 404 via failed model binding instead of matching the literal route. Don't rebuild — extend OrderController::summary() instead.
