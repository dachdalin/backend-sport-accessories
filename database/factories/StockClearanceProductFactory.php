<?php

namespace Database\Factories;

use App\Models\Product;
use App\Models\StockClearanceProduct;
use App\Models\StockClearanceSetup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockClearanceProduct>
 */
class StockClearanceProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'stock_clearance_setup_id' => StockClearanceSetup::factory(),
            'product_id' => Product::factory(),
            'discount_type' => 'percent',
            'discount_amount' => fake()->randomFloat(2, 5, 50),
            'is_active' => true,
        ];
    }
}
