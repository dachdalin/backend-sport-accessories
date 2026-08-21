<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\ShippingAddress;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<ShippingAddress>
 */
class ShippingAddressFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'customer_id' => Customer::factory(),
            'contact_person_name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'address_type' => 'home',
            'address' => fake()->streetAddress(),
            'city' => fake()->city(),
            'state' => fake()->state(),
            'zip' => fake()->postcode(),
            'country' => fake()->country(),
            'is_default' => false,
        ];
    }
}
