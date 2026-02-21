<?php $__env->startSection('title', 'Dashboard'); ?>

<?php $__env->startSection('content'); ?>
    <!-- KPI Cards -->
    <div class="row mb-4">
        <div class="col-xl-3 col-md-6 mb-4">
            <div class="kpi-card">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-number"><?php echo e($activeVehicles); ?></div>
                        <div class="kpi-label">Active Vehicles</div>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-circle p-3">
                        <i class="bi bi-truck fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="kpi-card" style="background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-number"><?php echo e($tripsToday); ?></div>
                        <div class="kpi-label">Trips Today</div>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-circle p-3">
                        <i class="bi bi-route fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="kpi-card" style="background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-number"><?php echo e($vehiclesInShop); ?></div>
                        <div class="kpi-label">Vehicles In Shop</div>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-circle p-3">
                        <i class="bi bi-tools fs-4"></i>
                    </div>
                </div>
            </div>
        </div>

        <div class="col-xl-3 col-md-6 mb-4">
            <div class="kpi-card" style="background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <div class="kpi-number">$<?php echo e(number_format($monthlyOperationalCost, 0)); ?></div>
                        <div class="kpi-label">Monthly Operational Cost</div>
                    </div>
                    <div class="bg-white bg-opacity-20 rounded-circle p-3">
                        <i class="bi bi-currency-dollar fs-4"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Maintenance Alerts -->
    <?php if($maintenanceAlerts->count() > 0): ?>
        <div class="alert alert-warning alert-dismissible fade show" role="alert">
            <i class="bi bi-exclamation-triangle me-2"></i>
            <strong>Predictive Maintenance Alert:</strong> <?php echo e($maintenanceAlerts->count()); ?> vehicle(s) require maintenance
            based on odometer readings.
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    <?php endif; ?>

    <!-- Charts Row -->
    <div class="row mb-4">
        <div class="col-xl-8">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Trip Activity (Last 30 Days)</h5>
                </div>
                <div class="card-body">
                    <canvas id="tripActivityChart" height="100"></canvas>
                </div>
            </div>
        </div>

        <div class="col-xl-4">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Driver Safety Distribution</h5>
                </div>
                <div class="card-body">
                    <canvas id="driverSafetyChart" height="200"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Live Fleet Table -->
    <div class="row mb-4">
        <div class="col-xl-9">
            <div class="card">
                <div class="card-header bg-white d-flex justify-content-between align-items-center">
                    <h5 class="card-title mb-0">Live Fleet Status</h5>
                    <div class="d-flex gap-2">
                        <span class="badge bg-success">Available</span>
                        <span class="badge bg-primary">On Trip</span>
                        <span class="badge bg-danger">In Shop</span>
                    </div>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Vehicle</th>
                                    <th>Driver</th>
                                    <th>Status</th>
                                    <th>Load</th>
                                    <th>Destination</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php $__empty_1 = true; $__currentLoopData = $liveFleet; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $fleet): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <i class="bi bi-truck me-2 text-primary"></i>
                                                <div>
                                                    <div class="fw-semibold"><?php echo e($fleet['model']); ?></div>
                                                    <small class="text-muted"><?php echo e($fleet['license_plate']); ?></small>
                                                </div>
                                            </div>
                                        </td>
                                        <td><?php echo e($fleet['driver']); ?></td>
                                        <td>
                                            <span class="status-badge status-<?php echo e($fleet['status']); ?>">
                                                <?php echo e(ucfirst(str_replace('_', ' ', $fleet['status']))); ?>

                                            </span>
                                        </td>
                                        <td><?php echo e(number_format($fleet['load'], 2)); ?> kg</td>
                                        <td><?php echo e($fleet['destination']); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-outline-primary fleet-view-btn"
                                                data-id="<?php echo e($fleet['id']); ?>" title="View Details">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                                    <tr>
                                        <td colspan="6" class="text-center py-4">
                                            <i class="bi bi-truck text-muted" style="font-size:2rem;"></i>
                                            <p class="text-muted mt-2 mb-0">No active fleet vehicles found</p>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Activity -->
        <div class="col-xl-3">
            <div class="card">
                <div class="card-header bg-white">
                    <h5 class="card-title mb-0">Recent Activity</h5>
                </div>
                <div class="card-body" style="max-height: 400px; overflow-y: auto;">
                    <?php $__empty_1 = true; $__currentLoopData = $recentActivity; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $activity): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                        <div class="activity-item <?php echo e($activity['color']); ?>">
                            <div class="d-flex align-items-start">
                                <i class="bi bi-<?php echo e($activity['icon']); ?> me-2 text-<?php echo e($activity['color']); ?>"></i>
                                <div class="flex-grow-1">
                                    <div class="small"><?php echo e($activity['message']); ?></div>
                                    <small class="text-muted"><?php echo e($activity['time']->diffForHumans()); ?></small>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                        <div class="text-center text-muted">
                            <i class="bi bi-clock fs-1"></i>
                            <p class="mt-2">No recent activity</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    
    <div class="modal fade" id="fleetDetailModal" tabindex="-1" aria-labelledby="fleetDetailLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content border-0 shadow-lg" style="border-radius:16px;overflow:hidden;">

                
                <div id="fleetModalLoading" class="text-center py-5">
                    <div class="spinner-border text-primary" role="status" style="width:2.5rem;height:2.5rem;"></div>
                    <p class="mt-3 text-muted">Fetching fleet data…</p>
                </div>

                
                <div id="fleetModalError" class="d-none text-center py-5">
                    <i class="bi bi-exclamation-triangle text-danger" style="font-size:2.5rem;"></i>
                    <p class="mt-3 text-muted">Could not load vehicle details. Please try again.</p>
                    <button class="btn btn-sm btn-outline-secondary" data-bs-dismiss="modal">Close</button>
                </div>

                
                <div id="fleetModalContent" class="d-none">
                    
                    <div id="fleetModalHeader"
                        style="background:linear-gradient(135deg,#667eea,#764ba2);padding:1.5rem 1.75rem;">
                        <div class="d-flex align-items-center justify-content-between">
                            <div class="d-flex align-items-center gap-3">
                                <div
                                    style="width:52px;height:52px;border-radius:14px;background:rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;">
                                    <i class="bi bi-truck-front-fill text-white fs-4"></i>
                                </div>
                                <div>
                                    <h5 class="modal-title text-white mb-0 fw-bold" id="fleetDetailLabel">Vehicle Details
                                    </h5>
                                    <small id="fm-license" class="text-white opacity-75"></small>
                                </div>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <span id="fm-status-badge" class="badge fs-6 px-3 py-2"></span>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                        </div>
                    </div>

                    <div class="modal-body p-0">
                        
                        <div class="row g-0 border-bottom">
                            <div class="col-4 text-center py-3 border-end">
                                <div class="small text-muted">Odometer</div>
                                <div class="fw-bold mt-1" id="fm-odometer">—</div>
                            </div>
                            <div class="col-4 text-center py-3 border-end">
                                <div class="small text-muted">Max Capacity</div>
                                <div class="fw-bold mt-1" id="fm-capacity">—</div>
                            </div>
                            <div class="col-4 text-center py-3">
                                <div class="small text-muted">Total Trips</div>
                                <div class="fw-bold mt-1" id="fm-trips">—</div>
                            </div>
                        </div>

                        <div class="p-4">
                            
                            <div class="row mb-4">
                                <div class="col-sm-6">
                                    <div class="small text-muted mb-1"><i class="bi bi-geo-alt me-1"></i> Current Location
                                    </div>
                                    <div class="fw-semibold" id="fm-location">—</div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="small text-muted mb-1"><i class="bi bi-clock-history me-1"></i> Last Update
                                    </div>
                                    <div class="fw-semibold" id="fm-last-update">—</div>
                                </div>
                            </div>

                            
                            <div id="fm-trip-section" class="mb-4 d-none">
                                <h6 class="text-uppercase text-muted small fw-bold mb-3">
                                    <i class="bi bi-route me-1"></i> Current Trip
                                </h6>
                                <div class="rounded-3 p-3" style="background:#f8f9ff;border:1px solid #e0e7ff;">
                                    <div class="row gy-2">
                                        <div class="col-sm-6">
                                            <span class="text-muted small">Driver</span>
                                            <div class="fw-semibold" id="fm-driver">—</div>
                                        </div>
                                        <div class="col-sm-6">
                                            <span class="text-muted small">Destination</span>
                                            <div class="fw-semibold" id="fm-destination">—</div>
                                        </div>
                                        <div class="col-sm-6">
                                            <span class="text-muted small">Cargo Weight</span>
                                            <div class="fw-semibold" id="fm-cargo">—</div>
                                        </div>
                                        <div class="col-sm-6">
                                            <span class="text-muted small">Dispatched At</span>
                                            <div class="fw-semibold" id="fm-dispatched">—</div>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div id="fm-no-trip" class="mb-4 d-none">
                                <div class="rounded-3 p-3 text-center text-muted"
                                    style="background:#f8fafc;border:1px dashed #cbd5e1;">
                                    <i class="bi bi-inbox me-2"></i> No active trip assigned
                                </div>
                            </div>

                            
                            <h6 class="text-uppercase text-muted small fw-bold mb-3">
                                <i class="bi bi-currency-dollar me-1"></i> Cost Summary
                            </h6>
                            <div class="row g-3 mb-4">
                                <div class="col-sm-4">
                                    <div class="rounded-3 p-3 text-center"
                                        style="background:#f0fdf4;border:1px solid #bbf7d0;">
                                        <div class="small text-muted">Fuel Cost</div>
                                        <div class="fw-bold text-success mt-1" id="fm-fuel-cost">—</div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="rounded-3 p-3 text-center"
                                        style="background:#fff7ed;border:1px solid #fed7aa;">
                                        <div class="small text-muted">Maintenance</div>
                                        <div class="fw-bold text-warning mt-1" id="fm-maint-cost">—</div>
                                    </div>
                                </div>
                                <div class="col-sm-4">
                                    <div class="rounded-3 p-3 text-center"
                                        style="background:#f5f3ff;border:1px solid #ddd6fe;">
                                        <div class="small text-muted">Total Cost</div>
                                        <div class="fw-bold mt-1" style="color:#7c3aed;" id="fm-total-cost">—</div>
                                    </div>
                                </div>
                            </div>

                            
                            <h6 class="text-uppercase text-muted small fw-bold mb-3">
                                <i class="bi bi-tools me-1"></i> Last Maintenance
                            </h6>
                            <div id="fm-maint-info" class="rounded-3 p-3"
                                style="background:#fffbeb;border:1px solid #fde68a;">
                                <div class="row gy-1">
                                    <div class="col-sm-6">
                                        <span class="text-muted small">Service Type</span>
                                        <div class="fw-semibold" id="fm-maint-type">—</div>
                                    </div>
                                    <div class="col-sm-6">
                                        <span class="text-muted small">Performed At</span>
                                        <div class="fw-semibold" id="fm-maint-date">—</div>
                                    </div>
                                </div>
                            </div>
                            <div id="fm-no-maint" class="d-none rounded-3 p-3 text-center text-muted"
                                style="background:#f8fafc;border:1px dashed #cbd5e1;">
                                <i class="bi bi-check-circle me-2 text-success"></i> No maintenance on record
                            </div>
                        </div>
                    </div>

                    <div class="modal-footer border-top">
                        <a id="fm-full-link" href="#" class="btn btn-primary btn-sm">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Full Vehicle Profile
                        </a>
                        <button type="button" class="btn btn-outline-secondary btn-sm"
                            data-bs-dismiss="modal">Close</button>
                    </div>
                </div>

            </div>
        </div>
