<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Orders\ProcessOrderAction;
use App\Enums\OrderPaymentStatus;
use App\Enums\OrderStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreOrderRequest;
use App\Http\Resources\Api\V1\OrderResource;
use App\Models\Order;
use App\Models\ShippingAddress;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class OrderController extends Controller
{
    /**
     * Display a paginated listing of the authenticated customer's orders.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return OrderResource::collection(
            Order::query()
                ->where('customer_id', $request->user()->id)
                ->withCount('items')
                ->latest()
                ->paginate(15)
                ->withQueryString(),
        );
    }

    /**
     * Process the authenticated customer's cart into a new order, calculating fulfillment
     * (live pricing, stock, shipping, and coupon discount) server-side.
     */
    public function store(StoreOrderRequest $request, ProcessOrderAction $action): JsonResponse
    {
        $shippingAddress = ShippingAddress::query()->findOrFail($request->validated('shipping_address_id'));

        $order = $action->handle($request->user(), $shippingAddress, $request->safe()->except('shipping_address_id'));

        return (new OrderResource($order))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    /**
     * Display the specified order, if the caller owns it.
     */
    public function show(Request $request, Order $order): OrderResource
    {
        abort_unless($order->customer_id === $request->user()->id, 404);

        return new OrderResource($order->load('items'));
    }

    /**
     * Summarize the authenticated customer's order history: counts by fulfillment
     * status and total amount spent on paid orders.
     */
    public function summary(Request $request): JsonResponse
    {
        $totals = Order::query()
            ->where('customer_id', $request->user()->id)
            ->selectRaw(
                'count(*) as total_orders, '.
                'sum(case when order_status = ? then 1 else 0 end) as total_received, '.
                'sum(case when order_status = ? then 1 else 0 end) as total_pending, '.
                'sum(case when payment_status = ? then order_amount else 0 end) as total_spend',
                [OrderStatus::Delivered->value, OrderStatus::Pending->value, OrderPaymentStatus::Paid->value],
            )
            ->first();

        return response()->json([
            'data' => [
                'total_orders' => (int) $totals->total_orders,
                'total_received' => (int) $totals->total_received,
                'total_pending' => (int) $totals->total_pending,
                'total_spend' => round((float) $totals->total_spend, 2),
            ],
        ]);
    }
}
