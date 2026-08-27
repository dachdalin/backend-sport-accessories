---
paths:
  - 'app/Http/Controllers/Api/**,app/Actions/Orders/**,database/migrations/**'
---

# Orders Migrations

## Order processing/checkout API built (2026-08-27) — fulfillment calculated server-side
Built customer checkout: POST /api/v1/orders (auth:sanctum, throttle:order-store 5/min by customer) processes the authenticated customer's cart_items into an Order. App\Actions\Orders\ProcessOrderAction does everything inside one DB::transaction: locks the cart's products with lockForUpdate, verifies each is active/in-stock, prices items from Product::final_price (live price, never client-supplied), resolves shipping cost via App\Services\OrderService::resolveShippingCost() (ShippingMethod must be active), resolves an optional coupon via OrderService::resolveCouponDiscount() (reuses Coupon validation logic from CouponController: status/expiry/min_order_amount), decrements Product.current_stock per item, then clears the customer's cart_items. Added orders.customer_id (nullable FK, nullOnDelete — via migration 2026_08_27_140000_add_customer_id_to_orders_table, NOT the original create_orders_table migration since it's already deployed) plus Order::customer()/Customer::orders(). shipping_address stays the existing freeform text column — the API snapshots the customer's chosen ShippingAddress (validated via Rule::exists('shipping_addresses','id')->where('customer_id', ...) in StoreOrderRequest, so ownership is enforced in validation, not a controller abort_unless) into that text field at order time. Also added GET /api/v1/orders (index/show, no throttle) scoped to the caller's own orders like SupportTicketController. Don't rebuild — extend ProcessOrderAction/OrderService/OrderController instead.
