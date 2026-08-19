<?php

namespace Database\Factories;

use App\Enums\SearchFunctionVisibility;
use App\Models\SearchFunction;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SearchFunction>
 */
class SearchFunctionFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'key' => fake()->unique()->words(2, true),
            'url' => '/'.fake()->slug(),
            'visible_for' => SearchFunctionVisibility::Admin,
        ];
    }
}
