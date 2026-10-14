<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

class CustomerFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'email' => fake()->unique()->safeEmail(),
            'phone' => '0912'.fake()->numerify('#######'),
            'company' => fake()->company(),
            'city' => fake()->randomElement(['Dubai', 'Istanbul', 'Toronto', 'Berlin']),
            'status' => 'active',
        ];
    }
}
