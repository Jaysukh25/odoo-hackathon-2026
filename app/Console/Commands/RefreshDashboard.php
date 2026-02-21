<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Vehicle;
use App\Models\Trip;
use App\Models\MaintenanceLog;
use App\Models\FuelLog;
use Carbon\Carbon;

class RefreshDashboard extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'dashboard:refresh';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Refresh dashboard data with latest statistics';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $activeVehicles = Vehicle::where('status', 'available')->count();
        $tripsToday = Trip::whereDate('created_at', Carbon::today())->count();
        $vehiclesInShop = Vehicle::where('status', 'in_shop')->count();
        
        $monthlyOperationalCost = FuelLog::whereMonth('fuel_date', Carbon::now()->month)
            ->sum('cost') + 
            MaintenanceLog::whereMonth('performed_at', Carbon::now()->month)
            ->sum('cost');

        $data = [
            'activeVehicles' => $activeVehicles,
            'tripsToday' => $tripsToday,
            'vehiclesInShop' => $vehiclesInShop,
            'monthlyOperationalCost' => $monthlyOperationalCost,
            'timestamp' => now()->toISOString(),
        ];

        $this->info('Dashboard data refreshed successfully');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Active Vehicles', $activeVehicles],
                ['Trips Today', $tripsToday],
                ['Vehicles In Shop', $vehiclesInShop],
                ['Monthly Operational Cost', '$' . number_format($monthlyOperationalCost, 2)],
            ]
        );

        return 0;
    }
}
