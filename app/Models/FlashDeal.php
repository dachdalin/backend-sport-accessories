<?php

namespace App\Models;

use Database\Factories\FlashDealFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class FlashDeal extends Model
{
    /** @use HasFactory<FlashDealFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'slug',
        'start_date',
        'end_date',
        'status',
        'featured',
        'background_color',
        'text_color',
        'banner',
        'banner_storage_type',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => true,
        'featured' => false,
        'banner' => 'def.png',
        'banner_storage_type' => 'public',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'start_date' => 'date',
            'end_date' => 'date',
            'status' => 'boolean',
            'featured' => 'boolean',
        ];
    }

    /**
     * @return HasMany<FlashDealProduct, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(FlashDealProduct::class);
    }
}
