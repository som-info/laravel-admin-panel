<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class ProductFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->words(3, true),
            'sku' => strtoupper(fake()->unique()->bothify('PRD-####')),
            'category' => 'hardware',
            'price' => fake()->numberBetween(100, 5000) * 1000,
            'stock' => fake()->numberBetween(10, 200),
            'is_active' => true,
        ];
    }
}
