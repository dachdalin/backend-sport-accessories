<?php

namespace App\Models;

use Database\Factories\CustomerFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Customer extends Authenticatable
{
    /** @use HasFactory<CustomerFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'google_id',
        'telegram_id',
        'phone',
        'address',
        'balance',
        'status',
    ];

    /**
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'status' => true,
        'balance' => 0.00,
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
            'password' => 'hashed',
            'balance' => 'decimal:2',
        ];
    }

    /**
     * The login source this customer authenticates with, derived from which
     * social id column is set. Read-only — accounts don't switch providers.
     *
     * @return Attribute<string, never>
     */
    protected function provider(): Attribute
    {
        return Attribute::make(
            get: fn (): string => match (true) {
                $this->google_id !== null => 'google',
                $this->telegram_id !== null => 'telegram',
                default => 'manual',
            },
        );
    }

    /**
     * The identifier the provider above knows this customer by.
     *
     * @return Attribute<?string, never>
     */
    protected function providerId(): Attribute
    {
        return Attribute::make(
            get: fn (): ?string => $this->google_id ?? $this->telegram_id,
        );
    }

    /**
     * @return HasMany<ShippingAddress, $this>
     */
    public function shippingAddresses(): HasMany
    {
        return $this->hasMany(ShippingAddress::class);
    }

    /**
     * @return HasMany<CartItem, $this>
     */
    public function cartItems(): HasMany
    {
        return $this->hasMany(CartItem::class);
    }

    /**
     * @return HasMany<Order, $this>
     */
    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    /**
     * @return HasMany<PasswordResetCode, $this>
     */
    public function passwordResetCodes(): HasMany
    {
        return $this->hasMany(PasswordResetCode::class);
    }
}
