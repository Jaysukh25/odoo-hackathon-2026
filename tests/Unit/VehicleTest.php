<?php

namespace Tests\Unit;

use App\Models\Vehicle;
use App\Models\MaintenanceLog;
use App\Models\FuelLog;
use App\Models\Trip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleTest extends TestCase
{
    use RefreshDatabase;

    public function test_vehicle_needs_maintenance()
    {
        $vehicle = Vehicle::factory()->create(['odometer' => 10000]);
        
        // Create maintenance log at 4000km
        $vehicle->maintenanceLogs()->create([
            'type' => 'Oil Change',
            'description' => 'Regular oil change',
            'cost' => 75.00,
            'odometer_at_service' => 4000.00,
            'performed_at' => now()->subMonth(),
        ]);

        $this->assertTrue($vehicle->needsMaintenance());
    }

    public function test_vehicle_does_not_need_maintenance()
    {
        $vehicle = Vehicle::factory()->create(['odometer' => 8000]);
        
        // Create maintenance log at 4000km
        $vehicle->maintenanceLogs()->create([
            'type' => 'Oil Change',
            'description' => 'Regular oil change',
            'cost' => 75.00,
            'odometer_at_service' => 4000.00,
            'performed_at' => now()->subMonth(),
        ]);

        $this->assertFalse($vehicle->needsMaintenance());
    }

    public function test_vehicle_needs_maintenance_without_previous_service()
    {
        $vehicle = Vehicle::factory()->create(['odometer' => 6000]);

        $this->assertTrue($vehicle->needsMaintenance()); // Over 5000km without service
    }

    public function test_total_fuel_cost_calculation()
    {
        $vehicle = Vehicle::factory()->create();
        
        $vehicle->fuelLogs()->createMany([
            [
                'liters' => 50,
                'cost_per_liter' => 1.50,
                'cost' => 75.00,
                'odometer' => 10000,
                'fuel_date' => now(),
            ],
            [
                'liters' => 60,
                'cost_per_liter' => 1.45,
                'cost' => 87.00,
                'odometer' => 10500,
                'fuel_date' => now(),
            ],
        ]);

        $this->assertEquals(162.00, $vehicle->total_fuel_cost);
    }

    public function test_total_maintenance_cost_calculation()
    {
        $vehicle = Vehicle::factory()->create();
        
        $vehicle->maintenanceLogs()->createMany([
            [
                'type' => 'Oil Change',
                'description' => 'Regular oil change',
                'cost' => 75.00,
                'odometer_at_service' => 10000,
                'performed_at' => now()->subMonth(),
            ],
            [
                'type' => 'Tire Rotation',
                'description' => 'Tire rotation',
                'cost' => 50.00,
                'odometer_at_service' => 15000,
                'performed_at' => now()->subWeek(),
            ],
        ]);

        $this->assertEquals(125.00, $vehicle->total_maintenance_cost);
    }

    public function test_total_operational_cost_calculation()
    {
        $vehicle = Vehicle::factory()->create();
        
        $vehicle->fuelLogs()->create([
            'liters' => 50,
            'cost_per_liter' => 1.50,
            'cost' => 75.00,
            'odometer' => 10000,
            'fuel_date' => now(),
        ]);
        
        $vehicle->maintenanceLogs()->create([
            'type' => 'Oil Change',
            'description' => 'Regular oil change',
            'cost' => 25.00,
            'odometer_at_service' => 10000,
            'performed_at' => now()->subMonth(),
        ]);

        $this->assertEquals(100.00, $vehicle->total_operational_cost);
    }

    public function test_average_fuel_efficiency_calculation()
    {
        $vehicle = Vehicle::factory()->create();
        
        // Create fuel logs and trips
        $vehicle->fuelLogs()->createMany([
            [
                'liters' => 50,
                'cost_per_liter' => 1.50,
                'cost' => 75.00,
                'odometer' => 10000,
                'fuel_date' => now()->subDays(10),
            ],
            [
                'liters' => 60,
                'cost_per_liter' => 1.45,
                'cost' => 87.00,
                'odometer' => 10500,
                'fuel_date' => now()->subDays(5),
            ],
        ]);

        $vehicle->trips()->createMany([
            [
                'driver_id' => 1,
                'origin' => 'A',
                'destination' => 'B',
                'cargo_weight' => 100,
                'distance' => 400,
                'estimated_duration' => 300,
                'status' => 'completed',
                'started_at' => now()->subDays(8),
                'completed_at' => now()->subDays(8)->addHours(5),
            ],
            [
                'driver_id' => 2,
                'origin' => 'C',
                'destination' => 'D',
                'cargo_weight' => 200,
                'distance' => 300,
                'estimated_duration' => 240,
                'status' => 'completed',
                'started_at' => now()->subDays(3),
                'completed_at' => now()->subDays(3)->addHours(4),
            ],
        ]);

        // Total distance: 700km, Total fuel: 110L
        // Efficiency: 700 / 110 = 6.36 km/L
        $expectedEfficiency = 700 / 110;
        $this->assertEquals($expectedEfficiency, $vehicle->average_fuel_efficiency);
    }
}
