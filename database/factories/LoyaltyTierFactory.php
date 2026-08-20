<?php

namespace Database\Factories;

use App\Models\LoyaltyTier;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<LoyaltyTier>
 */
class LoyaltyTierFactory extends Factory
{
    /**
     * @var array<string, array{points: int, discount: int}>
     */
    private const TIERS = [
        'Bronze' => ['points' => 0, 'discount' => 5],
        'Silver' => ['points' => 500, 'discount' => 10],
        'Gold' => ['points' => 1500, 'discount' => 15],
        'Platinum' => ['points' => 3000, 'discount' => 20],
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement(array_keys(self::TIERS));
        $tier = self::TIERS[$name];

        return [
            'name' => $name,
            'points_required' => $tier['points'],
            'discount_percentage' => $tier['discount'],
            'status' => true,
        ];
    }
}
