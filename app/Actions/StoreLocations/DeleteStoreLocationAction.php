<?php

namespace App\Actions\StoreLocations;

use App\Models\StoreLocation;
use Illuminate\Support\Facades\DB;

class DeleteStoreLocationAction
{
    public function handle(StoreLocation $storeLocation): void
    {
        DB::transaction(function () use ($storeLocation) {
            $storeLocation->delete();
        });
    }
}
