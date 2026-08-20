<?php

namespace App\Actions\DeliveryZones;

use App\Models\DeliveryZone;
use Illuminate\Support\Facades\DB;

class DeleteDeliveryZoneAction
{
    public function handle(DeliveryZone $deliveryZone): void
    {
        DB::transaction(function () use ($deliveryZone) {
            $deliveryZone->delete();
        });
    }
}
