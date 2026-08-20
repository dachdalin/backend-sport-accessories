<?php

namespace App\Models;

use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Product extends Model
{
    /** @use HasFactory<ProductFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'slug',
        'code',
        'description',
        'thumbnail',
        'thumbnail_storage_type',
        'unit_price',
        'purchase_price',
        'current_stock',
        'minimum_order_qty',
        'category_id',
        'brand_id',
        'tax',
        'tax_type',
        'discount',
        'discount_type',
        'free_shipping',
        'refundable',
        'featured',
        'meta_title',
        'meta_description',
        'status',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'thumbnail' => 'def.png',
        'thumbnail_storage_type' => 'public',
        'current_stock' => 0,
        'minimum_order_qty' => 1,
        'tax' => 0.00,
        'discount' => 0.00,
        'free_shipping' => false,
        'refundable' => true,
        'featured' => false,
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
            'unit_price' => 'decimal:2',
            'purchase_price' => 'decimal:2',
            'tax' => 'decimal:2',
            'discount' => 'decimal:2',
            'free_shipping' => 'boolean',
            'refundable' => 'boolean',
            'featured' => 'boolean',
            'status' => 'boolean',
        ];
    }

    /**
     * @return BelongsTo<Category, $this>
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * @return BelongsTo<Brand, $this>
     */
    public function brand(): BelongsTo
    {
        return $this->belongsTo(Brand::class);
    }
}
