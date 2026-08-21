<?php

namespace Database\Factories;

use App\Models\FlashDeal;
use App\Models\FlashDealProduct;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FlashDealProduct>
 */
class FlashDealProductFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'flash_deal_id' => FlashDeal::factory(),
            'product_id' => Product::factory(),
            'discount' => fake()->randomFloat(2, 5, 50),
            'discount_type' => 'percent',
        ];
    }
}
