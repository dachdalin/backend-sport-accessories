<?php

namespace Database\Factories;

use App\Models\FeatureDeal;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<FeatureDeal>
 */
class FeatureDealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'photo' => 'def.png',
            'photo_storage_type' => 'public',
            'url' => fake()->optional()->url(),
            'status' => true,
        ];
    }
}
