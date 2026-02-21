<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Driver;
use App\Models\Trip;
use App\Models\MaintenanceLog;
use App\Models\FuelLog;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
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

        return view('dashboard', compact(
            'activeVehicles',
            'tripsToday',
            'vehiclesInShop',
            'monthlyOperationalCost',
            'tripActivity',
            'driverSafetyData',
            'liveFleet',
            'recentActivity',
            'maintenanceAlerts'
        ));
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

    public function fleetDetail(Vehicle $vehicle)
    {
        $vehicle->load([
            'currentTrip.driver',
            'maintenanceLogs' => fn($q) => $q->latest()->limit(1),
            'fuelLogs',
        ]);

        $currentTrip = $vehicle->currentTrip;
        $lastMaint = $vehicle->maintenanceLogs->first();

        return response()->json([
            'id' => $vehicle->id,
            'model' => $vehicle->model,
            'license_plate' => $vehicle->license_plate,
            'status' => $vehicle->status,
            'location' => $currentTrip ? $currentTrip->destination : 'Main Terminal',
            'last_updated' => $vehicle->updated_at->format('M d, Y H:i'),
            'odometer' => (float) $vehicle->odometer,
            'max_capacity' => (float) $vehicle->max_capacity,
            'trips_count' => $vehicle->trips()->count(),
            'fuel_cost' => (float) $vehicle->total_fuel_cost,
            'maintenance_cost' => (float) $vehicle->total_maintenance_cost,
            'total_cost' => (float) $vehicle->total_operational_cost,
            'current_trip' => $currentTrip ? [
                'driver' => $currentTrip->driver?->name ?? 'Unassigned',
                'destination' => $currentTrip->destination,
                'cargo_weight' => (float) ($currentTrip->cargo_weight ?? 0),
                'dispatched_at' => $currentTrip->started_at?->format('d M Y, H:i'),
            ] : null,
            'last_maintenance' => $lastMaint ? [
                'service_type' => $lastMaint->service_type,
                'performed_at' => $lastMaint->performed_at
                    ? \Carbon\Carbon::parse($lastMaint->performed_at)->format('d M Y')
                    : null,
            ] : null,
        ]);
    }
}
