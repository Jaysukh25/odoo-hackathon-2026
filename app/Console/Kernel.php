<?php

namespace App\Console;

use Illuminate\Console\Scheduling\Schedule;
use Illuminate\Foundation\Console\Kernel as ConsoleKernel;

class Kernel extends ConsoleKernel
{
    /**
     * Define the application's command schedule.
     *
     * @param  \Illuminate\Console\Scheduling\Schedule  $schedule
     * @return void
     */
    protected function schedule(Schedule $schedule)
    {
        // Dashboard refresh every 5 minutes during business hours
        $schedule->command('dashboard:refresh')
            ->everyFiveMinutes()
            ->weekdays()
            ->between('8:00', '18:00')
            ->withoutOverlapping();

        // Check maintenance alerts daily at 8 AM
        $schedule->command('maintenance:check-alerts')
            ->dailyAt('08:00')
            ->withoutOverlapping();

        // Check license expiry daily at 9 AM
        $schedule->command('drivers:check-licence-expiry')
            ->dailyAt('09:00')
            ->withoutOverlapping();

        // $schedule->command('inspire')->hourly();
    }

    /**
     * Register the commands for the application.
     *
     * @return void
     */
    protected function commands()
    {
        $this->load(__DIR__.'/Commands');

        require base_path('routes/console.php');
    }
}
