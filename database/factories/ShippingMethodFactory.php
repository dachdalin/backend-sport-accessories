<?php

namespace Database\Factories;

use App\Models\ShippingMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShippingMethod>
 */
class ShippingMethodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->unique()->words(2, true).' shipping',
            'cost' => fake()->randomFloat(2, 0, 50),
            'duration' => fake()->numberBetween(1, 7).'-'.fake()->numberBetween(8, 14).' days',
            'status' => true,
            'creator_id' => 1,
            'creator_type' => 'admin',
        ];
    }
}
