<?php

namespace App\Services;

use App\Enums\CouponType;
use App\Enums\TaxType;
use App\Models\CartItem;
use App\Models\Coupon;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingMethod;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class OrderService
{
    /**
     * List all orders with their items, most recently placed first.
     *
     * @return Collection<int, Order>
     */
    public function list(): Collection
    {
        return Order::query()
            ->withCount('items')
            ->latest()
            ->get();
    }

    /**
     * Generate a unique, human-friendly order number.
     */
    public function generateOrderNumber(): string
    {
        do {
            $orderNumber = 'ORD-'.strtoupper(Str::random(8));
        } while (Order::query()->where('order_number', $orderNumber)->exists());

        return $orderNumber;
    }

    /**
     * Calculate the order total from its line items, shipping cost, and discount.
     *
     * @param  array<int, array{quantity: int, unit_price: float|string}>  $items
     */
    public function calculateOrderAmount(array $items, float $shippingCost, float $discountAmount): float
    {
        $itemsTotal = array_reduce(
            $items,
            fn (float $carry, array $item) => $carry + ($item['quantity'] * (float) $item['unit_price']),
            0.0,
        );

        return max(0.0, $itemsTotal + $shippingCost - $discountAmount);
    }

    /**
     * Resolve line items into order_items rows, snapshotting the product name and subtotal.
     *
     * @param  array<int, array{product_id: int, quantity: int, unit_price: float}>  $items
     * @return array<int, array{product_id: int, product_name: string, unit_price: float, quantity: int, subtotal: float}>
     */
    public function resolveItems(array $items): array
    {
        $productNames = Product::query()
            ->whereIn('id', array_column($items, 'product_id'))
            ->pluck('name', 'id');

        return array_map(fn (array $item) => [
            'product_id' => $item['product_id'],
            'product_name' => $productNames->get($item['product_id'], 'Unknown product'),
            'unit_price' => $item['unit_price'],
            'quantity' => $item['quantity'],
            'subtotal' => $item['quantity'] * $item['unit_price'],
        ], $items);
    }

    /**
     * Fetch the authenticated customer's cart items with their product loaded.
     *
     * @return Collection<int, CartItem>
     */
    public function cartItemsFor(Customer $customer): Collection
    {
        return CartItem::query()
            ->where('customer_id', $customer->id)
            ->with('product')
            ->get();
    }

    /**
     * Resolve the shipping cost for an order, validating the shipping method is active.
     */
    public function resolveShippingCost(?int $shippingMethodId): float
    {
        if (! $shippingMethodId) {
            return 0.0;
        }

        $shippingMethod = ShippingMethod::query()
            ->where('id', $shippingMethodId)
            ->where('status', true)
            ->first();

        if (! $shippingMethod) {
            throw ValidationException::withMessages([
                'shipping_method_id' => [__('The selected shipping method is unavailable.')],
            ]);
        }

        return (float) $shippingMethod->cost;
    }

    /**
     * Validate a coupon code against the items subtotal and return the discount it grants.
     *
     * @return array{amount: float, type: ?string}
     */
    public function resolveCouponDiscount(?string $code, float $itemsTotal): array
    {
        if (! $code) {
            return ['amount' => 0.0, 'type' => null];
        }

        $coupon = Coupon::query()
            ->where('code', $code)
            ->where('status', true)
            ->first();

        if (! $coupon) {
            throw ValidationException::withMessages([
                'coupon_code' => [__('This coupon code is invalid.')],
            ]);
        }

        if ($coupon->expires_at && $coupon->expires_at->isPast()) {
            throw ValidationException::withMessages([
                'coupon_code' => [__('This coupon code has expired.')],
            ]);
        }

        if ($coupon->min_order_amount && $itemsTotal < $coupon->min_order_amount) {
            throw ValidationException::withMessages([
                'coupon_code' => [__('A minimum order amount of :amount is required for this coupon.', ['amount' => $coupon->min_order_amount])],
            ]);
        }

        $discountAmount = $coupon->type === CouponType::Percentage
            ? round($itemsTotal * ($coupon->value / 100), 2)
            : min((float) $coupon->value, $itemsTotal);

        return [
            'amount' => $discountAmount,
            'type' => $coupon->type === CouponType::Percentage ? TaxType::Percent->value : TaxType::Amount->value,
        ];
    }
}
