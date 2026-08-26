<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    /**
     * Display a paginated listing of the active products.
     *
     * Pass `discounted=1` to list only products currently carrying a discount.
     */
    public function index(Request $request): AnonymousResourceCollection
    {
        $products = Product::query()
            ->where('status', true)
            ->when($request->boolean('discounted'), fn ($query) => $query->where('discount', '>', 0))
            ->with(['category', 'brand'])
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return ProductResource::collection($products);
    }

    /**
     * Display the specified active product.
     */
    public function show(Product $product): ProductResource
    {
        abort_unless($product->status, 404);

        return new ProductResource($product->load(['category', 'brand', 'images']));
    }
}
