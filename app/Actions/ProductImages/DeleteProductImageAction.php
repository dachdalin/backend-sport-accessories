<?php

namespace App\Actions\ProductImages;

use App\Models\ProductImage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteProductImageAction
{
    public function handle(ProductImage $productImage): void
    {
        $path = $productImage->image;
        $disk = $productImage->image_storage_type;

        DB::transaction(function () use ($productImage) {
            $productImage->delete();
        });

        Storage::disk($disk)->delete($path);
    }
}
