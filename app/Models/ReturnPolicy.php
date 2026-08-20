<?php

namespace App\Models;

use Database\Factories\ReturnPolicyFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ReturnPolicy extends Model
{
    /** @use HasFactory<ReturnPolicyFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'description',
        'days_allowed',
        'status',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'days_allowed' => 30,
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
            'days_allowed' => 'integer',
            'status' => 'boolean',
        ];
    }
}
