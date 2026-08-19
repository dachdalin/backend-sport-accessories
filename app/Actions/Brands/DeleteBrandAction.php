<?php

namespace App\Actions\Brands;

use App\Models\Brand;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteBrandAction
{
    public function handle(Brand $brand): void
    {
        $path = $brand->image;
        $disk = $brand->image_storage_type;

        DB::transaction(function () use ($brand) {
            $brand->delete();
        });

        if ($path !== 'def.png') {
            Storage::disk($disk)->delete($path);
        }
    }
}
