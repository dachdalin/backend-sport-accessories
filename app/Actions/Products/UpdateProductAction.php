<?php

namespace App\Actions\Products;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UpdateProductAction
{
    public function __construct(private readonly ProductService $productService) {}

    /**
     * @param  array{name: string, slug?: string, code: ?string, description: ?string, unit_price: string, purchase_price: ?string, current_stock: int, minimum_order_qty: int, category_id: ?int, brand_id: ?int, tax: string, tax_type: ?string, discount: string, discount_type: ?string, free_shipping: bool, refundable: bool, featured: bool, meta_title: ?string, meta_description: ?string, status: bool}  $data
     * @param  array<int, array{color_id: ?int, size_id: ?int, material_id: ?int, sku: ?string, extra_price: ?string, stock: int}>  $variants
     * @param  array<int, int>  $attributes
     */
    public function handle(Product $product, array $data, ?UploadedFile $thumbnail, array $variants = [], array $attributes = []): Product
    {
        if ($data['name'] !== $product->name) {
            $data['slug'] = $this->productService->generateSlug($data['name'], $product->id);
        }

        $newPath = null;
        $oldPath = $product->thumbnail;
        $oldDisk = $product->thumbnail_storage_type;

        try {
            $product = DB::transaction(function () use ($product, $data, $thumbnail, $variants, $attributes, &$newPath) {
                if ($thumbnail) {
                    $newPath = $thumbnail->store('products', 'public');
                    $data['thumbnail'] = $newPath;
                }

                $product->update($data);

                $product->variants()->delete();

                if ($variants !== []) {
                    $product->variants()->createMany($variants);
                }

                $product->attributes()->sync($attributes);

                return $product;
            });
        } catch (Throwable $e) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }

            throw $e;
        }

        if ($newPath && $oldPath !== 'def.png') {
            Storage::disk($oldDisk)->delete($oldPath);
        }

        return $product;
    }
}
