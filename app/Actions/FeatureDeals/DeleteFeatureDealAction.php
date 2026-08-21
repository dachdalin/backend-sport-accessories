<?php

namespace App\Actions\FeatureDeals;

use App\Models\FeatureDeal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteFeatureDealAction
{
    public function handle(FeatureDeal $featureDeal): void
    {
        $path = $featureDeal->photo;
        $disk = $featureDeal->photo_storage_type;

        DB::transaction(function () use ($featureDeal) {
            $featureDeal->delete();
        });

        if ($path !== 'def.png') {
            Storage::disk($disk)->delete($path);
        }
    }
}
