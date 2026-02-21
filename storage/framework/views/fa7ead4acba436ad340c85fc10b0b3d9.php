<?php $__env->startSection('title', 'Login'); ?>

<?php $__env->startSection('content'); ?>
    <form method="POST" action="<?php echo e(route('login')); ?>" id="loginForm">
        <?php echo csrf_field(); ?>

        <div class="mb-4">
            <label for="email" class="form-label">
                <i class="bi bi-envelope me-1"></i> Email Address
            </label>
            <input type="email" class="form-control <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="email" name="email"
                value="<?php echo e(old('email')); ?>" placeholder="you@example.com" required autofocus autocomplete="off">
            <?php $__errorArgs = ['email'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                <div class="invalid-feedback"><?php echo e($message); ?></div>
            <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
        </div>

        <div class="mb-4">
            <label for="password" class="form-label">
                <i class="bi bi-lock me-1"></i> Password
            </label>
            <div class="position-relative">
                <input type="password" class="form-control <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?> is-invalid <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>" id="password"
                    name="password" placeholder="Enter your password" required autocomplete="off"
                    style="padding-right: 3rem;">
                <button type="button" onclick="togglePassword()" style="
                        position: absolute; right: 0.85rem; top: 50%; transform: translateY(-50%);
                        background: none; border: none; color: rgba(255,255,255,0.4); cursor: pointer;
                        transition: color 0.2s ease; padding: 0;" id="eyeBtn">
                    <i class="bi bi-eye" id="eyeIcon"></i>
                </button>
                <?php $__errorArgs = ['password'];
$__bag = $errors->getBag($__errorArgs[1] ?? 'default');
if ($__bag->has($__errorArgs[0])) :
if (isset($message)) { $__messageOriginal = $message; }
$message = $__bag->first($__errorArgs[0]); ?>
                    <div class="invalid-feedback"><?php echo e($message); ?></div>
                <?php unset($message);
if (isset($__messageOriginal)) { $message = $__messageOriginal; }
endif;
unset($__errorArgs, $__bag); ?>
            </div>
        </div>

        <div class="d-flex align-items-center justify-content-between mb-4">
            <div class="form-check">
                <input type="checkbox" class="form-check-input" id="remember" name="remember">
                <label class="form-check-label" for="remember">Remember me</label>
            </div>
        </div>

        <button type="submit" class="btn-auth" id="loginBtn">
            <span id="btnText"><i class="bi bi-box-arrow-in-right me-2"></i>Sign In</span>
            <span id="btnSpinner" class="d-none">
                <span class="spinner-border spinner-border-sm me-2"></span>Signing in...
            </span>
        </button>
    </form>

    <?php if(app()->environment('local') || config('app.debug')): ?>
        <hr class="auth-divider">

        <div class="demo-accounts">
            <div class="demo-title"><i class="bi bi-info-circle me-1"></i> Demo credentials (password: password)</div>

            <div class="demo-row">
                <span class="demo-role role-manager-pill">Manager</span>
                <span class="demo-email">manager@fleetflow.com</span>
            </div>
            <div class="demo-row">
                <span class="demo-role role-dispatcher-pill">Dispatcher</span>
                <span class="demo-email">dispatcher@fleetflow.com</span>
            </div>
            <div class="demo-row">
                <span class="demo-role role-safety-pill">Safety</span>
                <span class="demo-email">safety@fleetflow.com</span>
            </div>
            <div class="demo-row">
                <span class="demo-role role-finance-pill">Finance</span>
                <span class="demo-email">finance@fleetflow.com</span>
            </div>
        </div>
    <?php endif; ?>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('scripts'); ?>
    <script>
        // Toggle password visibility
        function togglePassword() {
            const field = document.getElementById('password');
            const icon = document.getElementById('eyeIcon');
            if (field.type === 'password') {
                field.type = 'text';
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            } else {
                field.type = 'password';
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            }
        }

        // Show spinner on submit
        document.getElementById('loginForm').addEventListener('submit', function () {
            document.getElementById('btnText').classList.add('d-none');
            document.getElementById('btnSpinner').classList.remove('d-none');
            document.getElementById('loginBtn').disabled = true;
        });

        <?php if(app()->environment('local') || config('app.debug')): ?>
            // Auto-fill demo credentials on badge click
            document.querySelectorAll('.demo-row').forEach(function (row) {
                row.style.cursor = 'pointer';
                row.title = 'Click to auto-fill';
                row.addEventListener('click', function () {
                    const email = row.querySelector('.demo-email').textContent.trim();
                    document.getElementById('email').value = email;
                    document.getElementById('password').value = 'password';
                    // Flash highlight
                    row.style.background = 'rgba(99,102,241,0.15)';
                    setTimeout(() => row.style.background = '', 600);
                });
            });
        <?php endif; ?>
    </script>
<?php $__env->stopSection(); ?>
<?php echo $__env->make('layouts.auth', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH D:\Hack\F_FleetFlow\resources\views/auth/login.blade.php ENDPATH**/ ?>