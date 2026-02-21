@extends('layouts.app')

@section('title', 'Add Maintenance')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Add Maintenance Record</h2>
        <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Maintenance
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Maintenance Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('maintenance.store') }}">
                        @csrf

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="vehicle_id" class="form-label">Vehicle</label>
                                <select class="form-select" id="vehicle_id" name="vehicle_id" required
                                    onchange="updateOdometerInfo()">
                                    <option value="">Select Vehicle</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" data-odometer="{{ $vehicle->odometer }}"
                                            data-plate="{{ $vehicle->license_plate }}">
                                            {{ $vehicle->license_plate }} - {{ $vehicle->model }} (Current:
                                            {{ number_format($vehicle->odometer, 0) }} km)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="type" class="form-label">Maintenance Type</label>
                                <select class="form-select" id="type" name="type" required>
                                    <option value="">Select Type</option>
                                    <option value="Oil Change" {{ old('type') == 'Oil Change' ? 'selected' : '' }}>Oil Change
                                    </option>
                                    <option value="Tire Rotation" {{ old('type') == 'Tire Rotation' ? 'selected' : '' }}>Tire
                                        Rotation</option>
                                    <option value="Brake Service" {{ old('type') == 'Brake Service' ? 'selected' : '' }}>Brake
                                        Service</option>
                                    <option value="Engine Service" {{ old('type') == 'Engine Service' ? 'selected' : '' }}>
                                        Engine Service</option>
                                    <option value="Transmission" {{ old('type') == 'Transmission' ? 'selected' : '' }}>
                                        Transmission</option>
                                    <option value="Electrical" {{ old('type') == 'Electrical' ? 'selected' : '' }}>Electrical
                                    </option>
                                    <option value="Inspection" {{ old('type') == 'Inspection' ? 'selected' : '' }}>Inspection
                                    </option>
                                    <option value="Other" {{ old('type') == 'Other' ? 'selected' : '' }}>Other</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="description" class="form-label">Description</label>
                            <textarea class="form-control" id="description" name="description" rows="4"
                                required>{{ old('description') }}</textarea>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="cost" class="form-label">Cost ($)</label>
                                <input type="number" step="0.01" min="0"
                                    class="form-control @error('cost') is-invalid @enderror" id="cost" name="cost"
                                    value="{{ old('cost') }}" required>
                                @error('cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="odometer_at_service" class="form-label">Odometer at Service (km)</label>
                                <input type="number" step="0.01" min="0"
                                    class="form-control @error('odometer_at_service') is-invalid @enderror"
                                    id="odometer_at_service" name="odometer_at_service"
                                    value="{{ old('odometer_at_service') }}" required>
                                @error('odometer_at_service')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="performed_at" class="form-label">Service Date</label>
                                <input type="date" class="form-control @error('performed_at') is-invalid @enderror"
                                    id="performed_at" name="performed_at"
                                    value="{{ old('performed_at') ?? now()->format('Y-m-d') }}" required>
                                @error('performed_at')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('maintenance.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Add Maintenance Record
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Maintenance Guidelines</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Vehicle Status:</strong> Adding maintenance will automatically set vehicle status to "In
                        Shop".
                    </div>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Odometer Reading:</strong> Record the exact odometer reading at the time of service.
                    </div>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>
                        <strong>Predictive Maintenance:</strong> System alerts when odometer exceeds 5000km since last
                        service.
                    </div>
                </div>
            </div>

            <div class="card mt-3">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Vehicle Odometer Info</h5>
                </div>
                <div class="card-body">
                    <div id="odometerInfo" class="text-center text-muted">
                        <i class="bi bi-speedometer2 fs-1"></i>
                        <p class="mt-2">Select a vehicle to view odometer information</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
@endsection

@section('scripts')
    <script>
        function updateOdometerInfo() {
            const select = document.getElementById('vehicle_id');
            const infoDiv = document.getElementById('odometerInfo');
            const odometerInput = document.getElementById('odometer_at_service');
            const selectedOption = select.options[select.selectedIndex];

            if (select.value) {
                const odometer = selectedOption.dataset.odometer;
                const plate = selectedOption.dataset.plate;

                infoDiv.innerHTML = `
                <div class="text-start">
                    <h6 class="text-primary">${plate}</h6>
                    <p class="mb-1"><strong>Current Odometer:</strong> ${number_format(odometer, 0)} km</p>
                    <p class="mb-0 text-muted">Enter service odometer reading</p>
                </div>
            `;

                // Pre-fill odometer with current reading
                odometerInput.value = odometer;
            } else {
                infoDiv.innerHTML = `
                <i class="bi bi-speedometer2 fs-1"></i>
                <p class="mt-2">Select a vehicle to view odometer information</p>
            `;
                odometerInput.value = '';
            }
        }

        function number_format(number, decimals) {
            return parseFloat(number).toFixed(decimals);
        }
    </script>
@endsection