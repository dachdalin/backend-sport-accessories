<?php

namespace App\Models;

use App\Enums\TaxType;
use Database\Factories\StockClearanceProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StockClearanceProduct extends Model
{
    /** @use HasFactory<StockClearanceProductFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'stock_clearance_setup_id',
        'product_id',
        'discount_type',
        'discount_amount',
        'is_active',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'discount_type' => 'percent',
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
            'is_active' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<StockClearanceSetup, $this>
     */
    public function setup(): BelongsTo
    {
        return $this->belongsTo(StockClearanceSetup::class, 'stock_clearance_setup_id');
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
