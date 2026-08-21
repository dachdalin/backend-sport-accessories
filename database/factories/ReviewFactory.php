<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\Review;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Review>
 */
class ReviewFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'product_id' => Product::factory(),
            'customer_name' => fake()->name(),
            'customer_email' => fake()->optional()->safeEmail(),
            'rating' => fake()->numberBetween(1, 5),
            'comment' => fake()->paragraph(),
            'admin_reply' => null,
            'status' => 'pending',
        ];
    }
}
