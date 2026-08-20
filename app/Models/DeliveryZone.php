<?php

namespace App\Models;

use Database\Factories\DeliveryZoneFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    /** @use HasFactory<DeliveryZoneFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'zip_code',
        'city',
        'delivery_charge',
        'status',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'delivery_charge' => 0,
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
            'delivery_charge' => 'decimal:2',
            'status' => 'boolean',
        ];
    }
}
