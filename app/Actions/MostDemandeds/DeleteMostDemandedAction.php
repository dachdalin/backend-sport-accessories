<?php

namespace App\Actions\MostDemandeds;

use App\Models\MostDemanded;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteMostDemandedAction
{
    public function handle(MostDemanded $mostDemanded): void
    {
        $path = $mostDemanded->banner;
        $disk = $mostDemanded->banner_storage_type;

        DB::transaction(function () use ($mostDemanded) {
            $mostDemanded->delete();
        });

        if ($path !== 'def.png') {
            Storage::disk($disk)->delete($path);
        }
    }
}
