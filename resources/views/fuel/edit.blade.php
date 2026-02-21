@extends('layouts.app')

@section('title', 'Edit Fuel Log')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Edit Fuel Log</h2>
        <a href="{{ route('fuel.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Fuel Logs
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Update Fuel Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('fuel.update', $fuelLog) }}">
                        @csrf
                        @method('PUT')

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="vehicle_id" class="form-label">Vehicle</label>
                                <select class="form-select" id="vehicle_id" name="vehicle_id" required
                                    onchange="updateOdometerInfo()">
                                    <option value="">Select Vehicle</option>
                                    @foreach($vehicles as $vehicle)
                                        <option value="{{ $vehicle->id }}" data-odometer="{{ $vehicle->odometer }}"
                                            data-plate="{{ $vehicle->license_plate }}" {{ $fuelLog->vehicle_id == $vehicle->id ? 'selected' : '' }}>
                                            {{ $vehicle->license_plate }} - {{ $vehicle->model }} (Current:
                                            {{ number_format($vehicle->odometer, 0) }} km)
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label for="fuel_date" class="form-label">Fuel Date</label>
                                <input type="date" class="form-control" id="fuel_date" name="fuel_date"
                                    value="{{ $fuelLog->fuel_date->format('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="liters" class="form-label">Liters</label>
                                <input type="number" step="0.01" min="0"
                                    class="form-control @error('liters') is-invalid @enderror" id="liters" name="liters"
                                    value="{{ $fuelLog->liters }}" required onchange="calculateTotalCost()">
                                @error('liters')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="cost_per_liter" class="form-label">Cost per Liter ($)</label>
                                <input type="number" step="0.01" min="0"
                                    class="form-control @error('cost_per_liter') is-invalid @enderror" id="cost_per_liter"
                                    name="cost_per_liter" value="{{ $fuelLog->cost_per_liter }}" required
                                    onchange="calculateTotalCost()">
                                @error('cost_per_liter')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="cost" class="form-label">Total Cost ($)</label>
                                <input type="number" step="0.01" min="0"
                                    class="form-control @error('cost') is-invalid @enderror" id="cost" name="cost"
                                    value="{{ $fuelLog->cost }}" required>
                                @error('cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Can be manually adjusted</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="odometer" class="form-label">Odometer Reading (km)</label>
                                <input type="number" step="0.01" min="0"
                                    class="form-control @error('odometer') is-invalid @enderror" id="odometer"
                                    name="odometer" value="{{ $fuelLog->odometer }}" required>
                                @error('odometer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('fuel.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-check-circle me-2"></i>Update Fuel Log
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Current Record</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted">Fuel Date</label>
                        <p class="fw-semibold">{{ $fuelLog->fuel_date->format('M d, Y H:i') }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted">Liters</label>
                        <p class="fw-semibold">{{ number_format($fuelLog->liters, 2) }} L</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted">Cost per Liter</label>
                        <p class="fw-semibold">${{ number_format($fuelLog->cost_per_liter, 2) }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted">Total Cost</label>
                        <p class="fw-semibold">${{ number_format($fuelLog->cost, 2) }}</p>
                    </div>
                    <div class="mb-3">
                        <label class="text-muted">Odometer at Fueling</label>
                        <p class="fw-semibold">{{ number_format($fuelLog->odometer, 0) }} km</p>
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
        // Initialize odometer info on page load
        document.addEventListener('DOMContentLoaded', function () {
            updateOdometerInfo();
        });

        function updateOdometerInfo() {
            const select = document.getElementById('vehicle_id');
            const infoDiv = document.getElementById('odometerInfo');
            const selectedOption = select.options[select.selectedIndex];

            if (select.value) {
                const odometer = selectedOption.dataset.odometer;
                const plate = selectedOption.dataset.plate;

                infoDiv.innerHTML = `
                <div class="text-start">
                    <h6 class="text-primary">${plate}</h6>
                    <p class="mb-1"><strong>Current Odometer:</strong> ${number_format(odometer, 0)} km</p>
                    <p class="mb-0 text-muted">Enter current odometer reading</p>
                </div>
            `;
            } else {
                infoDiv.innerHTML = `
                <i class="bi bi-speedometer2 fs-1"></i>
                <p class="mt-2">Select a vehicle to view odometer information</p>
            `;
            }
        }

        function calculateTotalCost() {
            const liters = parseFloat(document.getElementById('liters').value) || 0;
            const costPerLiter = parseFloat(document.getElementById('cost_per_liter').value) || 0;
            const totalCost = liters * costPerLiter;

            // Only auto-calculate if cost field is empty or matches the previous calculation
            const costField = document.getElementById('cost');
            if (!costField.value || parseFloat(costField.value) === 0) {
                costField.value = totalCost.toFixed(2);
            }
        }

        function number_format(number, decimals) {
            return parseFloat(number).toFixed(decimals);
        }
    </script>
@endsection