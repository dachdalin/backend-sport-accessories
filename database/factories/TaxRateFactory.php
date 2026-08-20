<?php

namespace Database\Factories;

use App\Models\TaxRate;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TaxRate>
 */
class TaxRateFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true).' tax',
            'region' => fake()->country(),
            'rate' => fake()->randomFloat(2, 1, 25),
            'is_default' => false,
            'status' => true,
        ];
    }
}
