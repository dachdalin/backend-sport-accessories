<?php

namespace App\Http\Resources\Api\V1;

use App\Models\OfflinePaymentMethod;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

/** @mixin OfflinePaymentMethod */
class OfflinePaymentMethodResource extends JsonResource
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
            'method_name' => $this->method_name,
            'method_fields' => $this->method_fields,
            'method_informations' => $this->method_informations,
        ];
    }
}
