<?php $__env->startSection('title', 'Trips'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Trip Management</h2>
    <a href="<?php echo e(route('trips.create')); ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Create Trip
    </a>
</div>

<div class="card">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" id="tripSearch" placeholder="Search trips...">
                </div>
            </div>
            <div class="col-md-6 text-end">
                <span class="text-muted">Showing <?php echo e($trips->firstItem()); ?> to <?php echo e($trips->lastItem()); ?> of <?php echo e($trips->total()); ?> trips</span>
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
                        <th>Driver</th>
                        <th>Route</th>
                        <th>Cargo</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="tripTableBody">
                    <?php $__empty_1 = true; $__currentLoopData = $trips; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $trip): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <div>
                                <small class="text-muted">Created</small><br>
                                <strong><?php echo e($trip->created_at->format('M d, Y')); ?></strong>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-truck me-2 text-primary"></i>
                                <div>
                                    <div class="fw-semibold"><?php echo e($trip->vehicle->license_plate); ?></div>
                                    <small class="text-muted"><?php echo e($trip->vehicle->model); ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-2" style="width: 32px; height: 32px; font-size: 12px;">
                                    <?php echo e(strtoupper(substr($trip->driver->name, 0, 1))); ?>

                                </div>
                                <div>
                                    <div class="fw-semibold"><?php echo e($trip->driver->name); ?></div>
                                    <small class="text-muted"><?php echo e($trip->driver->phone); ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong><?php echo e($trip->origin); ?></strong><br>
                                <i class="bi bi-arrow-down text-muted"></i><br>
                                <strong><?php echo e($trip->destination); ?></strong><br>
                                <small class="text-muted"><?php echo e(number_format($trip->distance, 0)); ?> km</small>
                            </div>
                        </td>
                        <td>
                            <div>
                                <strong><?php echo e(number_format($trip->cargo_weight, 2)); ?> kg</strong><br>
                                <small class="text-muted">Capacity: <?php echo e(number_format($trip->vehicle->max_capacity, 2)); ?> kg</small>
                                <?php if(!$trip->cargo_within_capacity): ?>
                                    <br>
                                    <span class="badge bg-danger">Over Capacity</span>
                                <?php endif; ?>
                            </div>
                        </td>
                        <td>
                            <span class="status-badge status-<?php echo e($trip->status); ?>">
                                <?php echo e(ucfirst(str_replace('_', ' ', $trip->status))); ?>

                            </span>
                            <?php if($trip->status === 'completed' && $trip->arrived_late): ?>
                                <br>
                                <span class="badge bg-warning mt-1">Late</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="<?php echo e(route('trips.show', $trip)); ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <?php if($trip->status === 'draft'): ?>
                                    <a href="<?php echo e(route('trips.edit', $trip)); ?>" class="btn btn-sm btn-outline-secondary">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <form action="<?php echo e(route('trips.dispatch', $trip)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-success" title="Dispatch">
                                            <i class="bi bi-play-circle"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <?php if($trip->status === 'on_trip'): ?>
                                    <form action="<?php echo e(route('trips.complete', $trip)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-primary" title="Complete">
                                            <i class="bi bi-check-circle"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <?php if(in_array($trip->status, ['draft', 'dispatched'])): ?>
                                    <form action="<?php echo e(route('trips.cancel', $trip)); ?>" method="POST" class="d-inline">
                                        <?php echo csrf_field(); ?>
                                        <button type="submit" class="btn btn-sm btn-outline-danger" title="Cancel" onclick="return confirm('Cancel this trip?')">
                                            <i class="bi bi-x-circle"></i>
                                        </button>
                                    </form>
                                <?php endif; ?>
                                <form action="<?php echo e(route('trips.destroy', $trip)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Delete this trip?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-route fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No trips found</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        <?php echo e($trips->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
// Search functionality
document.getElementById('tripSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#tripTableBody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Hack\F_FleetFlow\resources\views/trips/index.blade.php ENDPATH**/ ?>