<?php

namespace App\Actions\FlashDeals;

use App\Models\FlashDeal;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class DeleteFlashDealAction
{
    public function handle(FlashDeal $flashDeal): void
    {
        $path = $flashDeal->banner;
        $disk = $flashDeal->banner_storage_type;

        DB::transaction(function () use ($flashDeal) {
            $flashDeal->delete();
        });

        if ($path !== 'def.png') {
            Storage::disk($disk)->delete($path);
        }
    }
}
