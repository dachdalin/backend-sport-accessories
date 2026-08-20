<?php

namespace App\Actions\DeliveryZones;

use App\Models\DeliveryZone;
use Illuminate\Support\Facades\DB;

class UpdateDeliveryZoneAction
{
    /**
     * @param  array{zip_code: string, city: ?string, delivery_charge: float, status: bool}  $data
     */
    public function handle(DeliveryZone $deliveryZone, array $data): DeliveryZone
    {
        return DB::transaction(function () use ($deliveryZone, $data) {
            $deliveryZone->update($data);

            return $deliveryZone;
        });
    }
}
