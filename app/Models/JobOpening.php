<?php

namespace App\Models;

use App\Enums\EmploymentType;
use Database\Factories\JobOpeningFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class JobOpening extends Model
{
    /** @use HasFactory<JobOpeningFactory> */
    use HasFactory;

    /**
     * @var array<int, string>
     */
    protected $fillable = [
        'title',
        'department',
        'location',
        'employment_type',
        'description',
        'status',
    ];

    /**
     * @var array<string, mixed>
     */
    protected $attributes = [
        'employment_type' => 'full_time',
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
            'employment_type' => EmploymentType::class,
            'status' => 'boolean',
        ];
    }
}
