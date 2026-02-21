@extends('layouts.app')

@section('title', 'Driver Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Driver Details</h2>
    <div>
        <a href="{{ route('drivers.edit', $driver) }}" class="btn btn-primary me-2">
            <i class="bi bi-pencil me-2"></i>Edit
        </a>
        <a href="{{ route('drivers.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back
        </a>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Driver Information -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Driver Information</h5>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Full Name</label>
                        <div class="d-flex align-items-center">
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                {{ strtoupper(substr($driver->name, 0, 1)) }}
                            </div>
                            <p class="fw-semibold fs-5 mb-0">{{ $driver->name }}</p>
                        </div>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Phone Number</label>
                        <p class="fw-semibold">{{ $driver->phone }}</p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">License Number</label>
                        <p class="fw-semibold"><code>{{ $driver->license_number }}</code></p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">License Expiry</label>
                        <p class="fw-semibold">{{ $driver->license_expiry->format('M d, Y') }}</p>
                        @if($driver->license_expiring_soon)
                            <span class="badge bg-warning">
                                <i class="bi bi-exclamation-triangle me-1"></i>
                                Expires in {{ $driver->license_expiry->diffInDays(now()) }} days
                            </span>
                        @endif
                        @if($driver->license_expired)
                            <span class="badge bg-danger">
                                <i class="bi bi-x-circle me-1"></i>
                                Expired
                            </span>
                        @endif
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Status</label>
                        <p>
                            <span class="status-badge status-{{ $driver->status }}">
                                {{ ucfirst(str_replace('_', ' ', $driver->status)) }}
                            </span>
                        </p>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="text-muted">Risk Assessment</label>
                        <p>
                            <span class="badge bg-{{ $driver->risk_color }} fs-6">
                                {{ $driver->risk_level }}
                            </span>
                            <br>
                            <small class="text-muted">Risk Score: {{ $driver->risk_score }}</small>
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Trips -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Recent Trips</h5>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Vehicle</th>
                                <th>Route</th>
                                <th>Cargo</th>
                                <th>Status</th>
                                <th>Performance</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($driver->trips->take(10) as $trip)
                            <tr>
                                <td>{{ $trip->created_at->format('M d, Y') }}</td>
                                <td>{{ $trip->vehicle->license_plate }}</td>
                                <td>{{ $trip->origin }} → {{ $trip->destination }}</td>
                                <td>{{ number_format($trip->cargo_weight, 2) }} kg</td>
                                <td>
                                    <span class="status-badge status-{{ $trip->status }}">
                                        {{ ucfirst(str_replace('_', ' ', $trip->status)) }}
                                    </span>
                                </td>
                                <td>
                                    @if($trip->status === 'completed' && $trip->arrived_late)
                                        <span class="badge bg-warning">Late</span>
                                    @elseif($trip->status === 'completed')
                                        <span class="badge bg-success">On Time</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted">No trips found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Performance Stats -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Performance Statistics</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted">Total Trips</label>
                    <p class="fw-semibold fs-5">{{ $driver->trips->count() }}</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted">Completed Trips</label>
                    <p class="fw-semibold fs-5">{{ $driver->trips()->where('status', 'completed')->count() }}</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted">Cancelled Trips</label>
                    <p class="fw-semibold fs-5">{{ $driver->trips()->where('status', 'cancelled')->count() }}</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted">Late Arrivals</label>
                    <p class="fw-semibold fs-5">{{ $driver->trips()->where('arrived_late', true)->count() }}</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted">Total Distance</label>
                    <p class="fw-semibold fs-5">{{ number_format($driver->trips()->where('status', 'completed')->sum('distance'), 0) }} km</p>
                </div>
            </div>
        </div>

        <!-- Current Trip -->
        @if($driver->currentTrip)
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Current Trip</h5>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <label class="text-muted">Vehicle</label>
                    <p class="fw-semibold">{{ $driver->currentTrip->vehicle->license_plate }} ({{ $driver->currentTrip->vehicle->model }})</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted">Route</label>
                    <p class="fw-semibold">{{ $driver->currentTrip->origin }} → {{ $driver->currentTrip->destination }}</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted">Cargo Weight</label>
                    <p class="fw-semibold">{{ number_format($driver->currentTrip->cargo_weight, 2) }} kg</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted">Started</label>
                    <p class="fw-semibold">{{ $driver->currentTrip->started_at->format('M d, Y H:i') }}</p>
                </div>
                <div class="mb-3">
                    <label class="text-muted">Estimated Duration</label>
                    <p class="fw-semibold">{{ $driver->currentTrip->estimated_duration }} minutes</p>
                </div>
            </div>
        </div>
        @endif

        <!-- Driver Scores -->
        @if($driver->driverScores->count() > 0)
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Recent Scores</h5>
            </div>
            <div class="card-body">
                @foreach($driver->driverScores->take(5) as $score)
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <small class="text-muted">{{ $score->score_date->format('M d, Y') }}</small>
                    <span class="badge bg-primary">{{ number_format($score->score, 1) }}</span>
                </div>
                @endforeach
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
