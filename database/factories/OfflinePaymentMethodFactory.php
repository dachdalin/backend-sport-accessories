<?php

namespace Database\Factories;

use App\Models\OfflinePaymentMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<OfflinePaymentMethod>
 */
class OfflinePaymentMethodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'method_name' => fake()->unique()->company().' Transfer',
            'method_fields' => fake()->sentence(),
            'method_informations' => fake()->paragraph(),
            'status' => false,
        ];
    }
}
