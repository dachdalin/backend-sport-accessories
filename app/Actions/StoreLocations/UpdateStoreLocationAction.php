<?php

namespace App\Actions\StoreLocations;

use App\Models\StoreLocation;
use Illuminate\Support\Facades\DB;

class UpdateStoreLocationAction
{
    /**
     * @param  array{name: string, address: string, city: string, phone: ?string, opening_hours: ?string, status: bool}  $data
     */
    public function handle(StoreLocation $storeLocation, array $data): StoreLocation
    {
        DB::transaction(function () use ($storeLocation, $data) {
            $storeLocation->update($data);
        });

        return $storeLocation;
    }
}
