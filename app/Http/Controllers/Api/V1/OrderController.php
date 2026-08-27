<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Orders\ProcessOrderAction;
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
}
