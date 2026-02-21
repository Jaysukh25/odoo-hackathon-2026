<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\FuelLog;
use App\Models\Vehicle;

class FuelController extends Controller
{
    public function index()
    {
        $fuelLogs = FuelLog::with('vehicle')
            ->orderBy('fuel_date', 'desc')
            ->paginate(10);

        return view('fuel.index', compact('fuelLogs'));
    }

    public function create()
    {
        $vehicles = Vehicle::all();

        return view('fuel.create', compact('vehicles'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'liters' => 'required|numeric|min:0',
            'cost_per_liter' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'odometer' => 'required|numeric|min:0',
            'fuel_date' => 'required|date',
        ], [
            'liters.min' => 'Value cannot be negative.',
            'cost_per_liter.min' => 'Value cannot be negative.',
            'cost.min' => 'Value cannot be negative.',
            'odometer.min' => 'Value cannot be negative.',
        ]);

        FuelLog::create($request->all());

        return redirect()->route('fuel.index')
            ->with('success', 'Fuel log created successfully.');
    }

    public function show(FuelLog $fuelLog)
    {
        $fuelLog->load('vehicle');

        return view('fuel.show', compact('fuelLog'));
    }

    public function edit(FuelLog $fuelLog)
    {
        $vehicles = Vehicle::all();

        return view('fuel.edit', compact('fuelLog', 'vehicles'));
    }

    public function update(Request $request, FuelLog $fuelLog)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'liters' => 'required|numeric|min:0',
            'cost_per_liter' => 'required|numeric|min:0',
            'cost' => 'nullable|numeric|min:0',
            'odometer' => 'required|numeric|min:0',
            'fuel_date' => 'required|date',
        ], [
            'liters.min' => 'Value cannot be negative.',
            'cost_per_liter.min' => 'Value cannot be negative.',
            'cost.min' => 'Value cannot be negative.',
            'odometer.min' => 'Value cannot be negative.',
        ]);

        $fuelLog->update($request->all());

        return redirect()->route('fuel.index')
            ->with('success', 'Fuel log updated successfully.');
    }

    public function destroy(FuelLog $fuelLog)
    {
        $fuelLog->delete();

        return redirect()->route('fuel.index')
            ->with('success', 'Fuel log deleted successfully.');
    }
}
