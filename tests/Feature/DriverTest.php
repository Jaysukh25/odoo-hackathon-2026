<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Driver;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverTest extends TestCase
{
    use RefreshDatabase;

    public function test_driver_license_expiry_warning()
    {
        $user = User::factory()->create(['role' => 'safety']);
        $driver = Driver::factory()->create([
            'license_expiry' => Carbon::now()->addDays(15) // Within 30 days
        ]);

        $response = $this->actingAs($user)->get("/drivers/{$driver->id}");

        $response->assertStatus(200);
        $response->assertSee('License Expiring Soon');
    }

    public function test_driver_license_expired()
    {
        $user = User::factory()->create(['role' => 'safety']);
        $driver = Driver::factory()->create([
            'license_expiry' => Carbon::now()->subDays(15) // Expired
        ]);

        $response = $this->actingAs($user)->get("/drivers/{$driver->id}");

        $response->assertStatus(200);
        $response->assertSee('Expired');
    }

    public function test_driver_risk_score_calculation()
    {
        $user = User::factory()->create(['role' => 'safety']);
        $driver = Driver::factory()->create();

        // Create trips that affect risk score
        $driver->trips()->createMany([
            [
                'vehicle_id' => 1,
                'origin' => 'A',
                'destination' => 'B',
                'cargo_weight' => 100,
                'distance' => 50,
                'estimated_duration' => 60,
                'status' => 'completed',
                'arrived_late' => true, // Late trip (2 points)
                'started_at' => now()->subDay(),
                'completed_at' => now()->subDay()->addHours(2),
            ],
            [
                'vehicle_id' => 2,
                'origin' => 'C',
                'destination' => 'D',
                'cargo_weight' => 200,
                'distance' => 75,
                'estimated_duration' => 90,
                'status' => 'cancelled', // Cancelled trip (3 points)
                'started_at' => null,
                'completed_at' => null,
            ],
        ]);

        $response = $this->actingAs($user)->get("/drivers/{$driver->id}");

        $response->assertStatus(200);
        $response->assertSee('MODERATE'); // Should be MODERATE risk level (5 points)
    }

    public function test_driver_can_be_assigned()
    {
        $driver = Driver::factory()->create([
            'license_expiry' => Carbon::now()->addYear(),
            'status' => 'available'
        ]);

        $this->assertTrue($driver->canBeAssigned());
    }

    public function test_driver_cannot_be_assigned_with_expired_license()
    {
        $driver = Driver::factory()->create([
            'license_expiry' => Carbon::now()->subDay(),
            'status' => 'available'
        ]);

        $this->assertFalse($driver->canBeAssigned());
    }

    public function test_driver_cannot_be_assigned_when_on_duty()
    {
        $driver = Driver::factory()->create([
            'license_expiry' => Carbon::now()->addYear(),
            'status' => 'on_duty'
        ]);

        $this->assertFalse($driver->canBeAssigned());
    }
}
