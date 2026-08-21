<?php

namespace App\Http\Controllers\Backend;

use App\Actions\ProductImages\CreateProductImageAction;
use App\Actions\ProductImages\DeleteProductImageAction;
use App\Http\Controllers\Controller;
use App\Http\Requests\Backend\StoreProductImageRequest;
use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Throwable;

class ProductImageController extends Controller
{
    /**
     * Store a newly uploaded product image.
     */
    public function store(StoreProductImageRequest $request, Product $product, CreateProductImageAction $action): RedirectResponse
    {
        try {
            $action->handle($product, $request->safe()->except('image'), $request->file('image'));
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not upload the image. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Image uploaded.')]);

        return back();
    }

    /**
     * Remove the specified product image.
     */
    public function destroy(ProductImage $productImage, DeleteProductImageAction $action): RedirectResponse
    {
        try {
            $action->handle($productImage);
        } catch (Throwable $e) {
            report($e);

            Inertia::flash('toast', ['type' => 'error', 'message' => __('Could not delete the image. Please try again.')]);

            return back();
        }

        Inertia::flash('toast', ['type' => 'success', 'message' => __('Image deleted.')]);

        return back();
    }
}
