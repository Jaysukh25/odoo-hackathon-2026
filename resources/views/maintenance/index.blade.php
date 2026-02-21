@extends('layouts.app')

@section('title', 'Maintenance')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Maintenance Logs</h2>
    <a href="{{ route('maintenance.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Maintenance
    </a>
</div>

<div class="card">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" id="maintenanceSearch" placeholder="Search maintenance records...">
                </div>
            </div>
            <div class="col-md-6 text-end">
                <span class="text-muted">Showing {{ $maintenanceLogs->firstItem() }} to {{ $maintenanceLogs->lastItem() }} of {{ $maintenanceLogs->total() }} records</span>
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
                        <th>Type</th>
                        <th>Description</th>
                        <th>Cost</th>
                        <th>Odometer</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="maintenanceTableBody">
                    @forelse($maintenanceLogs as $log)
                    <tr>
                        <td>
                            <div>
                                <strong>{{ $log->performed_at->format('M d, Y') }}</strong><br>
                                <small class="text-muted">{{ $log->performed_at->format('H:i') }}</small>
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
                            <span class="badge bg-info">{{ $log->type }}</span>
                        </td>
                        <td>
                            <div>
                                <strong>{{ Str::limit($log->description, 50) }}</strong>
                                @if(strlen($log->description) > 50)
                                    <br>
                                    <small class="text-muted">{{ $log->description }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <strong>${{ number_format($log->cost, 2) }}</strong>
                        </td>
                        <td>
                            <div>
                                <strong>{{ number_format($log->odometer_at_service, 0) }} km</strong><br>
                                <small class="text-muted">At service</small>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="{{ route('maintenance.show', $log) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="{{ route('maintenance.edit', $log) }}" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="{{ route('maintenance.destroy', $log) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this maintenance record?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-tools fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No maintenance records found</p>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        {{ $maintenanceLogs->links() }}
    </div>
</div>
@endsection

@section('scripts')
<script>
// Search functionality
document.getElementById('maintenanceSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#maintenanceTableBody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script>
@endsection
