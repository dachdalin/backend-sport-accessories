<?php

namespace App\Models;

use Database\Factories\SoftCredentialFactory;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SoftCredential extends Model
{
    /** @use HasFactory<SoftCredentialFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'value',
    ];

    /**
     * The raw secret must never be serialized back to the client.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'value',
    ];

    /**
     * @var array<int, string>
     */
    protected $appends = [
        'is_configured',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'value' => 'encrypted',
        ];
    }

    protected function isConfigured(): Attribute
    {
        return Attribute::make(
            get: fn () => filled($this->value),
        );
    }
}
