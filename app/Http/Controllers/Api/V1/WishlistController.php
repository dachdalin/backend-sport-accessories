<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\Wishlists\CreateWishlistAction;
use App\Actions\Wishlists\DeleteWishlistAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\StoreWishlistRequest;
use App\Http\Resources\Api\V1\WishlistResource;
use App\Models\Wishlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class WishlistController extends Controller
{
    /**
     * Display a paginated listing of the authenticated customer's wishlist entries.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        return WishlistResource::collection(
            Wishlist::query()
                ->where('customer_email', $request->user()->email)
                ->with('product')
                ->latest()
                ->paginate(15)
                ->withQueryString(),
        );
    }

    /**
     * Add a product to the authenticated customer's wishlist.
     */
    public function store(StoreWishlistRequest $request, CreateWishlistAction $action): JsonResponse
    {
        $wishlist = $action->handle([
            'product_id' => $request->validated('product_id'),
            'customer_name' => $request->user()->name,
            'customer_email' => $request->user()->email,
        ]);

        return (new WishlistResource($wishlist->load('product')))
            ->response()
            ->setStatusCode(201);
    }

    /**
     * Remove the specified entry from the authenticated customer's wishlist.
     */
    public function destroy(Request $request, Wishlist $wishlist, DeleteWishlistAction $action): JsonResponse
    {
        abort_unless($wishlist->customer_email === $request->user()->email, 404);

        $action->handle($wishlist);

        return response()->json(status: 204);
    }
}
