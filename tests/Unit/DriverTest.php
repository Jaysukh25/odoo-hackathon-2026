<?php

namespace Tests\Unit;

use App\Models\Driver;
use App\Models\Trip;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DriverTest extends TestCase
{
    use RefreshDatabase;

    public function test_license_expiring_soon()
    {
        $driver = Driver::factory()->create([
            'license_expiry' => Carbon::now()->addDays(15)
        ]);

        $this->assertTrue($driver->license_expiring_soon);
    }

    public function test_license_not_expiring_soon()
    {
        $driver = Driver::factory()->create([
            'license_expiry' => Carbon::now()->addDays(45)
        ]);

        $this->assertFalse($driver->license_expiring_soon);
    }

    public function test_license_expired()
    {
        $driver = Driver::factory()->create([
            'license_expiry' => Carbon::now()->subDays(15)
        ]);

        $this->assertTrue($driver->license_expired);
    }

    public function test_license_not_expired()
    {
        $driver = Driver::factory()->create([
            'license_expiry' => Carbon::now()->addDays(15)
        ]);

        $this->assertFalse($driver->license_expired);
    }

    public function test_risk_score_calculation_safe()
    {
        $driver = Driver::factory()->create([
            'license_expiry' => Carbon::now()->addYear()
        ]);

        // No trips, should be safe
        $this->assertEquals(0, $driver->risk_score);
        $this->assertEquals('SAFE', $driver->risk_level);
        $this->assertEquals('success', $driver->risk_color);
    }

    public function test_risk_score_calculation_moderate()
    {
        $driver = Driver::factory()->create([
            'license_expiry' => Carbon::now()->addYear()
        ]);

        // Create 3 late trips (6 points) - should be moderate
        $driver->trips()->createMany([
            [
                'vehicle_id' => 1,
                'origin' => 'A',
                'destination' => 'B',
                'cargo_weight' => 100,
                'distance' => 50,
                'estimated_duration' => 60,
                'status' => 'completed',
                'arrived_late' => true,
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
                'status' => 'completed',
                'arrived_late' => true,
                'started_at' => now()->subDays(2),
                'completed_at' => now()->subDays(2)->addHours(2),
            ],
            [
                'vehicle_id' => 3,
                'origin' => 'E',
                'destination' => 'F',
                'cargo_weight' => 150,
                'distance' => 60,
                'estimated_duration' => 75,
                'status' => 'completed',
                'arrived_late' => true,
                'started_at' => now()->subDays(3),
                'completed_at' => now()->subDays(3)->addHours(2),
            ],
        ]);

        $this->assertEquals(6, $driver->risk_score);
        $this->assertEquals('MODERATE', $driver->risk_level);
        $this->assertEquals('warning', $driver->risk_color);
    }

    public function test_risk_score_calculation_risky()
    {
        $driver = Driver::factory()->create([
            'license_expiry' => Carbon::now()->addDays(15) // License warning (5 points)
        ]);

        // Create 2 cancelled trips (6 points) + license warning (5 points) = 11 points
        $driver->trips()->createMany([
            [
                'vehicle_id' => 1,
                'origin' => 'A',
                'destination' => 'B',
                'cargo_weight' => 100,
                'distance' => 50,
                'estimated_duration' => 60,
                'status' => 'cancelled',
                'started_at' => null,
                'completed_at' => null,
            ],
            [
                'vehicle_id' => 2,
                'origin' => 'C',
                'destination' => 'D',
                'cargo_weight' => 200,
                'distance' => 75,
                'estimated_duration' => 90,
                'status' => 'cancelled',
                'started_at' => null,
                'completed_at' => null,
            ],
        ]);

        $this->assertEquals(11, $driver->risk_score);
        $this->assertEquals('RISKY', $driver->risk_level);
        $this->assertEquals('danger', $driver->risk_color);
    }

    public function test_can_be_assigned_with_valid_license()
    {
        $driver = Driver::factory()->create([
            'license_expiry' => Carbon::now()->addYear(),
            'status' => 'available'
        ]);

        $this->assertTrue($driver->canBeAssigned());
    }

    public function test_cannot_be_assigned_with_expired_license()
    {
        $driver = Driver::factory()->create([
            'license_expiry' => Carbon::now()->subDay(),
            'status' => 'available'
        ]);

        $this->assertFalse($driver->canBeAssigned());
    }

    public function test_cannot_be_assigned_when_on_duty()
    {
        $driver = Driver::factory()->create([
            'license_expiry' => Carbon::now()->addYear(),
            'status' => 'on_duty'
        ]);

        $this->assertFalse($driver->canBeAssigned());
    }

    public function test_cannot_be_assigned_with_current_trip()
    {
        $driver = Driver::factory()->create([
            'license_expiry' => Carbon::now()->addYear(),
            'status' => 'available'
        ]);

        // Create a current trip
        $driver->trips()->create([
            'vehicle_id' => 1,
            'origin' => 'A',
            'destination' => 'B',
            'cargo_weight' => 100,
            'distance' => 50,
            'estimated_duration' => 60,
            'status' => 'on_trip',
            'started_at' => now()->subHour(),
            'completed_at' => null,
        ]);

        $this->assertFalse($driver->canBeAssigned());
    }
}
