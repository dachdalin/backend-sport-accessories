<?php

namespace App\Models;

use App\Enums\TaxType;
use Database\Factories\ProductFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
     * The unit price after the product's own discount is applied.
     *
     * @return Attribute<float, never>
     */
    protected function finalPrice(): Attribute
    {
        return Attribute::make(
            get: function (): float {
                $price = (float) $this->unit_price;
                $discount = (float) $this->discount;

                if ($discount <= 0) {
                    return round($price, 2);
                }

                $reduced = $this->discount_type === TaxType::Percent->value
                    ? $price - ($price * $discount / 100)
                    : $price - $discount;

                return round(max(0, $reduced), 2);
            },
        );
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

    /**
     * @return HasMany<ProductImage, $this>
     */
    public function images(): HasMany
    {
        return $this->hasMany(ProductImage::class)->orderBy('sort_order');
    }

    /**
     * @return HasMany<Review, $this>
     */
    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class);
    }

    /**
     * @return HasMany<Wishlist, $this>
     */
    public function wishlists(): HasMany
    {
        return $this->hasMany(Wishlist::class);
    }

    /**
     * @return HasMany<CartItem, $this>
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }
}
