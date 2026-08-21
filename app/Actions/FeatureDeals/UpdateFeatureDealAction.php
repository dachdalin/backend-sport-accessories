<?php

namespace App\Actions\FeatureDeals;

use App\Models\FeatureDeal;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class UpdateFeatureDealAction
{
    /**
     * @param  array{url: ?string, status: bool}  $data
     */
    public function handle(FeatureDeal $featureDeal, array $data, ?UploadedFile $photo): FeatureDeal
    {
        $newPath = null;
        $oldPath = $featureDeal->photo;
        $oldDisk = $featureDeal->photo_storage_type;

        try {
            $featureDeal = DB::transaction(function () use ($featureDeal, $data, $photo, &$newPath) {
                if ($photo) {
                    $newPath = $photo->store('feature-deals', 'public');
                    $data['photo'] = $newPath;
                }

                $featureDeal->update($data);

                return $featureDeal;
            });
        } catch (Throwable $e) {
            if ($newPath) {
                Storage::disk('public')->delete($newPath);
            }

            throw $e;
        }

        if ($newPath && $oldPath !== 'def.png') {
            Storage::disk($oldDisk)->delete($oldPath);
        }

        return $featureDeal;
    }
}
