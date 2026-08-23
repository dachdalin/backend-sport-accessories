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
     * @param  array<int, UploadedFile>|null  $images
     */
    public function handle(array $data, ?UploadedFile $thumbnail, ?array $images = null): Product
    {
        $data['slug'] = $this->productService->generateSlug($data['name']);
        $storedPath = null;
        $storedGalleryPaths = [];

        try {
            return DB::transaction(function () use ($data, $thumbnail, $images, &$storedPath, &$storedGalleryPaths) {
                if ($thumbnail) {
                    $storedPath = $thumbnail->store('products', 'public');
                    $data['thumbnail'] = $storedPath;
                }

                $product = Product::create($data);

                foreach ($images ?? [] as $sortOrder => $image) {
                    $galleryPath = $image->store('products/gallery', 'public');
                    $storedGalleryPaths[] = $galleryPath;

                    $product->images()->create([
                        'image' => $galleryPath,
                        'image_storage_type' => 'public',
                        'sort_order' => $sortOrder,
                    ]);
                }

                return $product;
            });
        } catch (Throwable $e) {
            if ($storedPath) {
                Storage::disk('public')->delete($storedPath);
            }

            if ($storedGalleryPaths !== []) {
                Storage::disk('public')->delete($storedGalleryPaths);
            }

            throw $e;
        }
    }
}
