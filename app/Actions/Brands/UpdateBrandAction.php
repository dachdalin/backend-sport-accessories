<?php

namespace App\Actions\Brands;

use App\Models\Brand;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UpdateBrandAction
{
    /**
     * @param  array{name: string, image_alt_text: ?string, status: bool}  $data
     */
    public function handle(Brand $brand, array $data, ?UploadedFile $image): Brand
    {
        $newPath = null;
        $oldPath = $brand->image;
        $oldDisk = $brand->image_storage_type;

        try {
            $brand = DB::transaction(function () use ($brand, $data, $image, &$newPath) {
                if ($image) {
                    $newPath = $image->store('brands', 'cloudinary');
                    $data['image'] = $newPath;
                    $data['image_storage_type'] = 'cloudinary';
                }

                $brand->update($data);

                return $brand;
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

        return $brand;
    }
}
