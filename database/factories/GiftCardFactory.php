<?php

namespace Database\Factories;

use App\Models\GiftCard;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GiftCard>
 */
class GiftCardFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $initialBalance = fake()->randomFloat(2, 10, 200);

        return [
            'code' => strtoupper(fake()->unique()->bothify('GIFT-####-????')),
            'initial_balance' => $initialBalance,
            'balance' => $initialBalance,
            'expires_at' => fake()->dateTimeBetween('+1 month', '+1 year')->format('Y-m-d'),
            'status' => true,
        ];
    }
}
