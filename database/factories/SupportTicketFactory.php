<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\SupportTicket;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SupportTicket>
 */
class SupportTicketFactory extends Factory
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
            'subject' => fake()->sentence(6),
            'type' => fake()->randomElement(['billing', 'technical', 'general']),
            'priority' => 'low',
            'description' => fake()->paragraph(),
            'attachment' => null,
            'attachment_storage_type' => 'public',
            'reply' => null,
            'status' => 'open',
        ];
    }
}
