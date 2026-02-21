<?php $__env->startSection('title', 'Drivers'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Driver Management</h2>
    <a href="<?php echo e(route('drivers.create')); ?>" class="btn btn-primary">
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
                <span class="text-muted">Showing <?php echo e($drivers->firstItem()); ?> to <?php echo e($drivers->lastItem()); ?> of <?php echo e($drivers->total()); ?> drivers</span>
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
                    <?php $__empty_1 = true; $__currentLoopData = $drivers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $driver): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 40px; height: 40px;">
                                    <?php echo e(strtoupper(substr($driver->name, 0, 1))); ?>

                                </div>
                                <div>
                                    <div class="fw-semibold"><?php echo e($driver->name); ?></div>
                                    <small class="text-muted">ID: <?php echo e($driver->id); ?></small>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div>
                                <code><?php echo e($driver->license_number); ?></code><br>
                                <small class="text-muted">Expires: <?php echo e($driver->license_expiry->format('M d, Y')); ?></small>
                            </div>
                            <?php if($driver->license_expiring_soon): ?>
                                <span class="badge bg-warning mt-1">
                                    <i class="bi bi-exclamation-triangle me-1"></i>
                                    Expiring Soon
                                </span>
                            <?php endif; ?>
                            <?php if($driver->license_expired): ?>
                                <span class="badge bg-danger mt-1">
                                    <i class="bi bi-x-circle me-1"></i>
                                    Expired
                                </span>
                            <?php endif; ?>
                        </td>
                        <td><?php echo e($driver->phone); ?></td>
                        <td>
                            <span class="status-badge status-<?php echo e($driver->status); ?>">
                                <?php echo e(ucfirst(str_replace('_', ' ', $driver->status))); ?>

                            </span>
                        </td>
                        <td>
                            <span class="badge bg-<?php echo e($driver->risk_color); ?>">
                                <?php echo e($driver->risk_level); ?>

                            </span>
                            <br>
                            <small class="text-muted">Score: <?php echo e($driver->risk_score); ?></small>
                        </td>
                        <td>
                            <?php if($driver->currentTrip): ?>
                                <div>
                                    <small class="text-muted">Vehicle:</small> <?php echo e($driver->currentTrip->vehicle->license_plate); ?><br>
                                    <small class="text-muted">To:</small> <?php echo e($driver->currentTrip->destination); ?>

                                </div>
                            <?php else: ?>
                                <span class="text-muted">No active trip</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="<?php echo e(route('drivers.show', $driver)); ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo e(route('drivers.edit', $driver)); ?>" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <form action="<?php echo e(route('drivers.destroy', $driver)); ?>" method="POST" class="d-inline">
                                    <?php echo csrf_field(); ?>
                                    <?php echo method_field('DELETE'); ?>
                                    <button type="submit" class="btn btn-sm btn-outline-danger" onclick="return confirm('Are you sure?')">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); if ($__empty_1): ?>
                    <tr>
                        <td colspan="7" class="text-center py-4">
                            <i class="bi bi-person-badge fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No drivers found</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        <?php echo e($drivers->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
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
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Hack\F_FleetFlow\resources\views/drivers/index.blade.php ENDPATH**/ ?>