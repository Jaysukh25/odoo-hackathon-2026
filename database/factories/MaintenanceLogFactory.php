<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\MaintenanceLog>
 */
class MaintenanceLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'vehicle_id' => Vehicle::factory(),
            'type' => fake()->randomElement(['Oil Change', 'Tire Rotation', 'Brake Service', 'Engine Service', 'Transmission', 'Electrical', 'Inspection', 'Other']),
            'description' => fake()->sentence(10),
            'cost' => fake()->randomFloat(2, 50, 500),
            'odometer_at_service' => fake()->randomFloat(2, 5000, 100000),
            'performed_at' => fake()->dateTimeBetween('-6 months', 'now'),
        ];
    }
}
