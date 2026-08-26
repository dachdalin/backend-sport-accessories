<?php

namespace App\Http\Resources\Api\V1;

use App\Models\ShippingMethod;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin ShippingMethod */
class ShippingMethodResource extends JsonResource
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
            'title' => $this->title,
            'cost' => $this->cost,
            'duration' => $this->duration,
        ];
    }
}
