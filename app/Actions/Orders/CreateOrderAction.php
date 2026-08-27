<?php

namespace App\Actions\Orders;

use App\Events\OrderPlaced;
use App\Models\Order;
use App\Services\OrderService;
use Illuminate\Support\Facades\DB;

class CreateOrderAction
{
    public function __construct(private readonly OrderService $orderService) {}

    /**
     * @param  array{customer_name: string, customer_email: ?string, customer_phone: ?string, shipping_address: string, order_status: string, payment_status: string, payment_method: ?string, discount_amount: ?float, discount_type: ?string, shipping_cost: ?float, order_note: ?string}  $data
     * @param  array<int, array{product_id: int, quantity: int, unit_price: float}>  $items
     */
    public function handle(array $data, array $items): Order
    {
        $data['order_number'] = $this->orderService->generateOrderNumber();
        $data['order_amount'] = $this->orderService->calculateOrderAmount(
            $items,
            (float) ($data['shipping_cost'] ?? 0),
            (float) ($data['discount_amount'] ?? 0),
        );

        return DB::transaction(function () use ($data, $items) {
            $order = Order::create($data);

            $order->items()->createMany($this->orderService->resolveItems($items));

            OrderPlaced::dispatch($order);

            return $order;
        });
    }
}
