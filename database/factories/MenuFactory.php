<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Menu>
 */
class MenuFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->sentence(mt_rand(2,8)),
            'slug' => fake()->slug(),
            'description' => fake()->paragraph(1),
            'price' => mt_rand(10000, 50000),
            'stock' => fake()->boolean(),
        ];
    }
}
