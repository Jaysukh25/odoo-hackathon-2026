<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Trip;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TripTest extends TestCase
{
    use RefreshDatabase;

    public function test_trip_can_be_created()
    {
        $user = User::factory()->create(['role' => 'dispatcher']);
        $vehicle = Vehicle::factory()->create(['max_capacity' => 1000]);
        $driver = Driver::factory()->create();

        $response = $this->actingAs($user)->post('/trips', [
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'origin' => 'New York',
            'destination' => 'Boston',
            'cargo_weight' => 800,
            'distance' => 350,
            'estimated_duration' => 270,
        ]);

        $response->assertRedirect('/trips');
        $this->assertDatabaseHas('trips', [
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'status' => 'draft',
        ]);
    }

    public function test_trip_cannot_exceed_vehicle_capacity()
    {
        $user = User::factory()->create(['role' => 'dispatcher']);
        $vehicle = Vehicle::factory()->create(['max_capacity' => 500]);
        $driver = Driver::factory()->create();

        $response = $this->actingAs($user)->post('/trips', [
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'origin' => 'New York',
            'destination' => 'Boston',
            'cargo_weight' => 800, // Exceeds capacity
            'distance' => 350,
            'estimated_duration' => 270,
        ]);

        $response->assertSessionHasErrors('cargo_weight');
        $this->assertDatabaseMissing('trips', [
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
        ]);
    }

    public function test_trip_can_be_dispatched()
    {
        $user = User::factory()->create(['role' => 'dispatcher']);
        $trip = Trip::factory()->create(['status' => 'draft']);

        $response = $this->actingAs($user)->post("/trips/{$trip->id}/dispatch");

        $response->assertRedirect('/trips');
        $this->assertDatabaseHas('trips', [
            'id' => $trip->id,
            'status' => 'dispatched',
        ]);
        $this->assertDatabaseHas('vehicles', [
            'id' => $trip->vehicle_id,
            'status' => 'on_trip',
        ]);
        $this->assertDatabaseHas('drivers', [
            'id' => $trip->driver_id,
            'status' => 'on_duty',
        ]);
    }

    public function test_trip_can_be_completed()
    {
        $user = User::factory()->create(['role' => 'dispatcher']);
        $trip = Trip::factory()->create(['status' => 'on_trip']);

        $response = $this->actingAs($user)->post("/trips/{$trip->id}/complete");

        $response->assertRedirect('/trips');
        $this->assertDatabaseHas('trips', [
            'id' => $trip->id,
            'status' => 'completed',
        ]);
        $this->assertDatabaseHas('vehicles', [
            'id' => $trip->vehicle_id,
            'status' => 'available',
        ]);
        $this->assertDatabaseHas('drivers', [
            'id' => $trip->driver_id,
            'status' => 'available',
        ]);
    }

    public function test_trip_can_be_cancelled()
    {
        $user = User::factory()->create(['role' => 'dispatcher']);
        $trip = Trip::factory()->create(['status' => 'draft']);

        $response = $this->actingAs($user)->post("/trips/{$trip->id}/cancel");

        $response->assertRedirect('/trips');
        $this->assertDatabaseHas('trips', [
            'id' => $trip->id,
            'status' => 'cancelled',
        ]);
    }

    public function test_trip_status_history_is_created()
    {
        $user = User::factory()->create(['role' => 'dispatcher']);
        $trip = Trip::factory()->create(['status' => 'draft']);

        $this->actingAs($user)->post("/trips/{$trip->id}/dispatch");

        $this->assertDatabaseHas('trip_status_history', [
            'trip_id' => $trip->id,
            'old_status' => 'draft',
            'new_status' => 'dispatched',
        ]);
    }
}
