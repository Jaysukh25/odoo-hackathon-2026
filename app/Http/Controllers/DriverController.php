<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Driver;

class DriverController extends Controller
{
    public function index()
    {
        $drivers = Driver::with(['currentTrip.vehicle'])->paginate(10);
        
        return view('drivers.index', compact('drivers'));
    }

    public function create()
    {
        return view('drivers.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'license_number' => 'required|string|max:255|unique:drivers',
            'license_expiry' => 'required|date|after:today',
            'phone' => 'required|string|max:20',
            'status' => 'required|in:available,on_duty,off_duty',
        ]);

        Driver::create($request->all());

        return redirect()->route('drivers.index')
            ->with('success', 'Driver created successfully.');
    }

    public function show(Driver $driver)
    {
        $driver->load(['trips.vehicle', 'driverScores']);
        
        return view('drivers.show', compact('driver'));
    }

    public function edit(Driver $driver)
    {
        return view('drivers.edit', compact('driver'));
    }

    public function update(Request $request, Driver $driver)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'license_number' => 'required|string|max:255|unique:drivers,license_number,' . $driver->id,
            'license_expiry' => 'required|date|after:today',
            'phone' => 'required|string|max:20',
            'status' => 'required|in:available,on_duty,off_duty',
        ]);

        $driver->update($request->all());

        return redirect()->route('drivers.index')
            ->with('success', 'Driver updated successfully.');
    }

    public function destroy(Driver $driver)
    {
        if ($driver->currentTrip) {
            return redirect()->route('drivers.index')
                ->with('error', 'Cannot delete driver with active trips.');
        }

        $driver->delete();

        return redirect()->route('drivers.index')
            ->with('success', 'Driver deleted successfully.');
    }
}
