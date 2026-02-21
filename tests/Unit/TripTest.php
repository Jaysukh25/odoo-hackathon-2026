<?php

namespace Tests\Unit;

use App\Models\Trip;
use App\Models\Vehicle;
use App\Models\Driver;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class TripTest extends TestCase
{
    use RefreshDatabase;

    public function test_cargo_within_capacity()
    {
        $vehicle = Vehicle::factory()->create(['max_capacity' => 1000]);
        $trip = Trip::factory()->create([
            'vehicle_id' => $vehicle->id,
            'cargo_weight' => 800
        ]);

        $this->assertTrue($trip->cargo_within_capacity);
    }

    public function test_cargo_exceeds_capacity()
    {
        $vehicle = Vehicle::factory()->create(['max_capacity' => 1000]);
        $trip = Trip::factory()->create([
            'vehicle_id' => $vehicle->id,
            'cargo_weight' => 1200
        ]);

        $this->assertFalse($trip->cargo_within_capacity);
    }

    public function test_trip_status_update_dispatched()
    {
        $vehicle = Vehicle::factory()->create(['status' => 'available']);
        $driver = Driver::factory()->create(['status' => 'available']);
        $trip = Trip::factory()->create([
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'status' => 'draft'
        ]);

        $trip->updateStatus('dispatched');

        $this->assertEquals('dispatched', $trip->status);
        $this->assertNotNull($trip->started_at);
        $this->assertEquals('on_trip', $vehicle->fresh()->status);
        $this->assertEquals('on_duty', $driver->fresh()->status);
        
        // Check status history
        $this->assertDatabaseHas('trip_status_history', [
            'trip_id' => $trip->id,
            'old_status' => 'draft',
            'new_status' => 'dispatched',
        ]);
    }

    public function test_trip_status_update_completed()
    {
        $vehicle = Vehicle::factory()->create(['status' => 'on_trip']);
        $driver = Driver::factory()->create(['status' => 'on_duty']);
        $trip = Trip::factory()->create([
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'status' => 'on_trip',
            'started_at' => Carbon::now()->subHours(2),
            'estimated_duration' => 120 // 2 hours
        ]);

        $trip->updateStatus('completed');

        $this->assertEquals('completed', $trip->status);
        $this->assertNotNull($trip->completed_at);
        $this->assertEquals('available', $vehicle->fresh()->status);
        $this->assertEquals('available', $driver->fresh()->status);
    }

    public function test_trip_status_update_completed_late()
    {
        $vehicle = Vehicle::factory()->create(['status' => 'on_trip']);
        $driver = Driver::factory()->create(['status' => 'on_duty']);
        $trip = Trip::factory()->create([
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'status' => 'on_trip',
            'started_at' => Carbon::now()->subHours(3),
            'estimated_duration' => 120 // 2 hours, but took 3 hours
        ]);

        $trip->updateStatus('completed');

        $this->assertEquals('completed', $trip->status);
        $this->assertTrue($trip->fresh()->arrived_late);
    }

    public function test_trip_status_update_cancelled()
    {
        $vehicle = Vehicle::factory()->create(['status' => 'on_trip']);
        $driver = Driver::factory()->create(['status' => 'on_duty']);
        $trip = Trip::factory()->create([
            'vehicle_id' => $vehicle->id,
            'driver_id' => $driver->id,
            'status' => 'dispatched'
        ]);

        $trip->updateStatus('cancelled');

        $this->assertEquals('cancelled', $trip->status);
        $this->assertEquals('available', $vehicle->fresh()->status);
        $this->assertEquals('available', $driver->fresh()->status);
    }

    public function test_trip_status_color()
    {
        $draftTrip = Trip::factory()->create(['status' => 'draft']);
        $dispatchedTrip = Trip::factory()->create(['status' => 'dispatched']);
        $onTripTrip = Trip::factory()->create(['status' => 'on_trip']);
        $completedTrip = Trip::factory()->create(['status' => 'completed']);
        $cancelledTrip = Trip::factory()->create(['status' => 'cancelled']);

        $this->assertEquals('secondary', $draftTrip->status_color);
        $this->assertEquals('info', $dispatchedTrip->status_color);
        $this->assertEquals('primary', $onTripTrip->status_color);
        $this->assertEquals('success', $completedTrip->status_color);
        $this->assertEquals('danger', $cancelledTrip->status_color);
    }
}
