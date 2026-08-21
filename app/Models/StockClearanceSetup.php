<?php

namespace App\Models;

use App\Enums\OfferActiveTime;
use App\Enums\TaxType;
use Database\Factories\StockClearanceSetupFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StockClearanceSetup extends Model
{
    /** @use HasFactory<StockClearanceSetupFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'discount_type',
        'discount_amount',
        'offer_active_time',
        'offer_active_range_start',
        'offer_active_range_end',
        'show_in_homepage',
        'show_in_homepage_once',
        'show_in_shop',
        'is_active',
        'duration_start_date',
        'duration_end_date',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'discount_type' => 'percent',
        'offer_active_time' => 'always',
        'show_in_homepage' => false,
        'show_in_homepage_once' => false,
        'show_in_shop' => true,
        'is_active' => true,
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'discount_type' => TaxType::class,
            'discount_amount' => 'decimal:2',
            'offer_active_time' => OfferActiveTime::class,
            'show_in_homepage' => 'boolean',
            'show_in_homepage_once' => 'boolean',
            'show_in_shop' => 'boolean',
            'is_active' => 'boolean',
            'duration_start_date' => 'date',
            'duration_end_date' => 'date',
        ];
    }

    /**
     * @return HasMany<StockClearanceProduct, $this>
     */
    public function items(): HasMany
    {
        return $this->hasMany(StockClearanceProduct::class);
    }
}
