<?php

namespace Database\Factories;

use App\Models\Customer;
use App\Models\PasswordResetCode;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;

/**
 * @extends Factory<PasswordResetCode>
 */
class PasswordResetCodeFactory extends Factory
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
            'channel' => 'email',
            'code' => Hash::make((string) $this->faker->numberBetween(100000, 999999)),
            'expires_at' => now()->addMinutes(10),
            'used_at' => null,
        ];
    }
}
