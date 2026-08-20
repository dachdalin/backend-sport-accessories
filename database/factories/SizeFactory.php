<?php

namespace Database\Factories;

use App\Models\Size;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Size>
 */
class SizeFactory extends Factory
{
    /**
     * @var array<string, string>
     */
    private const SIZES = [
        'XS' => 'Extra Small',
        'S' => 'Small',
        'M' => 'Medium',
        'L' => 'Large',
        'XL' => 'Extra Large',
        'XXL' => 'Double Extra Large',
        '3XL' => 'Triple Extra Large',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $code = fake()->unique()->randomElement(array_keys(self::SIZES));

        return [
            'name' => self::SIZES[$code],
            'code' => $code,
        ];
    }
}
