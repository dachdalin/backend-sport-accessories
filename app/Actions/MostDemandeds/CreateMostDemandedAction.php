<?php

namespace App\Actions\MostDemandeds;

use App\Models\MostDemanded;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CreateMostDemandedAction
{
    /**
     * @param  array{product_id: int, status: bool}  $data
     */
    public function handle(array $data, ?UploadedFile $banner): MostDemanded
    {
        $storedPath = null;

        try {
            return DB::transaction(function () use ($data, $banner, &$storedPath) {
                if ($banner) {
                    $storedPath = $banner->store('most-demandeds', 'cloudinary');
                    $data['banner'] = $storedPath;
                    $data['banner_storage_type'] = 'cloudinary';
                }

                return MostDemanded::create($data);
            });
        } catch (Throwable $e) {
            if ($storedPath) {
                Storage::disk('cloudinary')->delete($storedPath);
            }

            throw $e;
        }
    }
}
