<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;
use App\Models\Trip;
use App\Models\FuelLog;
use App\Models\MaintenanceLog;
use Carbon\Carbon;
use Symfony\Component\HttpFoundation\StreamedResponse;

class AnalyticsController extends Controller
{
    public function index()
    {
        $fuelEfficiency = $this->calculateFuelEfficiency();
        $vehicleROI = $this->calculateVehicleROI();
        $monthlyCosts = $this->getMonthlyCosts();
        $topPerformers = $this->getTopPerformers();

        return view('analytics', compact(
            'fuelEfficiency',
            'vehicleROI',
            'monthlyCosts',
            'topPerformers'
        ));
    }

    private function calculateFuelEfficiency()
    {
        return Vehicle::with(['fuelLogs', 'trips'])
            ->get()
            ->map(function ($vehicle) {
                $totalDistance = $vehicle->trips()->where('status', 'completed')->sum('distance');
                $totalFuel = $vehicle->fuelLogs()->sum('liters');
                
                return [
                    'vehicle' => $vehicle->license_plate,
                    'model' => $vehicle->model,
                    'distance' => $totalDistance,
                    'fuel' => $totalFuel,
                    'efficiency' => $totalFuel > 0 ? round($totalDistance / $totalFuel, 2) : 0,
                ];
            })
            ->sortByDesc('efficiency')
            ->values();
    }

    private function calculateVehicleROI()
    {
        return Vehicle::with(['trips', 'fuelLogs', 'maintenanceLogs'])
            ->get()
            ->map(function ($vehicle) {
                $revenue = $vehicle->trips()->where('status', 'completed')->sum('distance') * 2;
                $fuelCost = $vehicle->fuelLogs()->sum('cost');
                $maintenanceCost = $vehicle->maintenanceLogs()->sum('cost');
                $totalCosts = $fuelCost + $maintenanceCost;
                $acquisitionCost = 50000;
                
                $roi = $acquisitionCost > 0 ? 
                    round((($revenue - $totalCosts) / $acquisitionCost) * 100, 2) : 0;

                return [
                    'vehicle' => $vehicle->license_plate,
                    'model' => $vehicle->model,
                    'revenue' => $revenue,
                    'fuel_cost' => $fuelCost,
                    'maintenance_cost' => $maintenanceCost,
                    'total_costs' => $totalCosts,
                    'roi_percentage' => $roi,
                ];
            })
            ->sortByDesc('roi_percentage')
            ->values();
    }

    private function getMonthlyCosts()
    {
        $months = collect();
        
        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $fuelCost = FuelLog::whereMonth('fuel_date', $date->month)
                ->whereYear('fuel_date', $date->year)
                ->sum('cost');
            
            $maintenanceCost = MaintenanceLog::whereMonth('performed_at', $date->month)
                ->whereYear('performed_at', $date->year)
                ->sum('cost');
            
            $months->push([
                'month' => $date->format('M Y'),
                'fuel_cost' => $fuelCost,
                'maintenance_cost' => $maintenanceCost,
                'total_cost' => $fuelCost + $maintenanceCost,
            ]);
        }
        
        return $months;
    }

    private function getTopPerformers()
    {
        return Vehicle::with(['trips.driver'])
            ->get()
            ->map(function ($vehicle) {
                $completedTrips = $vehicle->trips()->where('status', 'completed')->count();
                $totalDistance = $vehicle->trips()->where('status', 'completed')->sum('distance');
                
                return [
                    'vehicle' => $vehicle->license_plate,
                    'completed_trips' => $completedTrips,
                    'total_distance' => $totalDistance,
                    'avg_distance_per_trip' => $completedTrips > 0 ? round($totalDistance / $completedTrips, 2) : 0,
                ];
            })
            ->sortByDesc('completed_trips')
            ->take(10)
            ->values();
    }

    public function export()
    {
        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="analytics_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () {
            $file = fopen('php://output', 'w');
            
            fputcsv($file, ['Vehicle', 'Model', 'Total Distance (km)', 'Total Fuel (L)', 'Fuel Efficiency (km/L)', 'Total Revenue', 'Total Costs', 'ROI (%)']);
            
            $vehicles = Vehicle::with(['trips', 'fuelLogs', 'maintenanceLogs'])->get();
            
            foreach ($vehicles as $vehicle) {
                $totalDistance = $vehicle->trips()->where('status', 'completed')->sum('distance');
                $totalFuel = $vehicle->fuelLogs()->sum('liters');
                $efficiency = $totalFuel > 0 ? round($totalDistance / $totalFuel, 2) : 0;
                $revenue = $totalDistance * 2;
                $fuelCost = $vehicle->fuelLogs()->sum('cost');
                $maintenanceCost = $vehicle->maintenanceLogs()->sum('cost');
                $totalCosts = $fuelCost + $maintenanceCost;
                $acquisitionCost = 50000;
                $roi = $acquisitionCost > 0 ? round((($revenue - $totalCosts) / $acquisitionCost) * 100, 2) : 0;
                
                fputcsv($file, [
                    $vehicle->license_plate,
                    $vehicle->model,
                    $totalDistance,
                    $totalFuel,
                    $efficiency,
                    $revenue,
                    $totalCosts,
                    $roi,
                ]);
            }
            
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
