<?php

namespace Database\Factories;

use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Product>
 */
class ProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->words(3, true);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'code' => fake()->optional()->bothify('SKU-####'),
            'description' => fake()->optional()->paragraph(),
            'thumbnail' => 'def.png',
            'thumbnail_storage_type' => 'public',
            'unit_price' => fake()->randomFloat(2, 5, 500),
            'purchase_price' => fake()->optional()->randomFloat(2, 1, 300),
            'current_stock' => fake()->numberBetween(0, 100),
            'minimum_order_qty' => 1,
            'category_id' => null,
            'brand_id' => null,
            'tax' => 0.00,
            'tax_type' => null,
            'discount' => 0.00,
            'discount_type' => null,
            'free_shipping' => false,
            'refundable' => true,
            'featured' => false,
            'meta_title' => null,
            'meta_description' => null,
            'status' => true,
        ];
    }
}
