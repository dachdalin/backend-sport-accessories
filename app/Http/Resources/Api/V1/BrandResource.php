<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Brand;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin Brand */
class BrandResource extends JsonResource
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
            'name' => $this->name,
            'image_url' => Storage::disk($this->image_storage_type)->url($this->image),
            'image_alt_text' => $this->image_alt_text,
        ];
    }
}
