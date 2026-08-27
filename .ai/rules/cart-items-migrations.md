---
paths:
  - 'app/Http/Controllers/Api/**,app/Actions/CartItems/**,database/migrations/**'
---

# Cart Items Migrations

## Cart Items API built (2026-08-27)
Built customer cart: cart_items table (customer_id FK customers cascadeOnDelete, product_id FK products cascadeOnDelete, quantity, unique(customer_id, product_id)). App\Models\CartItem belongsTo Customer/Product; Customer::cartItems() and Product::cartItems() hasMany added. App\Http\Controllers\Api\V1\CartItemController (index/store/destroy, auth:sanctum only, follows ShippingAddress/Wishlist ownership-check pattern via abort_unless($cartItem->customer_id === $request->user()->id, 404)). App\Actions\CartItems\CreateCartItemAction increments quantity on the existing row instead of erroring when the same product is added twice (differs from Wishlist, which rejects duplicates via a unique validation rule) — no separate "update quantity" endpoint exists, re-POST to adjust. DeleteCartItemAction is a plain delete. Route: cart-items (apiResource, only index/store/destroy) under the auth:sanctum group in routes/api.php. No variant/color/size fields — this app has no product-variant system, cart is one row per (customer, product). Don't rebuild — extend the existing files instead.
