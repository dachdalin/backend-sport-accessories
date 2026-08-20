<?php

namespace App\Models;

use Database\Factories\TeamMemberFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TeamMember extends Model
{
    /** @use HasFactory<TeamMemberFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'role',
        'bio',
        'photo',
        'photo_storage_type',
        'photo_alt_text',
        'sort_order',
        'status',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'photo' => 'def.png',
        'photo_storage_type' => 'public',
        'sort_order' => 0,
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
            'sort_order' => 'integer',
            'status' => 'boolean',
        ];
    }
}
