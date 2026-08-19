<?php

namespace App\Models;

use App\Enums\SearchFunctionVisibility;
use Database\Factories\SearchFunctionFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SearchFunction extends Model
{
    /** @use HasFactory<SearchFunctionFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'key',
        'url',
        'visible_for',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'visible_for' => 'admin',
    ];

    /**
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'visible_for' => SearchFunctionVisibility::class,
        ];
    }
}
