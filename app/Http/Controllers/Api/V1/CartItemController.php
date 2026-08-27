<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\CartItems\CreateCartItemAction;
use App\Actions\CartItems\DeleteCartItemAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreCartItemRequest;
use App\Http\Resources\Api\V1\CartItemResource;
use App\Models\CartItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class CartItemController extends Controller
{
    /**
     * Display a listing of the authenticated customer's cart items.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return CartItemResource::collection(
            CartItem::query()
                ->where('customer_id', $request->user()->id)
                ->with('product')
                ->latest()
                ->get(),
        );
    }

    /**
     * Add a product to the authenticated customer's cart.
     */
    public function store(StoreCartItemRequest $request, CreateCartItemAction $action): JsonResponse
    {
        $cartItem = $action->handle([
            'customer_id' => $request->user()->id,
            'product_id' => $request->validated('product_id'),
            'quantity' => $request->validated('quantity'),
        ]);

        return (new CartItemResource($cartItem->load('product')))
            ->response()
            ->setStatusCode(JsonResponse::HTTP_CREATED);
    }

    /**
     * Remove the specified item from the authenticated customer's cart.
     */
    public function destroy(Request $request, CartItem $cartItem, DeleteCartItemAction $action): JsonResponse
    {
        abort_unless($cartItem->customer_id === $request->user()->id, 404);

        $action->handle($cartItem);

        return response()->json(status: JsonResponse::HTTP_NO_CONTENT);
    }
}
