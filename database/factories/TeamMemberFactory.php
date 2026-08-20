<?php

namespace Database\Factories;

use App\Models\TeamMember;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TeamMember>
 */
class TeamMemberFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'role' => fake()->jobTitle(),
            'bio' => fake()->paragraph(),
            'photo' => 'def.png',
            'photo_storage_type' => 'public',
            'photo_alt_text' => fake()->words(3, true),
            'sort_order' => fake()->numberBetween(0, 100),
            'status' => true,
        ];
    }
}
