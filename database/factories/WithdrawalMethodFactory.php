<?php

namespace Database\Factories;

use App\Models\WithdrawalMethod;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<WithdrawalMethod>
 */
class WithdrawalMethodFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'method_name' => fake()->unique()->company().' Payout',
            'method_fields' => fake()->sentence(),
            'is_default' => false,
            'status' => true,
        ];
    }
}
