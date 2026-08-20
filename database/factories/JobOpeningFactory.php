<?php

namespace Database\Factories;

use App\Enums\EmploymentType;
use App\Models\JobOpening;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<JobOpening>
 */
class JobOpeningFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->jobTitle(),
            'department' => fake()->randomElement(['Warehouse', 'Retail', 'Marketing', 'Customer Support']),
            'location' => fake()->city(),
            'employment_type' => EmploymentType::FullTime,
            'description' => fake()->paragraphs(3, true),
            'status' => true,
        ];
    }
}
