<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Driver;
use App\Models\Notification;
use App\Models\User;
use Carbon\Carbon;

class CheckLicenseExpiry extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'drivers:check-licence-expiry';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check for drivers with expiring licenses and create alerts';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $driversExpiringSoon = Driver::where('license_expiry', '<=', Carbon::now()->addDays(30))
            ->where('license_expiry', '>', Carbon::now())
            ->get();

        $driversExpired = Driver::where('license_expiry', '<', Carbon::now())->get();

        $alertCount = 0;

        // Check for expiring licenses
        foreach ($driversExpiringSoon as $driver) {
            $existingAlert = Notification::where('type', 'license')
                ->where('title', 'like', "%{$driver->name}%")
                ->where('created_at', '>', Carbon::now()->subDays(7))
                ->first();

            if (!$existingAlert) {
                $managers = User::where('role', 'manager')->get();
                $safetyOfficers = User::where('role', 'safety')->get();
                
                $recipients = $managers->merge($safetyOfficers);
                
                foreach ($recipients as $recipient) {
                    Notification::create([
                        'user_id' => $recipient->id,
                        'title' => 'Driver License Expiring Soon',
                        'message' => "Driver {$driver->name}'s license expires on {$driver->license_expiry->format('M d, Y')} ({$driver->license_expiry->diffInDays(now())} days).",
                        'type' => 'license',
                    ]);
                }

                $alertCount++;
                $this->info("License expiry alert created for driver: {$driver->name}");
            }
        }

        // Check for expired licenses
        foreach ($driversExpired as $driver) {
            $existingAlert = Notification::where('type', 'license')
                ->where('title', 'like', "%{$driver->name}%")
                ->where('message', 'like', '%expired%')
                ->where('created_at', '>', Carbon::now()->subDays(7))
                ->first();

            if (!$existingAlert) {
                $managers = User::where('role', 'manager')->get();
                $safetyOfficers = User::where('role', 'safety')->get();
                
                $recipients = $managers->merge($safetyOfficers);
                
                foreach ($recipients as $recipient) {
                    Notification::create([
                        'user_id' => $recipient->id,
                        'title' => 'Driver License Expired',
                        'message' => "Driver {$driver->name}'s license expired on {$driver->license_expiry->format('M d, Y')}. This driver cannot be assigned to trips.",
                        'type' => 'license',
                    ]);
                }

                $alertCount++;
                $this->info("License expired alert created for driver: {$driver->name}");
            }
        }

        if ($alertCount === 0) {
            $this->info('No new license alerts needed.');
        } else {
            $this->info("Created {$alertCount} new license alerts.");
        }

        return 0;
    }
}
