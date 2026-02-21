<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vehicle;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;

class CheckMaintenanceAlerts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'maintenance:check-alerts';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for vehicles requiring maintenance and create alerts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $vehiclesNeedingMaintenance = Vehicle::whereHas('maintenanceLogs')
            ->get()
            ->filter(fn($vehicle) => $vehicle->needsMaintenance());

        $alertCount = 0;

        foreach ($vehiclesNeedingMaintenance as $vehicle) {
            // Check if alert already exists for this vehicle
            $existingAlert = Notification::where('type', 'maintenance')
                ->where('title', 'like', "%{$vehicle->license_plate}%")
                ->where('created_at', '>', Carbon::now()->subDays(7))
                ->first();

            if (!$existingAlert) {
                // Create notification for managers
                $managers = User::where('role', 'manager')->get();
                
                foreach ($managers as $manager) {
                    Notification::create([
                        'user_id' => $manager->id,
                        'title' => 'Vehicle Maintenance Due',
                        'message' => "Vehicle {$vehicle->license_plate} requires maintenance based on odometer reading ({$vehicle->odometer} km). Last service was at {$vehicle->lastMaintenance()->odometer_at_service} km.",
                        'type' => 'maintenance',
                    ]);
                }

                $alertCount++;
                $this->info("Maintenance alert created for vehicle: {$vehicle->license_plate}");
            }
        }

        if ($alertCount === 0) {
            $this->info('No new maintenance alerts needed.');
        } else {
            $this->info("Created {$alertCount} new maintenance alerts.");
        }

        return 0;
    }
}
