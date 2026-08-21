<?php

namespace App\Http\Controllers\Backend;

use App\Actions\Wishlists\CreateWishlistAction;
use App\Actions\Wishlists\DeleteWishlistAction;
use App\Actions\Wishlists\UpdateWishlistAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreWishlistRequest;
use App\Http\Requests\Backend\UpdateWishlistRequest;
use App\Models\Product;
use App\Models\Wishlist;
use App\Services\WishlistService;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;
use Throwable;

class WishlistController extends Controller
{
    public function __construct(private readonly WishlistService $wishlistService) {}

    /**
     * Display a listing of the wishlist entries.
     */
    public function index(): Response
    {
        return Inertia::render('wishlists/Index', [
            'wishlists' => $this->wishlistService->list(),
            'products' => $this->productOptions(),
        ]);
    }

    /**
     * Store a newly created wishlist entry.
     */
    public function store(StoreWishlistRequest $request, CreateWishlistAction $action): RedirectResponse
    {
        try {
            $action->handle($request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not create the wishlist entry. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Wishlist entry created.')]);

        return to_route('wishlists.index');
    }

    /**
     * Update the specified wishlist entry.
     */
    public function update(UpdateWishlistRequest $request, Wishlist $wishlist, UpdateWishlistAction $action): RedirectResponse
    {
        try {
            $action->handle($wishlist, $request->validated());
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not update the wishlist entry. Please try again.')]);

            return back()->withInput();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Wishlist entry updated.')]);

        return to_route('wishlists.index');
    }

    /**
     * Remove the specified wishlist entry.
     */
    public function destroy(Wishlist $wishlist, DeleteWishlistAction $action): RedirectResponse
    {
        try {
            $action->handle($wishlist);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the wishlist entry. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Wishlist entry deleted.')]);

        return to_route('wishlists.index');
    }

    /**
     * @return array<int, array{value: int, label: string}>
     */
    private function productOptions(): array
    {
        return Product::query()
            ->orderBy('name')
            ->get(['id', 'name'])
            ->map(fn (Product $product) => ['value' => $product->id, 'label' => $product->name])
            ->all();
    }
}
