@extends('layouts.app')

@section('title', 'Maintenance Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Maintenance Details</h2>
    <div>
        <a href="{{ route('maintenance.edit', $maintenanceLog) }}" class="btn btn-primary me-2">
            <i class="bi bi-pencil me-2"></i>Edit
        </a>
        <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Maintenance Information -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Maintenance Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Maintenance ID</label>
                        <p class="fw-semibold">#{{ $maintenanceLog->id }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Service Date</label>
                        <p class="fw-semibold">{{ $maintenanceLog->performed_at->format('M d, Y H:i') }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Maintenance Type</label>
                        <p>
                            <span class="badge bg-info fs-6">{{ $maintenanceLog->type }}</span>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Cost</label>
                        <p class="fw-semibold fs-5">${{ number_format($maintenanceLog->cost, 2) }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Odometer at Service</label>
                        <p class="fw-semibold">{{ number_format($maintenanceLog->odometer_at_service, 0) }} km</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Distance Since Last Service</label>
                        <p class="fw-semibold">
                            @if($maintenanceLog->vehicle->lastMaintenance && $maintenanceLog->vehicle->lastMaintenance->id != $maintenanceLog->id)
                                {{ number_format($maintenanceLog->odometer_at_service - $maintenanceLog->vehicle->lastMaintenance->odometer_at_service, 0) }} km
                            @else
                                First service
                            @endif
                        </p>
                    </div>
                </div>
                
                <div class="row">
                    <div class="col-12 mb-3">
                        <label class="text-muted">Description</label>
                        <p class="fw-semibold">{{ $maintenanceLog->description }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Vehicle Maintenance History -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Vehicle Maintenance History</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Type</th>
                                <th>Cost</th>
                                <th>Odometer</th>
                                <th>Description</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($maintenanceLog->vehicle->maintenanceLogs->sortByDesc('performed_at')->take(10) as $history)
                            <tr @if($history->id == $maintenanceLog->id) class="table-primary" @endif>
                                <td>{{ $history->performed_at->format('M d, Y') }}</td>
                                <td><span class="badge bg-info">{{ $history->type }}</span></td>
                                <td>${{ number_format($history->cost, 2) }}</td>
                                <td>{{ number_format($history->odometer_at_service, 0) }} km</td>
                                <td>{{ Str::limit($history->description, 30) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">No maintenance history found</td>
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
                        <h6 class="mb-0">{{ $maintenanceLog->vehicle->license_plate }}</h6>
                        <small class="text-muted">{{ $maintenanceLog->vehicle->model }}</small>
                    </div>
                </div>
                <div class="mb-2">
                    <label class="text-muted">Current Odometer</label>
                    <p class="fw-semibold mb-0">{{ number_format($maintenanceLog->vehicle->odometer, 0) }} km</p>
                </div>
                <div class="mb-2">
                    <label class="text-muted">Current Status</label>
                    <p class="mb-0">
                        <span class="status-badge status-{{ $maintenanceLog->vehicle->status }}">
                            {{ ucfirst(str_replace('_', ' ', $maintenanceLog->vehicle->status)) }}
                        </span>
                    </p>
                </div>
                <div class="mb-2">
                    <label class="text-muted">Next Service Due</label>
                    <p class="mb-0">
                        @if($maintenanceLog->vehicle->needsMaintenance())
                            <span class="badge bg-warning">Due Now</span>
                            <br>
                            <small class="text-muted">Current: {{ number_format($maintenanceLog->vehicle->odometer, 0) }} km</small>
                            <br>
                            <small class="text-muted">Last service: {{ number_format($maintenanceLog->odometer_at_service, 0) }} km</small>
                        @else
                            <span class="badge bg-success">Good</span>
                            <br>
                            <small class="text-muted">Next service at {{ number_format($maintenanceLog->odometer_at_service + 5000, 0) }} km</small>
                        @endif
                    </p>
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
                    <label class="text-muted">This Service</label>
                    <p class="fw-semibold fs-5">${{ number_format($maintenanceLog->cost, 2) }}</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted">Total Maintenance Cost</label>
                    <p class="fw-semibold fs-5">${{ number_format($maintenanceLog->vehicle->total_maintenance_cost, 2) }}</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted">Total Fuel Cost</label>
                    <p class="fw-semibold fs-5">${{ number_format($maintenanceLog->vehicle->total_fuel_cost, 2) }}</p>
                </div>
                <hr>
                <div class="mb-3">
                    <label class="text-muted">Total Operational Cost</label>
                    <p class="fw-semibold fs-4 text-primary">${{ number_format($maintenanceLog->vehicle->total_operational_cost, 2) }}</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted">Cost per Kilometer</label>
                    <p class="fw-semibold">
                        @if($maintenanceLog->vehicle->odometer > 0)
                            ${{ number_format($maintenanceLog->vehicle->total_operational_cost / $maintenanceLog->vehicle->odometer, 3) }}/km
                        @else
                            N/A
                        @endif
                    </p>
                </div>
            </div>
        </div>

        <!-- Maintenance Schedule -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Maintenance Schedule</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>Predictive Maintenance</strong><br>
                    <small>Next service recommended at {{ number_format($maintenanceLog->odometer_at_service + 5000, 0) }} km</small>
                </div>
                
                <div class="progress mb-2">
                    @php
                    $nextServiceAt = $maintenanceLog->odometer_at_service + 5000;
                    $currentOdometer = $maintenanceLog->vehicle->odometer;
                    $progress = min(($currentOdometer - $maintenanceLog->odometer_at_service) / 5000 * 100, 100);
                    @endphp
                    <div class="progress-bar @if($progress > 80) bg-warning @elseif($progress >= 100) bg-danger @else bg-success @endif" 
                         role="progressbar" 
                         style="width: {{ $progress }}%">
                        {{ round($progress, 0) }}%
                    </div>
                </div>
                <small class="text-muted">
                    {{ number_format($currentOdometer, 0) }} / {{ number_format($nextServiceAt, 0) }} km
                </small>
            </div>
        </div>
    </div>
</div>
@endsection
