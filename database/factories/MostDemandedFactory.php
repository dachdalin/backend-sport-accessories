<?php

namespace Database\Factories;

use App\Models\MostDemanded;
use App\Models\Product;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<MostDemanded>
 */
class MostDemandedFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'banner' => 'def.png',
            'banner_storage_type' => 'public',
            'product_id' => Product::factory(),
            'status' => false,
        ];
    }
}
