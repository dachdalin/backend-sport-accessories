<?php

namespace Database\Factories;

use App\Models\StockClearanceSetup;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StockClearanceSetup>
 */
class StockClearanceSetupFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'discount_type' => 'percent',
            'discount_amount' => fake()->randomFloat(2, 5, 50),
            'offer_active_time' => 'always',
            'offer_active_range_start' => null,
            'offer_active_range_end' => null,
            'show_in_homepage' => true,
            'show_in_homepage_once' => false,
            'show_in_shop' => true,
            'is_active' => true,
            'duration_start_date' => fake()->dateTimeBetween('now', '+1 week')->format('Y-m-d'),
            'duration_end_date' => fake()->dateTimeBetween('+2 weeks', '+1 month')->format('Y-m-d'),
        ];
    }
}
