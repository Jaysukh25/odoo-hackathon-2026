@extends('layouts.app')

@section('title', 'Analytics')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Fleet Analytics</h2>
    <a href="{{ route('analytics.export') }}" class="btn btn-success">
        <i class="bi bi-download me-2"></i>Export CSV
    </a>
</div>

<!-- Fuel Efficiency Analysis -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">Fuel Efficiency Analysis</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Vehicle</th>
                        <th>Model</th>
                        <th>Total Distance (km)</th>
                        <th>Total Fuel (L)</th>
                        <th>Efficiency (km/L)</th>
                        <th>Performance</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($fuelEfficiency as $data)
                    <tr>
                        <td><strong>{{ $data['vehicle'] }}</strong></td>
                        <td>{{ $data['model'] }}</td>
                        <td>{{ number_format($data['distance'], 0) }}</td>
                        <td>{{ number_format($data['fuel'], 2) }}</td>
                        <td>
                            <strong>{{ number_format($data['efficiency'], 2) }}</strong>
                            @if($data['efficiency'] > 0)
                                <br>
                                <small class="text-muted">km per liter</small>
                            @endif
                        </td>
                        <td>
                            @php
                            $avgEfficiency = $fuelEfficiency->avg('efficiency');
                            $performance = $data['efficiency'] > 0 ? ($data['efficiency'] / $avgEfficiency) * 100 : 0;
                            @endphp
                            @if($performance >= 110)
                                <span class="badge bg-success">Excellent</span>
                            @elseif($performance >= 90)
                                <span class="badge bg-primary">Good</span>
                            @elseif($performance >= 70)
                                <span class="badge bg-warning">Average</span>
                            @else
                                <span class="badge bg-danger">Poor</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center text-muted">No fuel efficiency data available</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Vehicle ROI Analysis -->
<div class="card mb-4">
    <div class="card-header bg-white">
        <h5 class="card-title mb-0">Vehicle Return on Investment (ROI)</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th>Vehicle</th>
                        <th>Model</th>
                        <th>Revenue</th>
                        <th>Fuel Cost</th>
                        <th>Maintenance Cost</th>
                        <th>Total Costs</th>
                        <th>ROI %</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($vehicleROI as $data)
                    <tr>
                        <td><strong>{{ $data['vehicle'] }}</strong></td>
                        <td>{{ $data['model'] }}</td>
                        <td>${{ number_format($data['revenue'], 2) }}</td>
                        <td>${{ number_format($data['fuel_cost'], 2) }}</td>
                        <td>${{ number_format($data['maintenance_cost'], 2) }}</td>
                        <td><strong>${{ number_format($data['total_costs'], 2) }}</strong></td>
                        <td>
                            <strong>{{ number_format($data['roi_percentage'], 1) }}%</strong>
                        </td>
                        <td>
                            @if($data['roi_percentage'] >= 50)
                                <span class="badge bg-success">High ROI</span>
                            @elseif($data['roi_percentage'] >= 20)
                                <span class="badge bg-primary">Good ROI</span>
                            @elseif($data['roi_percentage'] >= 0)
                                <span class="badge bg-warning">Low ROI</span>
                            @else
                                <span class="badge bg-danger">Negative ROI</span>
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted">No ROI data available</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<div class="row">
    <div class="col-lg-8">
        <!-- Monthly Cost Trends -->
        <div class="card mb-4">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Monthly Cost Trends (Last 6 Months)</h5>
            </div>
            <div class="card-body">
                <canvas id="monthlyCostsChart" height="100"></canvas>
            </div>
        </div>
    </div>
    
    <div class="col-lg-4">
        <!-- Top Performers -->
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="card-title mb-0">Top Performing Vehicles</h5>
            </div>
            <div class="card-body">
                @forelse($topPerformers as $index => $performer)
                <div class="d-flex align-items-center mb-3">
                    <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                        {{ $index + 1 }}
                    </div>
                    <div class="flex-grow-1">
                        <h6 class="mb-0">{{ $performer['vehicle'] }}</h6>
                        <small class="text-muted">{{ $performer['completed_trips'] }} trips • {{ number_format($performer['total_distance'], 0) }} km</small>
                    </div>
                    <div class="text-end">
                        <strong>{{ number_format($performer['avg_distance_per_trip'], 0) }} km</strong>
                        <br>
                        <small class="text-muted">avg/trip</small>
                    </div>
                </div>
                @empty
                <p class="text-muted text-center">No performance data available</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Summary Statistics -->
<div class="row">
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-fuel-pump fs-1 text-primary mb-2"></i>
                <h5 class="card-title">Avg Fuel Efficiency</h5>
                <p class="card-text fs-4">
                    {{ number_format($fuelEfficiency->avg('efficiency'), 2) }} km/L
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-graph-up fs-1 text-success mb-2"></i>
                <h5 class="card-title">Average ROI</h5>
                <p class="card-text fs-4">
                    {{ number_format($vehicleROI->avg('roi_percentage'), 1) }}%
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-currency-dollar fs-1 text-warning mb-2"></i>
                <h5 class="card-title">Total Revenue</h5>
                <p class="card-text fs-4">
                    ${{ number_format($vehicleROI->sum('revenue'), 0) }}
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-3">
        <div class="card text-center">
            <div class="card-body">
                <i class="bi bi-tools fs-1 text-danger mb-2"></i>
                <h5 class="card-title">Total Costs</h5>
                <p class="card-text fs-4">
                    ${{ number_format($vehicleROI->sum('total_costs'), 0) }}
                </p>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
// Monthly Costs Chart
const monthlyCostsCtx = document.getElementById('monthlyCostsChart').getContext('2d');
const monthlyCostsChart = new Chart(monthlyCostsCtx, {
    type: 'line',
    data: {
        labels: @json($monthlyCosts->pluck('month')),
        datasets: [
            {
                label: 'Fuel Costs',
                data: @json($monthlyCosts->pluck('fuel_cost')),
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.1
            },
            {
                label: 'Maintenance Costs',
                data: @json($monthlyCosts->pluck('maintenance_cost')),
                borderColor: 'rgb(239, 68, 68)',
                backgroundColor: 'rgba(239, 68, 68, 0.1)',
                tension: 0.1
            },
            {
                label: 'Total Costs',
                data: @json($monthlyCosts->pluck('total_cost')),
                borderColor: 'rgb(34, 197, 94)',
                backgroundColor: 'rgba(34, 197, 94, 0.1)',
                tension: 0.1
            }
        ]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'top',
            },
            title: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        }
    }
});
</script>
@endsection
