<?php

namespace Database\Factories;

use App\Models\DeliveryZone;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<DeliveryZone>
 */
class DeliveryZoneFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'zip_code' => fake()->unique()->postcode(),
            'city' => fake()->city(),
            'delivery_charge' => fake()->randomFloat(2, 0, 25),
            'status' => true,
        ];
    }
}
