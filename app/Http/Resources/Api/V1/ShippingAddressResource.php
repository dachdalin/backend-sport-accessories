<?php

namespace App\Http\Resources\Api\V1;

use App\Models\ShippingAddress;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ShippingAddress */
class ShippingAddressResource extends JsonResource
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
            'contact_person_name' => $this->contact_person_name,
            'phone' => $this->phone,
            'address_type' => $this->address_type,
            'address' => $this->address,
            'city' => $this->city,
            'state' => $this->state,
            'zip' => $this->zip,
            'country' => $this->country,
            'is_default' => $this->is_default,
        ];
    }
}
