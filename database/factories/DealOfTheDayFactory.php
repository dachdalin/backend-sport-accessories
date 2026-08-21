<?php

namespace Database\Factories;

use App\Models\DealOfTheDay;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DealOfTheDay>
 */
class DealOfTheDayFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'product_id' => Product::factory(),
            'discount' => fake()->randomFloat(2, 5, 50),
            'discount_type' => fake()->randomElement(['percent', 'amount']),
            'status' => false,
        ];
    }
}
