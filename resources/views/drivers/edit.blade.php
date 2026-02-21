@extends('layouts.app')

@section('title', 'Edit Driver')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Edit Driver</h2>
    <a href="{{ route('drivers.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Drivers
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Update Driver Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('drivers.update', $driver) }}">
                    @csrf
                    @method('PUT')
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ $driver->name }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="{{ $driver->phone }}" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="license_number" class="form-label">License Number</label>
                            <input type="text" class="form-control" id="license_number" name="license_number" value="{{ $driver->license_number }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="license_expiry" class="form-label">License Expiry Date</label>
                            <input type="date" class="form-control" id="license_expiry" name="license_expiry" value="{{ $driver->license_expiry->format('Y-m-d') }}" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="available" {{ $driver->status == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="on_duty" {{ $driver->status == 'on_duty' ? 'selected' : '' }}>On Duty</option>
                                <option value="off_duty" {{ $driver->status == 'off_duty' ? 'selected' : '' }}>Off Duty</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('drivers.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-2"></i>Update Driver
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Driver Statistics</h5>
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
                    <label class="text-muted">Current Risk Level</label>
                    <p>
                        <span class="badge bg-{{ $driver->risk_color }} fs-6">
                            {{ $driver->risk_level }}
                        </span>
                        <br>
                        <small class="text-muted">Score: {{ $driver->risk_score }}</small>
                    </p>
                </div>
            </div>
        </div>
        
        @if($driver->license_expired)
        <div class="card mt-3">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">⚠️ License Issues</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-danger">
                    <i class="bi bi-x-circle me-2"></i>
                    <strong>License Expired</strong><br>
                    <small>This driver cannot be assigned to trips until license is renewed.</small>
                </div>
            </div>
        </div>
        @elseif($driver->license_expiring_soon)
        <div class="card mt-3">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">⚠️ License Warning</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>License Expiring Soon</strong><br>
                    <small>License expires in {{ $driver->license_expiry->diffInDays(now()) }} days.</small>
                </div>
            </div>
        </div>
        @endif
    </div>
</div>
@endsection
