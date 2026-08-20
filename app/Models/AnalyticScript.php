<?php

namespace App\Models;

use App\Enums\AnalyticScriptType;
use Database\Factories\AnalyticScriptFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AnalyticScript extends Model
{
    /** @use HasFactory<AnalyticScriptFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'type',
        'script_id',
        'script',
        'status',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'type' => 'custom',
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
            'type' => AnalyticScriptType::class,
            'status' => 'boolean',
        ];
    }
}
