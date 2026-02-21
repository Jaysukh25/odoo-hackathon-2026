@extends('layouts.app')

@section('title', 'Fuel Log Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Fuel Log Details</h2>
    <div>
        <a href="{{ route('fuel.edit', $fuelLog) }}" class="btn btn-primary me-2">
            <i class="bi bi-pencil me-2"></i>Edit
        </a>
        <a href="{{ route('fuel.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Fuel Information -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Fuel Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Fuel Log ID</label>
                        <p class="fw-semibold">#{{ $fuelLog->id }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Fuel Date</label>
                        <p class="fw-semibold">{{ $fuelLog->fuel_date->format('M d, Y H:i') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Liters</label>
                        <p class="fw-semibold fs-5">{{ number_format($fuelLog->liters, 2) }} L</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Cost per Liter</label>
                        <p class="fw-semibold fs-5">${{ number_format($fuelLog->cost_per_liter, 2) }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Total Cost</label>
                        <p class="fw-semibold fs-5 text-primary">${{ number_format($fuelLog->cost, 2) }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Odometer at Fueling</label>
                        <p class="fw-semibold">{{ number_format($fuelLog->odometer, 0) }} km</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vehicle Fuel History -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Vehicle Fuel History</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Liters</th>
                                <th>Cost/Liter</th>
                                <th>Total Cost</th>
                                <th>Odometer</th>
                                <th>Efficiency</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($fuelLog->vehicle->fuelLogs->sortByDesc('fuel_date')->take(10) as $history)
                            <tr @if($history->id == $fuelLog->id) class="table-primary" @endif>
                                <td>{{ $history->fuel_date->format('M d, Y') }}</td>
                                <td>{{ number_format($history->liters, 2) }} L</td>
                                <td>${{ number_format($history->cost_per_liter, 2) }}</td>
                                <td>${{ number_format($history->cost, 2) }}</td>
                                <td>{{ number_format($history->odometer, 0) }} km</td>
                                <td>
                                    @php
                                    $prevFuel = $fuelLog->vehicle->fuelLogs()
                                        ->where('fuel_date', '<', $history->fuel_date)
                                        ->orderBy('fuel_date', 'desc')
                                        ->first();
                                    
                                    $eff = 0;
                                    if ($prevFuel && $history->odometer > $prevFuel->odometer) {
                                        $dist = $history->odometer - $prevFuel->odometer;
                                        $eff = $dist / $history->liters;
                                    }
                                    @endphp
                                    @if($eff > 0)
                                        {{ number_format($eff, 2) }} km/L
                                    @else
                                        N/A
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No fuel history found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Vehicle Information -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Vehicle Information</h5>
            </div>
            <div class="card-body">
                <div class="d-flex align-items-center mb-3">
                    <i class="bi bi-truck text-primary fs-3 me-3"></i>
                    <div>
                        <h6 class="mb-0">{{ $fuelLog->vehicle->license_plate }}</h6>
                        <small class="text-muted">{{ $fuelLog->vehicle->model }}</small>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="text-muted">Current Odometer</label>
                    <p class="fw-semibold mb-0">{{ number_format($fuelLog->vehicle->odometer, 0) }} km</p>
                </div>
                <div class="mb-2">
                    <label class="text-muted">Vehicle Status</label>
                    <p class="mb-0">
                        <span class="status-badge status-{{ $fuelLog->vehicle->status }}">
                            {{ ucfirst(str_replace('_', ' ', $fuelLog->vehicle->status)) }}
                        </span>
                    </p>
                </div>
                <div class="mb-2">
                    <label class="text-muted">Average Fuel Efficiency</label>
                    <p class="fw-semibold mb-0">{{ number_format($fuelLog->vehicle->average_fuel_efficiency, 2) }} km/L</p>
                </div>
            </div>
        </div>

        <!-- Cost Analysis -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Cost Analysis</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted">This Fueling</label>
                    <p class="fw-semibold fs-5">${{ number_format($fuelLog->cost, 2) }}</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted">Total Fuel Cost</label>
                    <p class="fw-semibold fs-5">${{ number_format($fuelLog->vehicle->total_fuel_cost, 2) }}</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted">Total Maintenance Cost</label>
                    <p class="fw-semibold fs-5">${{ number_format($fuelLog->vehicle->total_maintenance_cost, 2) }}</p>
                </div>
                <hr>
                <div class="mb-3">
                    <label class="text-muted">Total Operational Cost</label>
                    <p class="fw-semibold fs-4 text-primary">${{ number_format($fuelLog->vehicle->total_operational_cost, 2) }}</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted">Cost per Kilometer</label>
                    <p class="fw-semibold">
                        @if($fuelLog->vehicle->odometer > 0)
                            ${{ number_format($fuelLog->vehicle->total_operational_cost / $fuelLog->vehicle->odometer, 3) }}/km
                        @else
                            N/A
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Fuel Efficiency Stats -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Fuel Efficiency Stats</h5>
            </div>
            <div class="card-body">
                @php
                $previousFuel = $fuelLog->vehicle->fuelLogs()
                    ->where('fuel_date', '<', $fuelLog->fuel_date)
                    ->orderBy('fuel_date', 'desc')
                    ->first();
                
                $efficiency = 0;
                $distance = 0;
                if ($previousFuel && $fuelLog->odometer > $previousFuel->odometer) {
                    $distance = $fuelLog->odometer - $previousFuel->odometer;
                    $efficiency = $distance / $fuelLog->liters;
                }
                @endphp
                
                <div class="mb-3">
                    <label class="text-muted">Distance Since Last Fueling</label>
                    <p class="fw-semibold fs-5">{{ number_format($distance, 0) }} km</p>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted">Fuel Efficiency This Period</label>
                    <p class="fw-semibold fs-5">
                        @if($efficiency > 0)
                            {{ number_format($efficiency, 2) }} km/L
                        @else
                            N/A
                        @endif
                    </p>
                </div>
                
                <div class="mb-3">
                    <label class="text-muted">Average Cost per Kilometer</label>
                    <p class="fw-semibold">
                        @if($distance > 0)
                            ${{ number_format($fuelLog->cost / $distance, 3) }}/km
                        @else
                            N/A
                        @endif
                    </p>
                </div>
                
                <div class="progress mb-2">
                    @php
                    $avgEfficiency = $fuelLog->vehicle->average_fuel_efficiency;
                    $percentage = $avgEfficiency > 0 ? min(($efficiency / $avgEfficiency) * 100, 150) : 0;
                    @endphp
                    <div class="progress-bar @if($percentage >= 100) bg-success @elseif($percentage >= 80) bg-warning @else bg-danger @endif" 
                         role="progressbar" 
                         style="width: {{ min($percentage, 100) }}%">
                        @if($efficiency > 0)
                            {{ round($percentage, 0) }}%
                        @endif
                    </div>
                </div>
                <small class="text-muted">
                    @if($efficiency > 0)
                        Efficiency vs Average: {{ $percentage >= 100 ? 'Above' : ($percentage >= 80 ? 'Near' : 'Below') }} average
                    @else
                        No efficiency data available
                    @endif
                </small>
            </div>
        </div>
    </div>
</div>
@endsection
