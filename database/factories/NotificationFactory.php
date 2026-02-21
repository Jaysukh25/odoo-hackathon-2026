<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Notification>
 */
class NotificationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'title' => fake()->randomElement([
                'Vehicle Maintenance Due',
                'Driver License Expiring',
                'Trip Completed',
                'Fuel Alert',
                'Safety Issue',
                'Cost Threshold Exceeded'
            ]),
            'message' => fake()->sentence(15),
            'type' => fake()->randomElement(['maintenance', 'license', 'trip', 'fuel', 'safety', 'cost']),
            'read_at' => fake()->boolean(70) ? Carbon::now() : null, // 70% chance of being read
        ];
    }
}
