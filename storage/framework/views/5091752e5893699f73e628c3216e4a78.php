<?php $__env->startSection('title', 'Vehicles'); ?>

<?php $__env->startSection('content'); ?>
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Vehicle Management</h2>
    <a href="<?php echo e(route('vehicles.create')); ?>" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Add Vehicle
    </a>
</div>

<div class="card">
    <div class="card-header bg-white">
        <div class="row align-items-center">
            <div class="col-md-6">
                <div class="search-box">
                    <i class="bi bi-search"></i>
                    <input type="text" class="form-control" id="vehicleSearch" placeholder="Search vehicles...">
                </div>
            </div>
            <div class="col-md-6 text-end">
                <span class="text-muted">Showing <?php echo e($vehicles->firstItem()); ?> to <?php echo e($vehicles->lastItem()); ?> of <?php echo e($vehicles->total()); ?> vehicles</span>
            </div>
        </div>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>
                            <a href="#" class="text-decoration-none text-dark" onclick="sortTable('model')">
                                Model <i class="bi bi-arrow-down-up"></i>
                            </a>
                        </th>
                        <th>License Plate</th>
                        <th>Capacity</th>
                        <th>Odometer</th>
                        <th>Status</th>
                        <th>Current Trip</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody id="vehicleTableBody">
                    <?php $__empty_1 = true; $__currentLoopData = $vehicles; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $vehicle): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); $__empty_1 = false; ?>
                    <tr>
                        <td>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-truck me-2 text-primary fs-5"></i>
                                <span class="fw-semibold"><?php echo e($vehicle->model); ?></span>
                            </div>
                        </td>
                        <td><code><?php echo e($vehicle->license_plate); ?></code></td>
                        <td><?php echo e(number_format($vehicle->max_capacity, 2)); ?> kg</td>
                        <td><?php echo e(number_format($vehicle->odometer, 0)); ?> km</td>
                        <td>
                            <span class="status-badge status-<?php echo e($vehicle->status); ?>">
                                <?php echo e(ucfirst(str_replace('_', ' ', $vehicle->status))); ?>

                            </span>
                            <?php if($vehicle->out_of_service): ?>
                                <span class="badge bg-secondary ms-1">Out of Service</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if($vehicle->currentTrip): ?>
                                <div>
                                    <small class="text-muted">Driver:</small> <?php echo e($vehicle->currentTrip->driver->name); ?><br>
                                    <small class="text-muted">To:</small> <?php echo e($vehicle->currentTrip->destination); ?>

                                </div>
                            <?php else: ?>
                                <span class="text-muted">No active trip</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group" role="group">
                                <a href="<?php echo e(route('vehicles.show', $vehicle)); ?>" class="btn btn-sm btn-outline-primary">
                                    <i class="bi bi-eye"></i>
                                </a>
                                <a href="<?php echo e(route('vehicles.edit', $vehicle)); ?>" class="btn btn-sm btn-outline-secondary">
                                    <i class="bi bi-pencil"></i>
                                </a>
                                <button type="button" class="btn btn-sm btn-outline-warning" onclick="toggleStatus(<?php echo e($vehicle->id); ?>)">
                                    <i class="bi bi-power"></i>
                                </button>
                                <form action="<?php echo e(route('vehicles.destroy', $vehicle)); ?>" method="POST" class="d-inline">
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
                            <i class="bi bi-truck fs-1 text-muted"></i>
                            <p class="text-muted mt-2">No vehicles found</p>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="card-footer bg-white">
        <?php echo e($vehicles->links()); ?>

    </div>
</div>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
<script>
function sortTable(column) {
    // Implement sorting logic here
    console.log('Sorting by:', column);
}

function toggleStatus(vehicleId) {
    if (confirm('Toggle vehicle service status?')) {
        fetch(`/vehicles/${vehicleId}/toggle-status`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            }
        })
        .catch(error => console.error('Error:', error));
    }
}

// Search functionality
document.getElementById('vehicleSearch').addEventListener('input', function(e) {
    const searchTerm = e.target.value.toLowerCase();
    const rows = document.querySelectorAll('#vehicleTableBody tr');
    
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
});
</script>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('layouts.app', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Hack\F_FleetFlow\resources\views/vehicles/index.blade.php ENDPATH**/ ?>