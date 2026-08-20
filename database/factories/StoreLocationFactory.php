<?php

namespace Database\Factories;

use App\Models\StoreLocation;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<StoreLocation>
 */
class StoreLocationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->city().' Store',
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'phone' => fake()->phoneNumber(),
            'opening_hours' => '9:00 AM - 9:00 PM',
            'status' => true,
        ];
    }
}
