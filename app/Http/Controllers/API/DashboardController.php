<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Models\Vehicle;
use App\Models\Trip;
use App\Models\MaintenanceLog;
use App\Models\FuelLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    /**
     * Get dashboard data for AJAX refresh
     */
    public function refresh()
    {
        $activeVehicles = Vehicle::where('status', 'available')->count();
        $tripsToday = Trip::whereDate('created_at', Carbon::today())->count();
        $vehiclesInShop = Vehicle::where('status', 'in_shop')->count();
        
        $monthlyOperationalCost = FuelLog::whereMonth('fuel_date', Carbon::now()->month)
            ->sum('cost') + 
            MaintenanceLog::whereMonth('performed_at', Carbon::now()->month)
            ->sum('cost');

        $tripActivity = Trip::selectRaw('DATE(created_at) as date, COUNT(*) as count')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        $driverSafetyData = [
            'SAFE' => Driver::whereHas('trips')->get()->filter(fn($driver) => $driver->risk_level === 'SAFE')->count(),
            'MODERATE' => Driver::whereHas('trips')->get()->filter(fn($driver) => $driver->risk_level === 'MODERATE')->count(),
            'RISKY' => Driver::whereHas('trips')->get()->filter(fn($driver) => $driver->risk_level === 'RISKY')->count(),
        ];

        $liveFleet = Vehicle::with(['currentTrip.driver', 'currentTrip'])
            ->whereIn('status', ['available', 'on_trip'])
            ->get()
            ->map(function ($vehicle) {
                return [
                    'id' => $vehicle->id,
                    'model' => $vehicle->model,
                    'license_plate' => $vehicle->license_plate,
                    'driver' => $vehicle->currentTrip?->driver?->name ?? 'Unassigned',
                    'status' => $vehicle->status,
                    'load' => $vehicle->currentTrip?->cargo_weight ?? 0,
                    'destination' => $vehicle->currentTrip?->destination ?? 'N/A',
                ];
            });

        $recentActivity = $this->getRecentActivity();

        $maintenanceAlerts = Vehicle::whereHas('maintenanceLogs')
            ->get()
            ->filter(fn($vehicle) => $vehicle->needsMaintenance())
            ->take(5);

        return response()->json([
            'activeVehicles' => $activeVehicles,
            'tripsToday' => $tripsToday,
            'vehiclesInShop' => $vehiclesInShop,
            'monthlyOperationalCost' => $monthlyOperationalCost,
            'tripActivity' => $tripActivity,
            'driverSafetyData' => $driverSafetyData,
            'liveFleet' => $liveFleet,
            'recentActivity' => $recentActivity,
            'maintenanceAlerts' => $maintenanceAlerts,
            'timestamp' => now()->toISOString(),
        ]);
    }

    private function getRecentActivity()
    {
        $activities = collect();

        Trip::with(['vehicle', 'driver'])
            ->where('updated_at', '>=', now()->subHours(24))
            ->orderBy('updated_at', 'desc')
            ->take(10)
            ->get()
            ->each(function ($trip) use ($activities) {
                if ($trip->status === 'completed') {
                    $activities->push([
                        'type' => 'trip_completed',
                        'message' => "Trip completed by {$trip->driver->name} to {$trip->destination}",
                        'time' => $trip->updated_at,
                        'icon' => 'check-circle',
                        'color' => 'success'
                    ]);
                } elseif ($trip->status === 'dispatched') {
                    $activities->push([
                        'type' => 'trip_started',
                        'message' => "Trip started by {$trip->driver->name} to {$trip->destination}",
                        'time' => $trip->updated_at,
                        'icon' => 'play-circle',
                        'color' => 'primary'
                    ]);
                }
            });

        MaintenanceLog::with('vehicle')
            ->where('created_at', '>=', now()->subHours(24))
            ->orderBy('created_at', 'desc')
            ->take(10)
            ->get()
            ->each(function ($maintenance) use ($activities) {
                $activities->push([
                    'type' => 'maintenance_added',
                    'message' => "Maintenance added for {$maintenance->vehicle->license_plate}",
                    'time' => $maintenance->created_at,
                    'icon' => 'wrench',
                    'color' => 'warning'
                ]);
            });

        return $activities->sortByDesc('time')->take(10)->values();
    }
}
