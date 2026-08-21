<?php

namespace App\Actions\FeatureDeals;

use App\Models\FeatureDeal;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Throwable;

class CreateFeatureDealAction
{
    /**
     * @param  array{url: ?string, status: bool}  $data
     */
    public function handle(array $data, ?UploadedFile $photo): FeatureDeal
    {
        $storedPath = null;

        try {
            return DB::transaction(function () use ($data, $photo, &$storedPath) {
                if ($photo) {
                    $storedPath = $photo->store('feature-deals', 'public');
                    $data['photo'] = $storedPath;
                }

                return FeatureDeal::create($data);
            });
        } catch (Throwable $e) {
            if ($storedPath) {
                Storage::disk('public')->delete($storedPath);
            }

            throw $e;
        }
    }
}
