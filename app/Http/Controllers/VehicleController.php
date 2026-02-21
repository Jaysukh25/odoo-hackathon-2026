<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Vehicle;

class VehicleController extends Controller
{
    public function index()
    {
        $vehicles = Vehicle::with([
            'currentTrip.driver',
            'maintenanceLogs' => function ($query) {
                $query->latest()->limit(1);
            }
        ])->paginate(10);

        return view('vehicles.index', compact('vehicles'));
    }

    public function create()
    {
        return view('vehicles.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'model' => 'required|string|max:255',
            'license_plate' => 'required|string|max:255|unique:vehicles',
            'max_capacity' => 'required|numeric|min:0',
            'odometer' => 'required|numeric|min:0',
            'status' => 'required|in:available,in_shop,out_of_service',
        ], [
            'max_capacity.min' => 'Value cannot be negative.',
            'odometer.min' => 'Value cannot be negative.',
        ]);

        Vehicle::create($request->all());

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehicle created successfully.');
    }

    public function show(Vehicle $vehicle)
    {
        $vehicle->load(['trips.driver', 'maintenanceLogs', 'fuelLogs']);

        return view('vehicles.show', compact('vehicle'));
    }

    public function edit(Vehicle $vehicle)
    {
        return view('vehicles.edit', compact('vehicle'));
    }

    public function update(Request $request, Vehicle $vehicle)
    {
        $request->validate([
            'model' => 'required|string|max:255',
            'license_plate' => 'required|string|max:255|unique:vehicles,license_plate,' . $vehicle->id,
            'max_capacity' => 'required|numeric|min:0',
            'odometer' => 'required|numeric|min:0',
            'status' => 'required|in:available,in_shop,out_of_service',
        ], [
            'max_capacity.min' => 'Value cannot be negative.',
            'odometer.min' => 'Value cannot be negative.',
        ]);

        $vehicle->update($request->all());

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehicle updated successfully.');
    }

    public function destroy(Vehicle $vehicle)
    {
        if ($vehicle->currentTrip) {
            return redirect()->route('vehicles.index')
                ->with('error', 'Cannot delete vehicle with active trips.');
        }

        $vehicle->delete();

        return redirect()->route('vehicles.index')
            ->with('success', 'Vehicle deleted successfully.');
    }

    public function toggleStatus(Vehicle $vehicle)
    {
        $vehicle->update(['out_of_service' => !$vehicle->out_of_service]);

        $status = $vehicle->out_of_service ? 'Out of Service' : 'In Service';

        return redirect()->route('vehicles.index')
            ->with('success', "Vehicle status changed to {$status}.");
    }
}
