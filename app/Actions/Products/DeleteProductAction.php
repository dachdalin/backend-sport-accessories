<?php

namespace App\Actions\Products;

use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteProductAction
{
    public function handle(Product $product): void
    {
        $path = $product->thumbnail;
        $disk = $product->thumbnail_storage_type;

        DB::transaction(function () use ($product) {
            $product->delete();
        });

        if ($path !== 'def.png') {
            Storage::disk($disk)->delete($path);
        }
    }
}
