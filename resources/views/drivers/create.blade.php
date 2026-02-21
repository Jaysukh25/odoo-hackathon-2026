@extends('layouts.app')

@section('title', 'Add Driver')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Add New Driver</h2>
    <a href="{{ route('drivers.index') }}" class="btn btn-secondary">
        <i class="bi bi-arrow-left me-2"></i>Back to Drivers
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Driver Information</h5>
            </div>
            <div class="card-body">
                <form method="POST" action="{{ route('drivers.store') }}">
                    @csrf
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="name" class="form-label">Full Name</label>
                            <input type="text" class="form-control" id="name" name="name" value="{{ old('name') }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="phone" class="form-label">Phone Number</label>
                            <input type="tel" class="form-control" id="phone" name="phone" value="{{ old('phone') }}" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="license_number" class="form-label">License Number</label>
                            <input type="text" class="form-control" id="license_number" name="license_number" value="{{ old('license_number') }}" required>
                        </div>
                        
                        <div class="col-md-6 mb-3">
                            <label for="license_expiry" class="form-label">License Expiry Date</label>
                            <input type="date" class="form-control" id="license_expiry" name="license_expiry" value="{{ old('license_expiry') }}" required>
                        </div>
                    </div>
                    
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label for="status" class="form-label">Status</label>
                            <select class="form-select" id="status" name="status" required>
                                <option value="available" {{ old('status') == 'available' ? 'selected' : '' }}>Available</option>
                                <option value="on_duty" {{ old('status') == 'on_duty' ? 'selected' : '' }}>On Duty</option>
                                <option value="off_duty" {{ old('status') == 'off_duty' ? 'selected' : '' }}>Off Duty</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="d-flex justify-content-end gap-2">
                        <a href="{{ route('drivers.index') }}" class="btn btn-secondary">Cancel</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-plus-circle me-2"></i>Add Driver
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">License Information</h5>
            </div>
            <div class="card-body">
                <div class="alert alert-info">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>License Expiry:</strong> Drivers with expired licenses cannot be assigned to trips.
                </div>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle me-2"></i>
                    <strong>30-Day Warning:</strong> System will alert when license expires within 30 days.
                </div>
                <div class="alert alert-success">
                    <i class="bi bi-shield-check me-2"></i>
                    <strong>Risk Assessment:</strong> Driver risk scores are calculated automatically based on performance.
                </div>
            </div>
        </div>
        
        <div class="card mt-3">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Risk Level Guide</h5>
            </div>
            <div class="card-body">
                <div class="mb-2">
                    <span class="badge bg-success me-2">SAFE</span>
                    <small>Score: 0-5</small>
                </div>
                <div class="mb-2">
                    <span class="badge bg-warning me-2">MODERATE</span>
                    <small>Score: 6-15</small>
                </div>
                <div class="mb-2">
                    <span class="badge bg-danger me-2">RISKY</span>
                    <small>Score: 16+</small>
                </div>
                <hr>
                <small class="text-muted">
                    Risk factors: Late trips (2pts), Cancelled trips (3pts), License warnings (5pts)
                </small>
            </div>
        </div>
    </div>
</div>
@endsection
