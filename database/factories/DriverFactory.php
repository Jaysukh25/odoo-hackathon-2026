<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Driver>
 */
class DriverFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => fake()->name(),
            'license_number' => fake()->bothify('DL#######'),
            'license_expiry' => fake()->dateTimeBetween('+1 month', '+3 years'),
            'phone' => fake()->phoneNumber(),
            'status' => fake()->randomElement(['available', 'on_duty', 'off_duty']),
        ];
    }
}
