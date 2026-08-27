<?php

namespace App\Models;

use Database\Factories\PasswordResetCodeFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PasswordResetCode extends Model
{
    /** @use HasFactory<PasswordResetCodeFactory> */
    use HasFactory;

    /**
     * @var string
     */
    protected $table = 'customer_password_resets';

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'customer_id',
        'channel',
        'code',
        'expires_at',
        'used_at',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'expires_at' => 'datetime',
            'used_at' => 'datetime',
        ];
    }

    /**
     * @return BelongsTo<Customer, $this>
     */
    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
