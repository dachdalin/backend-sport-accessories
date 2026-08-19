<?php

namespace Database\Factories;

use App\Models\HelpTopic;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<HelpTopic>
 */
class HelpTopicFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'type' => 'default',
            'question' => fake()->sentence().'?',
            'answer' => fake()->paragraph(),
            'ranking' => fake()->numberBetween(1, 100),
            'status' => true,
        ];
    }
}
