<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MaintenanceLog;
use App\Models\Vehicle;

class MaintenanceController extends Controller
{
    public function index()
    {
        $maintenanceLogs = MaintenanceLog::with('vehicle')
            ->orderBy('performed_at', 'desc')
            ->paginate(10);

        return view('maintenance.index', compact('maintenanceLogs'));
    }

    public function create()
    {
        $vehicles = Vehicle::all();

        return view('maintenance.create', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'type' => 'required|string|max:255',
            'description' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'odometer_at_service' => 'required|numeric|min:0',
            'performed_at' => 'required|date',
        ], [
            'cost.min' => 'Value cannot be negative.',
            'odometer_at_service.min' => 'Value cannot be negative.',
        ]);

        MaintenanceLog::create($request->all());

        return redirect()->route('maintenance.index')
            ->with('success', 'Maintenance log created successfully.');
    }

    public function show(MaintenanceLog $maintenanceLog)
    {
        $maintenanceLog->load('vehicle');

        return view('maintenance.show', compact('maintenanceLog'));
    }

    public function edit(MaintenanceLog $maintenanceLog)
    {
        $vehicles = Vehicle::all();

        return view('maintenance.edit', compact('maintenanceLog', 'vehicles'));
    }

    public function update(Request $request, MaintenanceLog $maintenanceLog)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'type' => 'required|string|max:255',
            'description' => 'required|string',
            'cost' => 'required|numeric|min:0',
            'odometer_at_service' => 'required|numeric|min:0',
            'performed_at' => 'required|date',
        ], [
            'cost.min' => 'Value cannot be negative.',
            'odometer_at_service.min' => 'Value cannot be negative.',
        ]);

        $maintenanceLog->update($request->all());

        return redirect()->route('maintenance.index')
            ->with('success', 'Maintenance log updated successfully.');
    }

    public function destroy(MaintenanceLog $maintenanceLog)
    {
        $maintenanceLog->delete();

        return redirect()->route('maintenance.index')
            ->with('success', 'Maintenance log deleted successfully.');
    }
}
