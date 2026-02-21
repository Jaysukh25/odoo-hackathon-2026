@extends('layouts.app')

@section('title', 'Fuel Logs')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Fuel Logs</h2>
    <a href="{{ route('fuel.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Fuel Log
    </a>
</div>

<div class="card">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" id="fuelSearch" placeholder="Search fuel records...">
                </div>
            </div>
            <div class="col-md-6 text-end">
                <span class="text-muted">Showing {{ $fuelLogs->firstItem() }} to {{ $fuelLogs->lastItem() }} of {{ $fuelLogs->total() }} records</span>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Vehicle</th>
                        <th>Liters</th>
                        <th>Cost/Liter</th>
                        <th>Total Cost</th>
                        <th>Odometer</th>
                        <th>Efficiency</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="fuelTableBody">
                    @forelse($fuelLogs as $log)
                    <tr>
                        <td>
                            <div>
                                <strong>{{ $log->fuel_date->format('M d, Y') }}</strong><br>
                                <small class="text-muted">{{ $log->fuel_date->format('H:i') }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-truck me-2 text-primary"></i>
                                <div>
                                    <div class="fw-semibold">{{ $log->vehicle->license_plate }}</div>
                                    <small class="text-muted">{{ $log->vehicle->model }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <strong>{{ number_format($log->liters, 2) }} L</strong>
                        </td>
                        <td>
                            <strong>${{ number_format($log->cost_per_liter, 2) }}</strong>
                        </td>
                        <td>
                            <strong>${{ number_format($log->cost, 2) }}</strong>
                        </td>
                        <td>
                            <div>
                                <strong>{{ number_format($log->odometer, 0) }} km</strong><br>
                                <small class="text-muted">At fueling</small>
                            </div>
                        </td>
                        <td>
                            @php
                            $previousFuel = $log->vehicle->fuelLogs()
                                ->where('fuel_date', '<', $log->fuel_date)
                                ->orderBy('fuel_date', 'desc')
                                ->first();
                            
                            $efficiency = 0;
                            if ($previousFuel && $log->odometer > $previousFuel->odometer) {
                                $distance = $log->odometer - $previousFuel->odometer;
                                $efficiency = $distance / $log->liters;
                            }
                            @endphp
                            @if($efficiency > 0)
                                <strong>{{ number_format($efficiency, 2) }} km/L</strong>
                            @else
                                <span class="text-muted">N/A</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('fuel.show', $log) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('fuel.edit', $log) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('fuel.destroy', $log) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this fuel record?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-4">
                            <i class="bi bi-fuel-pump fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No fuel records found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $fuelLogs->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
// Search functionality
document.getElementById('fuelSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#fuelTableBody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script>
@endsection
