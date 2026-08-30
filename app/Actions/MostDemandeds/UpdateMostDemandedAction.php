<?php

namespace App\Actions\MostDemandeds;

use App\Models\MostDemanded;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UpdateMostDemandedAction
{
    /**
     * @param  array{product_id: int, status: bool}  $data
     */
    public function handle(MostDemanded $mostDemanded, array $data, ?UploadedFile $banner): MostDemanded
    {
        $newPath = null;
        $oldPath = $mostDemanded->banner;
        $oldDisk = $mostDemanded->banner_storage_type;

        try {
            $mostDemanded = DB::transaction(function () use ($mostDemanded, $data, $banner, &$newPath) {
                if ($banner) {
                    $newPath = $banner->store('most-demandeds', 'cloudinary');
                    $data['banner'] = $newPath;
                    $data['banner_storage_type'] = 'cloudinary';
                }

                $mostDemanded->update($data);

                return $mostDemanded;
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

        return $mostDemanded;
    }
}
