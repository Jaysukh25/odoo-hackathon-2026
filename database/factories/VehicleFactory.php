<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Vehicle>
 */
class VehicleFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'model' => fake()->randomElement(['Ford F-150', 'Chevrolet Silverado', 'Ram 1500', 'Toyota Tundra', 'GMC Sierra']),
            'license_plate' => strtoupper(fake()->bothify('???-###')),
            'max_capacity' => fake()->randomFloat(2, 800, 1500),
            'odometer' => fake()->randomFloat(2, 5000, 100000),
            'status' => fake()->randomElement(['available', 'in_shop', 'out_of_service']),
            'out_of_service' => fake()->boolean(20), // 20% chance of being out of service
        ];
    }
}
