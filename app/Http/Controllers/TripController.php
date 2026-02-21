<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Trip;
use App\Models\Vehicle;
use App\Models\Driver;

class TripController extends Controller
{
    public function index()
    {
        $trips = Trip::with(['vehicle', 'driver'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('trips.index', compact('trips'));
    }

    public function create()
    {
        $vehicles = Vehicle::where('status', 'available')->where('out_of_service', false)->get();
        $drivers = Driver::where('license_expiry', '>', now())->get()->filter(fn($driver) => $driver->canBeAssigned());

        return view('trips.create', compact('vehicles', 'drivers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:drivers,id',
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'cargo_weight' => 'required|numeric|min:0',
            'distance' => 'required|numeric|min:0',
            'estimated_duration' => 'required|integer|min:1',
        ], [
            'cargo_weight.min' => 'Value cannot be negative.',
            'distance.min' => 'Value cannot be negative.',
            'estimated_duration.min' => 'Duration must be at least 1 minute.',
        ]);

        $vehicle = Vehicle::find($request->vehicle_id);

        if ($request->cargo_weight > $vehicle->max_capacity) {
            return back()->withErrors([
                'cargo_weight' => 'Cargo weight exceeds vehicle maximum capacity.'
            ])->withInput();
        }

        $trip = Trip::create($request->all());

        return redirect()->route('trips.index')
            ->with('success', 'Trip created successfully.');
    }

    public function show(Trip $trip)
    {
        $trip->load(['vehicle', 'driver', 'statusHistory']);

        return view('trips.show', compact('trip'));
    }

    public function edit(Trip $trip)
    {
        if ($trip->status !== 'draft') {
            return redirect()->route('trips.index')
                ->with('error', 'Can only edit draft trips.');
        }

        $vehicles = Vehicle::where('status', 'available')->where('out_of_service', false)->get();
        $drivers = Driver::where('license_expiry', '>', now())->get()->filter(fn($driver) => $driver->canBeAssigned());

        return view('trips.edit', compact('trip', 'vehicles', 'drivers'));
    }

    public function update(Request $request, Trip $trip)
    {
        if ($trip->status !== 'draft') {
            return redirect()->route('trips.index')
                ->with('error', 'Can only edit draft trips.');
        }

        $request->validate([
            'vehicle_id' => 'required|exists:vehicles,id',
            'driver_id' => 'required|exists:drivers,id',
            'origin' => 'required|string|max:255',
            'destination' => 'required|string|max:255',
            'cargo_weight' => 'required|numeric|min:0',
            'distance' => 'required|numeric|min:0',
            'estimated_duration' => 'required|integer|min:1',
        ], [
            'cargo_weight.min' => 'Value cannot be negative.',
            'distance.min' => 'Value cannot be negative.',
            'estimated_duration.min' => 'Duration must be at least 1 minute.',
        ]);

        $vehicle = Vehicle::find($request->vehicle_id);

        if ($request->cargo_weight > $vehicle->max_capacity) {
            return back()->withErrors([
                'cargo_weight' => 'Cargo weight exceeds vehicle maximum capacity.'
            ])->withInput();
        }

        $trip->update($request->all());

        return redirect()->route('trips.index')
            ->with('success', 'Trip updated successfully.');
    }

    public function destroy(Trip $trip)
    {
        if ($trip->status === 'on_trip') {
            return redirect()->route('trips.index')
                ->with('error', 'Cannot delete trip that is currently in progress.');
        }

        $trip->delete();

        return redirect()->route('trips.index')
            ->with('success', 'Trip deleted successfully.');
    }

    public function dispatch(Trip $trip)
    {
        if ($trip->status !== 'draft') {
            return redirect()->route('trips.index')
                ->with('error', 'Can only dispatch draft trips.');
        }

        $trip->updateStatus('dispatched');

        return redirect()->route('trips.index')
            ->with('success', 'Trip dispatched successfully.');
    }

    public function complete(Trip $trip)
    {
        if ($trip->status !== 'on_trip') {
            return redirect()->route('trips.index')
                ->with('error', 'Can only complete trips that are in progress.');
        }

        $trip->updateStatus('completed');

        return redirect()->route('trips.index')
            ->with('success', 'Trip completed successfully.');
    }

    public function cancel(Trip $trip)
    {
        if ($trip->status === 'completed') {
            return redirect()->route('trips.index')
                ->with('error', 'Cannot cancel completed trips.');
        }

        $trip->updateStatus('cancelled');

        return redirect()->route('trips.index')
            ->with('success', 'Trip cancelled successfully.');
    }
}
