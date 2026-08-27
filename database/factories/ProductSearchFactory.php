<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\ProductSearch;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ProductSearch>
 */
class ProductSearchFactory extends Factory
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
            'created_at' => fake()->dateTimeBetween('-6 days', 'now'),
        ];
    }
}
