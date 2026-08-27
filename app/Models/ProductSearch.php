<?php

namespace App\Models;

use Database\Factories\ProductSearchFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * A single "this product appeared in a customer's search results" event,
 * used to compute the trending-products ranking. No updated_at — a search
 * event never changes after it's logged.
 */
class ProductSearch extends Model
{
    /** @use HasFactory<ProductSearchFactory> */
    use HasFactory;

    const UPDATED_AT = null;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'product_id',
        'created_at',
    ];

    /**
     * @return BelongsTo<Product, $this>
     */
    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }
}
