<?php

namespace App\Models;

use Database\Factories\MostDemandedFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class MostDemanded extends Model
{
    /** @use HasFactory<MostDemandedFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'banner',
        'banner_storage_type',
        'product_id',
        'status',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'banner' => 'def.png',
        'banner_storage_type' => 'public',
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
