<?php

namespace Database\Factories;

use App\Models\Currency;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Currency>
 */
class CurrencyFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => ucfirst(fake()->unique()->words(2, true)).' Dollar',
            'symbol' => fake()->randomElement(['$', '€', '£', '¥']),
            'code' => fake()->unique()->currencyCode(),
            'exchange_rate' => fake()->randomFloat(4, 0.01, 100),
            'status' => false,
        ];
    }
}
