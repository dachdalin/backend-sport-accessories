<?php

namespace App\Http\Controllers\Api\V1;

use App\Actions\ProductSearches\LogProductSearchAction;
use App\Enums\ReviewStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\ListProductsRequest;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Product;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    /**
     * Display a paginated listing of the active products.
     *
     * Query params: `search` (matches name/code), `min_price`/`max_price` (unit price range),
     * `rating` (minimum average approved-review rating), `discounted` (true/false),
     * `in_stock` (true/false). When `search` is given, every product returned on this
     * page is logged as a search result for the trending-products ranking.
     */
    public function index(ListProductsRequest $request, LogProductSearchAction $logProductSearchAction): AnonymousResourceCollection
    {
        $products = Product::query()
            ->where('status', true)
            ->when($request->filled('search'), function (Builder $query) use ($request) {
                $search = $request->string('search')->toString();

                $query->where(function (Builder $query) use ($search) {
                    $query->where('name', 'like', "%{$search}%")
                        ->orWhere('code', 'like', "%{$search}%");
                });
            })
            ->when(
                $request->filled('min_price'),
                fn (Builder $query) => $query->where('unit_price', '>=', $request->float('min_price')),
            )
            ->when(
                $request->filled('max_price'),
                fn (Builder $query) => $query->where('unit_price', '<=', $request->float('max_price')),
            )
            ->when(
                $request->has('discounted'),
                fn (Builder $query) => $request->boolean('discounted')
                    ? $query->where('discount', '>', 0)
                    : $query->where('discount', '<=', 0),
            )
            ->when(
                $request->has('in_stock'),
                fn (Builder $query) => $request->boolean('in_stock')
                    ? $query->where('current_stock', '>', 0)
                    : $query->where('current_stock', '<=', 0),
            )
            ->withAvg(['reviews as average_rating' => fn (Builder $query) => $query->where('status', ReviewStatus::Approved)], 'rating')
            ->when($request->filled('rating'), function (Builder $query) use ($request) {
                // The bound rating value must be CAST to REAL: SQLite ranks the TEXT storage
                // class above REAL, so an uncast float binding compared against the avg()
                // subquery's REAL result would silently never match (see ProductControllerTest).
                $query->whereRaw(
                    '(select avg(rating) from reviews where reviews.product_id = products.id and reviews.status = ?) >= CAST(? AS REAL)',
                    [ReviewStatus::Approved->value, $request->float('rating')],
                );
            })
            ->with(['category', 'brand'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        if ($request->filled('search')) {
            $logProductSearchAction->handle($products->pluck('id')->all());
        }

        return ProductResource::collection($products);
    }

    /**
     * Display the specified active product.
     */
    public function show(Product $product): ProductResource
    {
        abort_unless($product->status, 404);

        return new ProductResource(
            $product
                ->loadAvg(['reviews as average_rating' => fn (Builder $query) => $query->where('status', ReviewStatus::Approved)], 'rating')
                ->load(['category', 'brand', 'images']),
        );
    }
}
