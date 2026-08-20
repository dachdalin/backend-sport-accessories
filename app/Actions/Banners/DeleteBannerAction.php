<?php

namespace App\Actions\Banners;

use App\Models\Banner;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteBannerAction
{
    public function handle(Banner $banner): void
    {
        $path = $banner->image;
        $disk = $banner->image_storage_type;

        DB::transaction(function () use ($banner) {
            $banner->delete();
        });

        if ($path !== 'def.png') {
            Storage::disk($disk)->delete($path);
        }
    }
}
