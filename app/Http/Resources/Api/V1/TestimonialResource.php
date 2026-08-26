<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Testimonial;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin Testimonial */
class TestimonialResource extends JsonResource
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
            'customer_name' => $this->customer_name,
            'customer_role' => $this->customer_role,
            'content' => $this->content,
            'rating' => $this->rating,
            'avatar_url' => Storage::disk($this->avatar_storage_type)->url($this->avatar),
        ];
    }
}
