<?php

namespace Database\Factories;

use App\Models\SoftCredential;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SoftCredential>
 */
class SoftCredentialFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->regexify('[A-Z][A-Z0-9_]{5,20}'),
            'value' => fake()->sha256(),
        ];
    }
}
