<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Resources\Api\V1\ProductResource;
use App\Models\Product;
use Illuminate\Http\Resources\Json\AnonymousResourceCollection;

class ProductController extends Controller
{
    /**
     * Display a paginated listing of the active products.
     */
    public function index(): AnonymousResourceCollection
    {
        return ProductResource::collection(
            Product::query()->where('status', true)->with(['category', 'brand'])->latest()->paginate(15)->withQueryString(),
        );
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
