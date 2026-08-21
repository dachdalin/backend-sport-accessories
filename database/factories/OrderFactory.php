<?php

namespace Database\Factories;

use App\Models\Order;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Order>
 */
class OrderFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_number' => 'ORD-'.strtoupper(Str::random(8)),
            'customer_name' => fake()->name(),
            'customer_email' => fake()->optional()->safeEmail(),
            'customer_phone' => fake()->optional()->phoneNumber(),
            'shipping_address' => fake()->address(),
            'order_status' => 'pending',
            'payment_status' => 'unpaid',
            'payment_method' => fake()->optional()->randomElement(['cod', 'card', 'bank_transfer']),
            'discount_amount' => 0.00,
            'discount_type' => null,
            'shipping_cost' => fake()->randomFloat(2, 0, 20),
            'order_amount' => fake()->randomFloat(2, 10, 500),
            'order_note' => fake()->optional()->sentence(),
        ];
    }
}
