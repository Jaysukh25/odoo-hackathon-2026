@extends('layouts.app')

@section('title', 'Drivers')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Driver Management</h2>
    <a href="{{ route('drivers.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Driver
    </a>
</div>

<div class="card">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" id="driverSearch" placeholder="Search drivers...">
                </div>
            </div>
            <div class="col-md-6 text-end">
                <span class="text-muted">Showing {{ $drivers->firstItem() }} to {{ $drivers->lastItem() }} of {{ $drivers->total() }} drivers</span>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>License</th>
                        <th>Phone</th>
                        <th>Status</th>
                        <th>Risk Level</th>
                        <th>Current Trip</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="driverTableBody">
                    @forelse($drivers as $driver)
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    {{ strtoupper(substr($driver->name, 0, 1)) }}
                                </div>
                                <div>
                                    <div class="fw-semibold">{{ $driver->name }}</div>
                                    <small class="text-muted">ID: {{ $driver->id }}</small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <code>{{ $driver->license_number }}</code><br>
                                <small class="text-muted">Expires: {{ $driver->license_expiry->format('M d, Y') }}</small>
                            </div>
                            @if($driver->license_expiring_soon)
                                <span class="badge bg-warning mt-1">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Expiring Soon
                                </span>
                            @endif
                            @if($driver->license_expired)
                                <span class="badge bg-danger mt-1">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Expired
                                </span>
                            @endif
                        </td>
                        <td>{{ $driver->phone }}</td>
                        <td>
                            <span class="status-badge status-{{ $driver->status }}">
                                {{ ucfirst(str_replace('_', ' ', $driver->status)) }}
                            </span>
                        </td>
                        <td>
                            <span class="badge bg-{{ $driver->risk_color }}">
                                {{ $driver->risk_level }}
                            </span>
                            <br>
                            <small class="text-muted">Score: {{ $driver->risk_score }}</small>
                        </td>
                        <td>
                            @if($driver->currentTrip)
                                <div>
                                    <small class="text-muted">Vehicle:</small> {{ $driver->currentTrip->vehicle->license_plate }}<br>
                                    <small class="text-muted">To:</small> {{ $driver->currentTrip->destination }}
                                </div>
                            @else
                                <span class="text-muted">No active trip</span>
                            @endif
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('drivers.show', $driver) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('drivers.edit', $driver) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('drivers.destroy', $driver) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-person-badge fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No drivers found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $drivers->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
// Search functionality
document.getElementById('driverSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#driverTableBody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script>
@endsection
