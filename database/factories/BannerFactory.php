<?php

namespace Database\Factories;

use App\Models\Banner;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Banner>
 */
class BannerFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => fake()->sentence(4),
            'image' => 'def.png',
            'image_storage_type' => 'public',
            'image_alt_text' => fake()->words(3, true),
            'link_url' => fake()->url(),
            'sort_order' => fake()->numberBetween(0, 100),
            'status' => true,
        ];
    }
}
