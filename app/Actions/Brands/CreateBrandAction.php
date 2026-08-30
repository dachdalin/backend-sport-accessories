<?php

namespace App\Actions\Brands;

use App\Models\Brand;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CreateBrandAction
{
    /**
     * @param  array{name: string, image_alt_text: ?string, status: bool}  $data
     */
    public function handle(array $data, ?UploadedFile $image): Brand
    {
        $storedPath = null;

        try {
            return DB::transaction(function () use ($data, $image, &$storedPath) {
                if ($image) {
                    $storedPath = $image->store('brands', 'cloudinary');
                    $data['image'] = $storedPath;
                    $data['image_storage_type'] = 'cloudinary';
                }

                return Brand::create($data);
            });
        } catch (Throwable $e) {
            if ($storedPath) {
                Storage::disk('cloudinary')->delete($storedPath);
            }

            throw $e;
        }
    }
}
