<?php

namespace Database\Factories;

use App\Models\FlashDeal;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<FlashDeal>
 */
class FlashDealFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->unique()->words(3, true);

        return [
            'title' => $title,
            'slug' => Str::slug($title),
            'start_date' => fake()->dateTimeBetween('now', '+1 week')->format('Y-m-d'),
            'end_date' => fake()->dateTimeBetween('+2 weeks', '+1 month')->format('Y-m-d'),
            'status' => true,
            'featured' => false,
            'background_color' => '#ffffff',
            'text_color' => '#000000',
            'banner' => 'def.png',
            'banner_storage_type' => 'public',
        ];
    }
}
