<?php

namespace App\Models;

use Database\Factories\FeatureDealFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FeatureDeal extends Model
{
    /** @use HasFactory<FeatureDealFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'photo',
        'photo_storage_type',
        'url',
        'status',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'photo' => 'def.png',
        'photo_storage_type' => 'public',
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