<?php $__env->stopSection(); ?>

    <?php $__env->startSection('scripts'); ?>
        <script>
            // ── Fleet Detail Modal ──────────────────────────────────────────
            const FLEET_ENDPOINT = '<?php echo e(route("dashboard.fleet.detail", ":id")); ?>';

            // Use event delegation for the eye buttons for maximum reliability
            document.addEventListener('click', function (e) {
                const btn = e.target.closest('.fleet-view-btn');
                if (btn) {
                    e.preventDefault();
                    openFleetModal(btn.dataset.id);
                }
            });

            function openFleetModal(id) {
                const modalEl = document.getElementById('fleetDetailModal');
                if (!modalEl) return;

                // Reset states
                document.getElementById('fleetModalLoading').classList.remove('d-none');
                document.getElementById('fleetModalError').classList.add('d-none');
                document.getElementById('fleetModalContent').classList.add('d-none');

                // Show modal using Bootstrap instance
                const modal = bootstrap.Modal.getOrCreateInstance(modalEl);
                modal.show();

                fetch(FLEET_ENDPOINT.replace(':id', id), {
                    headers: {
                        'Accept': 'application/json',
                        'X-Requested-With': 'XMLHttpRequest'
                    }
                })
                    .then(res => {
                        if (!res.ok) throw new Error('Network response was not ok');
                        return res.json();
                    })
                    .then(data => populateModal(data))
                    .catch(err => {
                        console.error('Modal Fetch Error:', err);
                        document.getElementById('fleetModalLoading').classList.add('d-none');
                        document.getElementById('fleetModalError').classList.remove('d-none');
                    });
            }

            function populateModal(v) {
                // Fill Header & Meta
                document.getElementById('fleetDetailLabel').textContent = v.model || 'Unknown Vehicle';
                document.getElementById('fm-license').textContent = v.license_plate || '—';
                document.getElementById('fm-location').textContent = v.location || 'Unknown';
                document.getElementById('fm-last-update').textContent = v.last_updated || '—';

                // Status Badge
                const statusMap = {
                    available: ['#10b981', 'Available'],
                    on_trip: ['#3b82f6', 'On Trip'],
                    in_shop: ['#ef4444', 'In Shop'],
                    out_of_service: ['#6b7280', 'Out of Service'],
                };
                const [color, label] = statusMap[v.status] ?? ['#6b7280', v.status || 'Unknown'];
                const badge = document.getElementById('fm-status-badge');
                badge.textContent = label;
                badge.style.background = color;

                // Stats
                document.getElementById('fm-odometer').textContent = Number(v.odometer || 0).toLocaleString() + ' km';
                document.getElementById('fm-capacity').textContent = Number(v.max_capacity || 0).toLocaleString() + ' kg';
                document.getElementById('fm-trips').textContent = v.trips_count || 0;

                // Current Trip Section
                const tripSection = document.getElementById('fm-trip-section');
                const noTrip = document.getElementById('fm-no-trip');
                if (v.current_trip) {
                    tripSection.classList.remove('d-none');
                    noTrip.classList.add('d-none');
                    document.getElementById('fm-driver').textContent = v.current_trip.driver || 'Unassigned';
                    document.getElementById('fm-destination').textContent = v.current_trip.destination || '—';
                    document.getElementById('fm-cargo').textContent = Number(v.current_trip.cargo_weight || 0).toLocaleString() + ' kg';
                    document.getElementById('fm-dispatched').textContent = v.current_trip.dispatched_at || '—';
                } else {
                    tripSection.classList.add('d-none');
                    noTrip.classList.remove('d-none');
                }

                // Costs
                document.getElementById('fm-fuel-cost').textContent = 'Rs ' + Number(v.fuel_cost || 0).toLocaleString();
                document.getElementById('fm-maint-cost').textContent = 'Rs ' + Number(v.maintenance_cost || 0).toLocaleString();
                document.getElementById('fm-total-cost').textContent = 'Rs ' + Number(v.total_cost || 0).toLocaleString();

                // Maintenance Section
                const maintInfo = document.getElementById('fm-maint-info');
                const noMaint = document.getElementById('fm-no-maint');
                if (v.last_maintenance) {
                    maintInfo.classList.remove('d-none');
                    noMaint.classList.add('d-none');
                    document.getElementById('fm-maint-type').textContent = v.last_maintenance.service_type || 'General Service';
                    document.getElementById('fm-maint-date').textContent = v.last_maintenance.performed_at || '—';
                } else {
                    maintInfo.classList.add('d-none');
                    noMaint.classList.remove('d-none');
                }

                // Full Profile Link
                document.getElementById('fm-full-link').href = '/vehicles/' + v.id;

                // Show Content
                document.getElementById('fleetModalLoading').classList.add('d-none');
                document.getElementById('fleetModalContent').classList.remove('d-none');
            }

            // ── Dashboard Charts ──────────────────────────────────────────
            const tripActivityCtx = document.getElementById('tripActivityChart').getContext('2d');
            const tripActivityChart = new Chart(tripActivityCtx, {
                type: 'bar',
                data: {
                    labels: <?php echo json_encode($tripActivity->pluck('date')->map(fn($date) => \Carbon\Carbon::parse($date)->format('M d')), 15, 512) ?>,
                    datasets: [{
                        label: 'Trips',
                        data: <?php echo json_encode($tripActivity->pluck('count'), 15, 512) ?>,
                        backgroundColor: 'rgba(99, 102, 241, 0.5)',
                        borderColor: 'rgba(99, 102, 241, 1)',
                        borderWidth: 1
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
                }
            });

            const driverSafetyCtx = document.getElementById('driverSafetyChart').getContext('2d');
            const driverSafetyChart = new Chart(driverSafetyCtx, {
                type: 'doughnut',
                data: {
                    labels: ['SAFE', 'MODERATE', 'RISKY'],
                    datasets: [{
                        data: [<?php echo e($driverSafetyData['SAFE']); ?>, <?php echo e($driverSafetyData['MODERATE']); ?>, <?php echo e($driverSafetyData['RISKY']); ?>],
                        backgroundColor: ['rgba(16,185,129,0.8)', 'rgba(245,158,11,0.8)', 'rgba(239,68,68,0.8)'],
                        borderWidth: 0
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: { legend: { position: 'bottom' } }
                }
            });
        </script>
    <?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Hack\F_FleetFlow\resources\views/dashboard.blade.php ENDPATH**/ ?>