<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\MaintenanceLog;
use App\Models\FuelLog;
use App\Models\DriverScore;
use App\Models\Notification;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        // Create users
        $users = [
            [
                'name' => 'John Manager',
                'email' => 'manager@fleetflow.com',
                'password' => bcrypt('password'),
                'role' => 'manager',
            ],
            [
                'name' => 'Sarah Dispatcher',
                'email' => 'dispatcher@fleetflow.com',
                'password' => bcrypt('password'),
                'role' => 'dispatcher',
            ],
            [
                'name' => 'Mike Safety',
                'email' => 'safety@fleetflow.com',
                'password' => bcrypt('password'),
                'role' => 'safety',
            ],
            [
                'name' => 'Lisa Finance',
                'email' => 'finance@fleetflow.com',
                'password' => bcrypt('password'),
                'role' => 'finance',
            ],
        ];

        foreach ($users as $user) {
            User::create($user);
        }

        // Create vehicles
        $vehicles = [
            [
                'model' => 'Ford F-150',
                'license_plate' => 'ABC-123',
                'max_capacity' => 1000.00,
                'odometer' => 45000.00,
                'status' => 'available',
                'out_of_service' => false,
            ],
            [
                'model' => 'Chevrolet Silverado',
                'license_plate' => 'DEF-456',
                'max_capacity' => 1200.00,
                'odometer' => 32000.00,
                'status' => 'available',
                'out_of_service' => false,
            ],
            [
                'model' => 'Ram 1500',
                'license_plate' => 'GHI-789',
                'max_capacity' => 1100.00,
                'odometer' => 67000.00,
                'status' => 'in_shop',
                'out_of_service' => false,
            ],
            [
                'model' => 'Toyota Tundra',
                'license_plate' => 'JKL-012',
                'max_capacity' => 950.00,
                'odometer' => 28000.00,
                'status' => 'available',
                'out_of_service' => false,
            ],
            [
                'model' => 'GMC Sierra',
                'license_plate' => 'MNO-345',
                'max_capacity' => 1300.00,
                'odometer' => 52000.00,
                'status' => 'on_trip',
                'out_of_service' => false,
            ],
        ];

        foreach ($vehicles as $vehicle) {
            Vehicle::create($vehicle);
        }

        // Create drivers
        $drivers = [
            [
                'name' => 'Robert Johnson',
                'license_number' => 'DL123456',
                'license_expiry' => Carbon::now()->addMonths(8),
                'phone' => '+1-555-0101',
                'status' => 'available',
            ],
            [
                'name' => 'Maria Garcia',
                'license_number' => 'DL789012',
                'license_expiry' => Carbon::now()->addMonths(2),
                'phone' => '+1-555-0102',
                'status' => 'available',
            ],
            [
                'name' => 'David Smith',
                'license_number' => 'DL345678',
                'license_expiry' => Carbon::now()->subDays(15),
                'phone' => '+1-555-0103',
                'status' => 'available',
            ],
            [
                'name' => 'Jennifer Wilson',
                'license_number' => 'DL901234',
                'license_expiry' => Carbon::now()->addYears(2),
                'phone' => '+1-555-0104',
                'status' => 'on_duty',
            ],
            [
                'name' => 'Michael Brown',
                'license_number' => 'DL567890',
                'license_expiry' => Carbon::now()->addMonths(20),
                'phone' => '+1-555-0105',
                'status' => 'available',
            ],
        ];

        foreach ($drivers as $driver) {
            Driver::create($driver);
        }

        // Create trips
        $trips = [
            [
                'vehicle_id' => 1,
                'driver_id' => 1,
                'origin' => 'New York, NY',
                'destination' => 'Boston, MA',
                'cargo_weight' => 800.00,
                'distance' => 350.00,
                'estimated_duration' => 270,
                'status' => 'completed',
                'started_at' => Carbon::now()->subDays(2),
                'completed_at' => Carbon::now()->subDays(2)->addHours(5),
                'arrived_late' => false,
            ],
            [
                'vehicle_id' => 2,
                'driver_id' => 2,
                'origin' => 'Los Angeles, CA',
                'destination' => 'San Francisco, CA',
                'cargo_weight' => 950.00,
                'distance' => 620.00,
                'estimated_duration' => 420,
                'status' => 'on_trip',
                'started_at' => Carbon::now()->subHours(3),
                'completed_at' => null,
                'arrived_late' => false,
            ],
            [
                'vehicle_id' => 4,
                'driver_id' => 4,
                'origin' => 'Chicago, IL',
                'destination' => 'Detroit, MI',
                'cargo_weight' => 600.00,
                'distance' => 450.00,
                'estimated_duration' => 300,
                'status' => 'draft',
                'started_at' => null,
                'completed_at' => null,
                'arrived_late' => false,
            ],
            [
                'vehicle_id' => 5,
                'driver_id' => 5,
                'origin' => 'Houston, TX',
                'destination' => 'Dallas, TX',
                'cargo_weight' => 1100.00,
                'distance' => 390.00,
                'estimated_duration' => 240,
                'status' => 'dispatched',
                'started_at' => Carbon::now()->subHour(),
                'completed_at' => null,
                'arrived_late' => false,
            ],
            [
                'vehicle_id' => 1,
                'driver_id' => 1,
                'origin' => 'Miami, FL',
                'destination' => 'Orlando, FL',
                'cargo_weight' => 400.00,
                'distance' => 380.00,
                'estimated_duration' => 240,
                'status' => 'cancelled',
                'started_at' => null,
                'completed_at' => null,
                'arrived_late' => false,
            ],
        ];

        foreach ($trips as $trip) {
            Trip::create($trip);
        }

        // Create maintenance logs
        $maintenanceLogs = [
            [
                'vehicle_id' => 1,
                'type' => 'Oil Change',
                'description' => 'Regular oil change and filter replacement',
                'cost' => 75.00,
                'odometer_at_service' => 40000.00,
                'performed_at' => Carbon::now()->subMonths(2),
            ],
            [
                'vehicle_id' => 2,
                'type' => 'Tire Rotation',
                'description' => 'Rotated all four tires and checked pressure',
                'cost' => 50.00,
                'odometer_at_service' => 28000.00,
                'performed_at' => Carbon::now()->subMonths(1),
            ],
            [
                'vehicle_id' => 3,
                'type' => 'Brake Service',
                'description' => 'Replaced front brake pads and rotors',
                'cost' => 350.00,
                'odometer_at_service' => 65000.00,
                'performed_at' => Carbon::now()->subDays(5),
            ],
            [
                'vehicle_id' => 4,
                'type' => 'Engine Service',
                'description' => 'Major engine service including spark plugs and air filter',
                'cost' => 450.00,
                'odometer_at_service' => 25000.00,
                'performed_at' => Carbon::now()->subMonths(3),
            ],
            [
                'vehicle_id' => 5,
                'type' => 'Inspection',
                'description' => 'Annual safety inspection and emissions test',
                'cost' => 120.00,
                'odometer_at_service' => 50000.00,
                'performed_at' => Carbon::now()->subWeeks(2),
            ],
        ];

        foreach ($maintenanceLogs as $log) {
            MaintenanceLog::create($log);
        }

        // Create fuel logs
        $fuelLogs = [
            [
                'vehicle_id' => 1,
                'liters' => 75.00,
                'cost_per_liter' => 1.45,
                'cost' => 108.75,
                'odometer' => 44500.00,
                'fuel_date' => Carbon::now()->subDays(10),
            ],
            [
                'vehicle_id' => 2,
                'liters' => 85.00,
                'cost_per_liter' => 1.52,
                'cost' => 129.20,
                'odometer' => 31500.00,
                'fuel_date' => Carbon::now()->subDays(8),
            ],
            [
                'vehicle_id' => 4,
                'liters' => 65.00,
                'cost_per_liter' => 1.48,
                'cost' => 96.20,
                'odometer' => 27500.00,
                'fuel_date' => Carbon::now()->subDays(15),
            ],
            [
                'vehicle_id' => 5,
                'liters' => 95.00,
                'cost_per_liter' => 1.50,
                'cost' => 142.50,
                'odometer' => 51500.00,
                'fuel_date' => Carbon::now()->subDays(5),
            ],
            [
                'vehicle_id' => 1,
                'liters' => 80.00,
                'cost_per_liter' => 1.46,
                'cost' => 116.80,
                'odometer' => 45000.00,
                'fuel_date' => Carbon::now()->subDays(1),
            ],
        ];

        foreach ($fuelLogs as $log) {
            FuelLog::create($log);
        }

        // Create driver scores
        $driverScores = [
            [
                'driver_id' => 1,
                'score' => 8.5,
                'reason' => 'Good performance, on-time deliveries',
                'score_date' => Carbon::now()->subWeek(),
            ],
            [
                'driver_id' => 2,
                'score' => 6.0,
                'reason' => 'Average performance, some delays',
                'score_date' => Carbon::now()->subDays(3),
            ],
            [
                'driver_id' => 4,
                'score' => 9.2,
                'reason' => 'Excellent performance, perfect safety record',
                'score_date' => Carbon::now()->subDays(2),
            ],
            [
                'driver_id' => 5,
                'score' => 4.5,
                'reason' => 'Some late arrivals, needs improvement',
                'score_date' => Carbon::now()->subDays(5),
            ],
        ];

        foreach ($driverScores as $score) {
            DriverScore::create($score);
        }

        // Create notifications
        $notifications = [
            [
                'user_id' => 1,
                'title' => 'Vehicle Maintenance Due',
                'message' => 'Vehicle GHI-789 is due for maintenance based on odometer reading',
                'type' => 'maintenance',
                'read_at' => null,
            ],
            [
                'user_id' => 1,
                'title' => 'Driver License Expiring',
                'message' => 'Maria Garcia\'s license expires in 2 months',
                'type' => 'license',
                'read_at' => Carbon::now()->subHours(2),
            ],
            [
                'user_id' => 2,
                'title' => 'Trip Completed',
                'message' => 'Robert Johnson completed trip from New York to Boston',
                'type' => 'trip',
                'read_at' => null,
            ],
        ];

        foreach ($notifications as $notification) {
            Notification::create($notification);
        }
    }
}
