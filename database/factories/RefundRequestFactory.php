<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\RefundRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<RefundRequest>
 */
class RefundRequestFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'order_id' => Order::factory(),
            'order_item_id' => null,
            'amount' => fake()->randomFloat(2, 5, 200),
            'reason' => fake()->sentence(10),
            'status' => 'pending',
            'admin_note' => null,
        ];
    }
}
