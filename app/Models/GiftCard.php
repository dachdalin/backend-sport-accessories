<?php

namespace App\Models;

use Database\Factories\GiftCardFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class GiftCard extends Model
{
    /** @use HasFactory<GiftCardFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'code',
        'initial_balance',
        'balance',
        'expires_at',
        'status',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
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
            'initial_balance' => 'decimal:2',
            'balance' => 'decimal:2',
            'expires_at' => 'date',
            'status' => 'boolean',
        ];
    }
}
