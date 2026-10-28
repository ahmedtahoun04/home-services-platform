<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    public function definition(): array
    {
        return [
            'name' => fake()->unique()->words(3, true),
            'category' => fake()->randomElement(['Plumbing', 'Electrical', 'Cleaning', 'HVAC']),
            'description' => fake()->sentence(),
            'price' => fake()->randomFloat(2, 20, 500),
            'rating' => fake()->randomFloat(1, 3, 5),
            'is_active' => true,
        ];
    }
}
