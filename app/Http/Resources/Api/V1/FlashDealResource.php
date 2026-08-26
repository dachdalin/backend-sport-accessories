<?php

namespace App\Http\Resources\Api\V1;

use App\Models\FlashDeal;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin FlashDeal */
class FlashDealResource extends JsonResource
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
            'slug' => $this->slug,
            'start_date' => $this->start_date?->toDateString(),
            'end_date' => $this->end_date?->toDateString(),
            'featured' => $this->featured,
            'background_color' => $this->background_color,
            'text_color' => $this->text_color,
            'banner_url' => Storage::disk($this->banner_storage_type)->url($this->banner),
            'products' => FlashDealProductResource::collection($this->whenLoaded('items')),
        ];
    }
}
