@extends('layouts.app')

@section('title', 'Add Fuel Log')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2>Add Fuel Log</h2>
        <a href="{{ route('fuel.index') }}" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-2"></i>Back to Fuel Logs
        </a>
    </div>

    <div class="row">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Fuel Information</h5>
                </div>
                <div class="card-body">
                    <form method="POST" action="{{ route('fuel.store') }}">
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
                                <label for="fuel_date" class="form-label">Fuel Date</label>
                                <input type="date" class="form-control" id="fuel_date" name="fuel_date"
                                    value="{{ old('fuel_date') ?? now()->format('Y-m-d') }}" required>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label for="liters" class="form-label">Liters</label>
                                <input type="number" step="0.01" min="0"
                                    class="form-control @error('liters') is-invalid @enderror" id="liters" name="liters"
                                    value="{{ old('liters') }}" required onchange="calculateTotalCost()">
                                @error('liters')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="cost_per_liter" class="form-label">Cost per Liter ($)</label>
                                <input type="number" step="0.01" min="0"
                                    class="form-control @error('cost_per_liter') is-invalid @enderror" id="cost_per_liter"
                                    name="cost_per_liter" value="{{ old('cost_per_liter') }}" required
                                    onchange="calculateTotalCost()">
                                @error('cost_per_liter')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label for="cost" class="form-label">Total Cost ($)</label>
                                <input type="number" step="0.01" min="0"
                                    class="form-control @error('cost') is-invalid @enderror" id="cost" name="cost"
                                    value="{{ old('cost') }}" readonly>
                                @error('cost')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <small class="text-muted">Auto-calculated</small>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label for="odometer" class="form-label">Odometer Reading (km)</label>
                                <input type="number" step="0.01" min="0"
                                    class="form-control @error('odometer') is-invalid @enderror" id="odometer"
                                    name="odometer" value="{{ old('odometer') }}" required>
                                @error('odometer')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="d-flex justify-content-end gap-2">
                            <a href="{{ route('fuel.index') }}" class="btn btn-secondary">Cancel</a>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-plus-circle me-2"></i>Add Fuel Log
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Fuel Guidelines</h5>
                </div>
                <div class="card-body">
                    <div class="alert alert-info">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>Cost Calculation:</strong> Total cost is automatically calculated from liters × cost per
                        liter.
                    </div>
                    <div class="alert alert-warning">
                        <i class="bi bi-exclamation-triangle me-2"></i>
                        <strong>Odometer Reading:</strong> Record the exact odometer reading at the time of fueling.
                    </div>
                    <div class="alert alert-success">
                        <i class="bi bi-check-circle me-2"></i>
                        <strong>Fuel Efficiency:</strong> System calculates km/L based on previous fueling records.
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

            <div class="card mt-3">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Fuel Cost Calculator</h5>
                </div>
                <div class="card-body">
                    <div class="mb-3">
                        <label class="text-muted">Quick Calculation</label>
                        <div class="input-group">
                            <input type="number" class="form-control" id="quickLiters" placeholder="Liters" step="0.01">
                            <input type="number" class="form-control" id="quickCostPerLiter" placeholder="Cost/Liter"
                                step="0.01">
                            <button class="btn btn-outline-primary" onclick="quickCalculate()">Calculate</button>
                        </div>
                    </div>
                    <div id="quickResult" class="text-center" style="display: none;">
                        <h5 class="text-primary">Total: $<span id="quickTotal">0.00</span></h5>
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
            const odometerInput = document.getElementById('odometer');
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

        function calculateTotalCost() {
            const liters = parseFloat(document.getElementById('liters').value) || 0;
            const costPerLiter = parseFloat(document.getElementById('cost_per_liter').value) || 0;
            const totalCost = liters * costPerLiter;

            document.getElementById('cost').value = totalCost.toFixed(2);
        }

        function quickCalculate() {
            const liters = parseFloat(document.getElementById('quickLiters').value) || 0;
            const costPerLiter = parseFloat(document.getElementById('quickCostPerLiter').value) || 0;
            const total = liters * costPerLiter;

            document.getElementById('quickTotal').textContent = total.toFixed(2);
            document.getElementById('quickResult').style.display = 'block';
        }

        function number_format(number, decimals) {
            return parseFloat(number).toFixed(decimals);
        }
    </script>
@endsection