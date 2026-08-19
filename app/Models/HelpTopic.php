<?php

namespace App\Models;

use Database\Factories\HelpTopicFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class HelpTopic extends Model
{
    /** @use HasFactory<HelpTopicFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'type',
        'question',
        'answer',
        'ranking',
        'status',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'type' => 'default',
        'ranking' => 1,
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
            'ranking' => 'integer',
            'status' => 'boolean',
        ];
    }
}
