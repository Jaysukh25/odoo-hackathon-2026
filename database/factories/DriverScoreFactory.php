<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DriverScore>
 */
class DriverScoreFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'driver_id' => Driver::factory(),
            'score' => fake()->randomFloat(1, 1.0, 10.0),
            'reason' => fake()->randomElement([
                'Good performance, on-time deliveries',
                'Average performance, some delays',
                'Excellent performance, perfect safety record',
                'Some late arrivals, needs improvement',
                'Consistent reliable service',
                'Minor issues with route planning'
            ]),
            'score_date' => fake()->dateTimeBetween('-30 days', 'now'),
        ];
    }
}
