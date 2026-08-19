<?php

namespace App\Models;

use Database\Factories\SocialMediaFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SocialMedia extends Model
{
    /** @use HasFactory<SocialMediaFactory> */
    use HasFactory;

    /**
     * Eloquent pluralizes "SocialMedia" as "social_media" (an uncountable
     * noun), which doesn't match the "social_medias" table used across
     * this app's routes and other resources.
     */
    protected $table = 'social_medias';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'link',
        'icon',
        'status',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
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
            'status' => 'boolean',
        ];
    }
}
