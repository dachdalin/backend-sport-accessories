<?php

namespace App\Models;

use Database\Factories\FlashDealProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FlashDealProduct extends Model
{
    /** @use HasFactory<FlashDealProductFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'flash_deal_id',
        'product_id',
        'discount',
        'discount_type',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'discount_type' => 'percent',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'discount' => 'decimal:2',
        ];
    }

    /**
     * @return BelongsTo<FlashDeal, $this>
     */
    public function flashDeal(): BelongsTo
    {
        return $this->belongsTo(FlashDeal::class);
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
