<?php

namespace App\Http\Resources\Api\V1;

use App\Models\Blog;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

/** @mixin Blog */
class BlogResource extends JsonResource
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
            'writer' => $this->writer,
            'description' => $this->description,
            'image_url' => Storage::disk($this->image_storage_type)->url($this->image),
            'image_alt_text' => $this->image_alt_text,
            'published_at' => $this->published_at,
            'category' => $this->whenLoaded('category', fn () => new BlogCategoryResource($this->category)),
        ];
    }
}
