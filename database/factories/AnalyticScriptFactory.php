<?php

namespace Database\Factories;

use App\Enums\AnalyticScriptType;
use App\Models\AnalyticScript;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<AnalyticScript>
 */
class AnalyticScriptFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(2, true).' tracking',
            'type' => AnalyticScriptType::Custom,
            'script_id' => fake()->bothify('??-#########'),
            'script' => '<script>console.log("tracking");</script>',
            'status' => false,
        ];
    }
}
