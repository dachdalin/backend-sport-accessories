<?php

namespace Database\Factories;

use App\Models\Testimonial;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Testimonial>
 */
class TestimonialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_name' => fake()->name(),
            'customer_role' => fake()->jobTitle(),
            'content' => fake()->paragraph(),
            'rating' => fake()->numberBetween(1, 5),
            'avatar' => 'def.png',
            'avatar_storage_type' => 'public',
            'status' => true,
        ];
    }
}
