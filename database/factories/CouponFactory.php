<?php

namespace Database\Factories;

use App\Enums\CouponType;
use App\Models\Coupon;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Coupon>
 */
class CouponFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'code' => strtoupper(fake()->unique()->bothify('SAVE##??')),
            'type' => CouponType::Fixed,
            'value' => fake()->randomFloat(2, 5, 50),
            'min_order_amount' => fake()->randomFloat(2, 20, 100),
            'usage_limit' => fake()->numberBetween(10, 500),
            'expires_at' => fake()->dateTimeBetween('+1 week', '+6 months')->format('Y-m-d'),
            'status' => true,
        ];
    }
}
