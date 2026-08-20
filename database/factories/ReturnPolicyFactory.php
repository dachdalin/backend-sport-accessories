<?php

namespace Database\Factories;

use App\Models\ReturnPolicy;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ReturnPolicy>
 */
class ReturnPolicyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->words(3, true).' Return Policy',
            'description' => fake()->paragraph(),
            'days_allowed' => fake()->randomElement([7, 14, 30, 60]),
            'status' => true,
        ];
    }
}
