<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Trip>
 */
class TripFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $status = fake()->randomElement(['draft', 'dispatched', 'on_trip', 'completed', 'cancelled']);
        $startedAt = $status === 'draft' ? null : fake()->dateTimeBetween('-30 days', 'now');
        $completedAt = in_array($status, ['completed', 'cancelled']) ? 
            fake()->dateTimeBetween($startedAt ?? '-30 days', 'now') : null;
        
        return [
            'vehicle_id' => Vehicle::factory(),
            'driver_id' => Driver::factory(),
            'origin' => fake()->city(),
            'destination' => fake()->city(),
            'cargo_weight' => fake()->randomFloat(2, 100, 1200),
            'distance' => fake()->randomFloat(2, 50, 800),
            'estimated_duration' => fake()->numberBetween(60, 600),
            'status' => $status,
            'started_at' => $startedAt,
            'completed_at' => $completedAt,
            'arrived_late' => $status === 'completed' ? fake()->boolean(30) : false, // 30% chance of being late
        ];
    }
}
