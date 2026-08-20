<?php

namespace App\Models;

use Database\Factories\LoyaltyTierFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyTier extends Model
{
    /** @use HasFactory<LoyaltyTierFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'points_required',
        'discount_percentage',
        'status',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'points_required' => 0,
        'discount_percentage' => 0,
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
            'points_required' => 'integer',
            'discount_percentage' => 'integer',
            'status' => 'boolean',
        ];
    }
}
