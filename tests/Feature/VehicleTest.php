<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VehicleTest extends TestCase
{
    use RefreshDatabase;

    public function test_dashboard_displays_active_vehicles_count()
    {
        $user = User::factory()->create(['role' => 'manager']);
        Vehicle::factory()->count(3)->create(['status' => 'available']);
        Vehicle::factory()->count(2)->create(['status' => 'in_shop']);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('3'); // Active vehicles count
    }

    public function test_vehicle_index_page_can_be_rendered()
    {
        $user = User::factory()->create(['role' => 'manager']);
        Vehicle::factory()->count(5)->create();

        $response = $this->actingAs($user)->get('/vehicles');

        $response->assertStatus(200);
        $response->assertViewHas('vehicles');
    }

    public function test_vehicle_can_be_created()
    {
        $user = User::factory()->create(['role' => 'manager']);

        $response = $this->actingAs($user)->post('/vehicles', [
            'model' => 'Test Truck',
            'license_plate' => 'TEST-123',
            'max_capacity' => 1000.00,
            'odometer' => 50000.00,
            'status' => 'available',
        ]);

        $response->assertRedirect('/vehicles');
        $this->assertDatabaseHas('vehicles', [
            'model' => 'Test Truck',
            'license_plate' => 'TEST-123',
        ]);
    }

    public function test_vehicle_can_be_updated()
    {
        $user = User::factory()->create(['role' => 'manager']);
        $vehicle = Vehicle::factory()->create();

        $response = $this->actingAs($user)->put("/vehicles/{$vehicle->id}", [
            'model' => 'Updated Truck',
            'license_plate' => $vehicle->license_plate,
            'max_capacity' => $vehicle->max_capacity,
            'odometer' => $vehicle->odometer,
            'status' => 'in_shop',
        ]);

        $response->assertRedirect('/vehicles');
        $this->assertDatabaseHas('vehicles', [
            'id' => $vehicle->id,
            'model' => 'Updated Truck',
            'status' => 'in_shop',
        ]);
    }

    public function test_vehicle_can_be_deleted()
    {
        $user = User::factory()->create(['role' => 'manager']);
        $vehicle = Vehicle::factory()->create();

        $response = $this->actingAs($user)->delete("/vehicles/{$vehicle->id}");

        $response->assertRedirect('/vehicles');
        $this->assertDatabaseMissing('vehicles', [
            'id' => $vehicle->id,
        ]);
    }

    public function test_vehicle_needs_maintenance_alert()
    {
        $user = User::factory()->create(['role' => 'manager']);
        $vehicle = Vehicle::factory()->create(['odometer' => 10000]);
        
        // Create maintenance log at 4000km
        $vehicle->maintenanceLogs()->create([
            'type' => 'Oil Change',
            'description' => 'Regular oil change',
            'cost' => 75.00,
            'odometer_at_service' => 4000.00,
            'performed_at' => now()->subMonth(),
        ]);

        $response = $this->actingAs($user)->get('/dashboard');

        $response->assertStatus(200);
        $response->assertSee('Predictive Maintenance Alert');
    }
}
