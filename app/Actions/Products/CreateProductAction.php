<?php

namespace App\Actions\Products;

use App\Models\Product;
use App\Services\ProductService;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CreateProductAction
{
    public function __construct(private readonly ProductService $productService) {}

    /**
     * @param  array{name: string, slug?: string, code: ?string, description: ?string, unit_price: string, purchase_price: ?string, current_stock: int, minimum_order_qty: int, category_id: ?int, brand_id: ?int, tax: string, tax_type: ?string, discount: string, discount_type: ?string, free_shipping: bool, refundable: bool, featured: bool, meta_title: ?string, meta_description: ?string, status: bool}  $data
     */
    public function handle(array $data, ?UploadedFile $thumbnail): Product
    {
        $data['slug'] = $this->productService->generateSlug($data['name']);
        $storedPath = null;

        try {
            return DB::transaction(function () use ($data, $thumbnail, &$storedPath) {
                if ($thumbnail) {
                    $storedPath = $thumbnail->store('products', 'public');
                    $data['thumbnail'] = $storedPath;
                }

                return Product::create($data);
            });
        } catch (Throwable $e) {
            if ($storedPath) {
                Storage::disk('public')->delete($storedPath);
            }

            throw $e;
        }
    }
}
