<?php

namespace App\Actions\Banners;

use App\Models\Banner;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UpdateBannerAction
{
    /**
     * @param  array{title: string, image_alt_text: ?string, link_url: ?string, sort_order?: int, status: bool}  $data
     */
    public function handle(Banner $banner, array $data, ?UploadedFile $image): Banner
    {
        $newPath = null;
        $oldPath = $banner->image;
        $oldDisk = $banner->image_storage_type;

        try {
            $banner = DB::transaction(function () use ($banner, $data, $image, &$newPath) {
                if ($image) {
                    $newPath = $image->store('banners', 'cloudinary');
                    $data['image'] = $newPath;
                    $data['image_storage_type'] = 'cloudinary';
                }

                $banner->update($data);

                return $banner;
            });
        } catch (Throwable $e) {
            if ($newPath) {
                Storage::disk('cloudinary')->delete($newPath);
            }

            throw $e;
        }

        if ($newPath && $oldPath !== 'def.png') {
            Storage::disk($oldDisk)->delete($oldPath);
        }

        return $banner;
    }
}
