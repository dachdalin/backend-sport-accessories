<?php

namespace App\Models;

use App\Enums\TaxType;
use Database\Factories\DealOfTheDayFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class DealOfTheDay extends Model
{
    /** @use HasFactory<DealOfTheDayFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'product_id',
        'discount',
        'discount_type',
        'status',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'discount' => 0.00,
        'discount_type' => 'amount',
        'status' => false,
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
            'discount_type' => TaxType::class,
            'status' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
