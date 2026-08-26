<?php

namespace App\Http\Resources\Api\V1;

use App\Models\DeliveryZone;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin DeliveryZone */
class DeliveryZoneResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'zip_code' => $this->zip_code,
            'city' => $this->city,
            'delivery_charge' => $this->delivery_charge,
        ];
    }
}
