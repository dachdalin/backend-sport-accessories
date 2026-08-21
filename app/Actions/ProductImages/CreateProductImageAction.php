<?php

namespace App\Actions\ProductImages;

use App\Models\Product;
use App\Models\ProductImage;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CreateProductImageAction
{
    /**
     * @param  array{sort_order?: int|null}  $data
     */
    public function handle(Product $product, array $data, UploadedFile $image): ProductImage
    {
        $storedPath = $image->store('products/gallery', 'public');

        try {
            return DB::transaction(function () use ($product, $data, $storedPath) {
                return $product->images()->create([
                    'image' => $storedPath,
                    'image_storage_type' => 'public',
                    'sort_order' => $data['sort_order'] ?? 0,
                ]);
            });
        } catch (Throwable $e) {
            Storage::disk('public')->delete($storedPath);

            throw $e;
        }
    }
}
