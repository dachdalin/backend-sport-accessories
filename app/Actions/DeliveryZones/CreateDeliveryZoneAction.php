<?php

namespace App\Actions\DeliveryZones;

use App\Models\DeliveryZone;
use Illuminate\Support\Facades\DB;

class CreateDeliveryZoneAction
{
    /**
     * @param  array{zip_code: string, city: ?string, delivery_charge: float, status: bool}  $data
     */
    public function handle(array $data): DeliveryZone
    {
        return DB::transaction(fn () => DeliveryZone::create($data));
    }
}
