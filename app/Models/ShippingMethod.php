<?php

namespace App\Models;

use Database\Factories\ShippingMethodFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ShippingMethod extends Model
{
    /** @use HasFactory<ShippingMethodFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'cost',
        'duration',
        'status',
        'creator_id',
        'creator_type',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'cost' => 0,
        'status' => true,
        'creator_type' => 'admin',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'cost' => 'decimal:2',
            'status' => 'boolean',
        ];
    }
}
