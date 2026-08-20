<?php

namespace App\Models;

use Database\Factories\BannerFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Banner extends Model
{
    /** @use HasFactory<BannerFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'image',
        'image_storage_type',
        'image_alt_text',
        'link_url',
        'sort_order',
        'status',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'image' => 'def.png',
        'image_storage_type' => 'public',
        'sort_order' => 0,
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
            'sort_order' => 'integer',
            'status' => 'boolean',
        ];
    }
}
