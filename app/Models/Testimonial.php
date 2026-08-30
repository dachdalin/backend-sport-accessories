<?php

namespace App\Models;

use Database\Factories\TestimonialFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class Testimonial extends Model
{
    /** @use HasFactory<TestimonialFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_name',
        'customer_role',
        'content',
        'rating',
        'avatar',
        'avatar_storage_type',
        'status',
    ];

    /**
     * @var array<int, string>
     */
    protected $appends = [
        'avatar_url',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'rating' => 5,
        'avatar' => 'def.png',
        'avatar_storage_type' => 'public',
        'status' => true,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'rating' => 'integer',
            'status' => 'boolean',
        ];
    }

    /**
     * The publicly reachable URL for the avatar, resolved against
     * whichever disk it was stored on (local `public` or `cloudinary`).
     */
    protected function avatarUrl(): Attribute
    {
        return Attribute::make(
            get: fn (): string => Storage::disk($this->avatar_storage_type)->url($this->avatar),
        );
    }
}
