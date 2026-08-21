<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Str;

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
}
