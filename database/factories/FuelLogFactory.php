<?php

namespace Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Carbon\Carbon;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\FuelLog>
 */
class FuelLogFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $liters = fake()->randomFloat(2, 30, 120);
        $costPerLiter = fake()->randomFloat(2, 1.20, 1.80);
        
        return [
            'vehicle_id' => Vehicle::factory(),
            'liters' => $liters,
            'cost_per_liter' => $costPerLiter,
            'cost' => $liters * $costPerLiter,
            'odometer' => fake()->randomFloat(2, 5000, 100000),
            'fuel_date' => fake()->dateTimeBetween('-3 months', 'now'),
        ];
    }
}
