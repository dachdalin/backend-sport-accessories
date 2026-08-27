<?php

namespace App\Actions\Orders;

use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Events\OrderPlaced;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Product;
use App\Models\ShippingAddress;
use App\Services\OrderService;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class ProcessOrderAction
{
    public function __construct(private readonly OrderService $orderService) {}

    /**
     * Place an order for the authenticated customer from their cart, calculating fulfillment
     * (live product pricing, stock, shipping, and coupon discount) server-side.
     *
     * @param  array{shipping_method_id: ?int, payment_method: ?string, coupon_code: ?string, order_note: ?string}  $data
     */
    public function handle(Customer $customer, ShippingAddress $shippingAddress, array $data): Order
    {
        $cartItems = $this->orderService->cartItemsFor($customer);

        if ($cartItems->isEmpty()) {
            throw ValidationException::withMessages([
                'cart' => [__('Your cart is empty.')],
            ]);
        }

        $shippingCost = $this->orderService->resolveShippingCost($data['shipping_method_id'] ?? null);

        return DB::transaction(function () use ($customer, $shippingAddress, $data, $cartItems, $shippingCost) {
            $products = Product::query()
                ->whereIn('id', $cartItems->pluck('product_id'))
                ->lockForUpdate()
                ->get()
                ->keyBy('id');

            $items = $cartItems->map(function ($cartItem) use ($products) {
                /** @var Product|null $product */
                $product = $products->get($cartItem->product_id);

                if (! $product || ! $product->status) {
                    throw ValidationException::withMessages([
                        'cart' => [__('A product in your cart is no longer available.')],
                    ]);
                }

                if ($cartItem->quantity > $product->current_stock) {
                    throw ValidationException::withMessages([
                        'cart' => [__(':product only has :stock left in stock.', [
                            'product' => $product->name,
                            'stock' => $product->current_stock,
                        ])],
                    ]);
                }

                $unitPrice = $product->final_price;

                return [
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'unit_price' => $unitPrice,
                    'quantity' => $cartItem->quantity,
                    'subtotal' => round($unitPrice * $cartItem->quantity, 2),
                ];
            })->all();

            $itemsTotal = array_sum(array_column($items, 'subtotal'));
            $coupon = $this->orderService->resolveCouponDiscount($data['coupon_code'] ?? null, $itemsTotal);
            $orderAmount = max(0.0, $itemsTotal + $shippingCost - $coupon['amount']);

            $order = Order::create([
                'order_number' => $this->orderService->generateOrderNumber(),
                'customer_id' => $customer->id,
                'customer_name' => $customer->name,
                'customer_email' => $customer->email,
                'customer_phone' => $customer->phone,
                'shipping_address' => $this->formatShippingAddress($shippingAddress),
                'order_status' => OrderStatus::Pending->value,
                'payment_status' => OrderPaymentStatus::Unpaid->value,
                'payment_method' => $data['payment_method'] ?? null,
                'discount_amount' => $coupon['amount'],
                'discount_type' => $coupon['type'],
                'shipping_cost' => $shippingCost,
                'order_amount' => $orderAmount,
                'order_note' => $data['order_note'] ?? null,
            ]);

            $order->items()->createMany($items);

            foreach ($items as $item) {
                $products->get($item['product_id'])->decrement('current_stock', $item['quantity']);
            }

            $cartItems->each->delete();

            OrderPlaced::dispatch($order);

            return $order->load('items');
        });
    }

    /**
     * Snapshot the customer's chosen address into the order's freeform shipping_address text.
     */
    private function formatShippingAddress(ShippingAddress $shippingAddress): string
    {
        return collect([
            $shippingAddress->contact_person_name,
            $shippingAddress->phone,
            $shippingAddress->address,
            $shippingAddress->city,
            $shippingAddress->state,
            $shippingAddress->zip,
            $shippingAddress->country,
        ])->filter()->implode(', ');
    }
}
