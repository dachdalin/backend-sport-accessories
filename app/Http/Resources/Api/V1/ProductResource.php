<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin Product */
class ProductResource extends JsonResource
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
            'slug' => $this->slug,
            'code' => $this->code,
            'description' => $this->description,
            'thumbnail_url' => Storage::disk($this->thumbnail_storage_type)->url($this->thumbnail),
            'unit_price' => $this->unit_price,
            'purchase_price' => $this->purchase_price,
            'current_stock' => $this->current_stock,
            'minimum_order_qty' => $this->minimum_order_qty,
            'tax' => $this->tax,
            'tax_type' => $this->tax_type,
            'discount' => $this->discount,
            'discount_type' => $this->discount_type,
            'free_shipping' => $this->free_shipping,
            'refundable' => $this->refundable,
            'featured' => $this->featured,
            'category' => $this->whenLoaded('category', fn () => new CategoryResource($this->category)),
            'brand' => $this->whenLoaded('brand', fn () => new BrandResource($this->brand)),
            'images' => ProductImageResource::collection($this->whenLoaded('images')),
        ];
    }
}
