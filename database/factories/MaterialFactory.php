<?php

namespace Database\Factories;

use App\Models\Material;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Material>
 */
class MaterialFactory extends Factory
{
    /**
     * @var array<string, string>
     */
    private const MATERIALS = [
        'NYL' => 'Nylon',
        'LEA' => 'Leather',
        'FOA' => 'Foam',
        'RUB' => 'Rubber',
        'MSH' => 'Mesh',
        'POL' => 'Polyester',
        'CTN' => 'Cotton',
    ];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $code = fake()->unique()->randomElement(array_keys(self::MATERIALS));

        return [
            'name' => self::MATERIALS[$code],
            'code' => $code,
        ];
    }
}
