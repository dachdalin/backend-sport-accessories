<?php

namespace App\Actions\StoreLocations;

use App\Models\StoreLocation;
use Illuminate\Support\Facades\DB;

class CreateStoreLocationAction
{
    /**
     * @param  array{name: string, address: string, city: string, phone: ?string, opening_hours: ?string, status: bool}  $data
     */
    public function handle(array $data): StoreLocation
    {
        return DB::transaction(fn () => StoreLocation::create($data));
    }
}
