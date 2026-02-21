<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $__env->yieldContent('title', 'FleetFlow'); ?> - Fleet Management System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap');

        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }

        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 40%, #312e81 70%, #4c1d95 100%);
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 1.5rem;
            position: relative;
            overflow: hidden;
        }

        /* Animated background orbs */
        body::before {
            content: '';
            position: absolute;
            top: -20%;
            left: -10%;
            width: 600px;
            height: 600px;
            background: radial-gradient(circle, rgba(99, 102, 241, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 8s ease-in-out infinite;
        }

        body::after {
            content: '';
            position: absolute;
            bottom: -20%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: radial-gradient(circle, rgba(139, 92, 246, 0.15) 0%, transparent 70%);
            border-radius: 50%;
            animation: float 10s ease-in-out infinite reverse;
        }

        @keyframes float {

            0%,
            100% {
                transform: translateY(0) scale(1);
            }

            50% {
                transform: translateY(-30px) scale(1.05);
            }
        }

        .auth-container {
            width: 100%;
            max-width: 460px;
            position: relative;
            z-index: 10;
            animation: slideUp 0.6s cubic-bezier(0.16, 1, 0.3, 1) forwards;
        }

        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        .auth-brand {
            text-align: center;
            margin-bottom: 2rem;
        }

        .auth-brand .brand-icon {
            width: 72px;
            height: 72px;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            border-radius: 20px;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 1rem;
            font-size: 2rem;
            color: white;
            box-shadow: 0 8px 32px rgba(99, 102, 241, 0.4);
        }

        .auth-brand h1 {
            font-size: 1.875rem;
            font-weight: 800;
            color: white;
            letter-spacing: -0.025em;
        }

        .auth-brand p {
            color: rgba(255, 255, 255, 0.55);
            font-size: 0.9rem;
            margin-top: 0.25rem;
        }

        .auth-card {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.12);
            border-radius: 24px;
            padding: 2.5rem;
            box-shadow:
                0 25px 50px rgba(0, 0, 0, 0.4),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
        }

        .auth-card .form-label {
            color: rgba(255, 255, 255, 0.75);
            font-size: 0.85rem;
            font-weight: 600;
            margin-bottom: 0.5rem;
        }

        .auth-card .form-control {
            background: rgba(255, 255, 255, 0.07);
            border: 1.5px solid rgba(255, 255, 255, 0.12);
            border-radius: 12px;
            color: white;
            padding: 0.8rem 1rem;
            font-size: 0.95rem;
            transition: all 0.25s ease;
        }

        .auth-card .form-control::placeholder {
            color: rgba(255, 255, 255, 0.3);
        }

        .auth-card .form-control:focus {
            background: rgba(255, 255, 255, 0.1);
            border-color: rgba(99, 102, 241, 0.8);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.2);
            color: white;
            outline: none;
        }

        .auth-card .form-control.is-invalid {
            border-color: rgba(239, 68, 68, 0.8);
            box-shadow: 0 0 0 4px rgba(239, 68, 68, 0.15);
        }

        .invalid-feedback {
            color: #fca5a5;
            font-size: 0.8rem;
        }

        .auth-card .form-check-input {
            background-color: rgba(255, 255, 255, 0.1);
            border-color: rgba(255, 255, 255, 0.25);
        }

        .auth-card .form-check-input:checked {
            background-color: #6366f1;
            border-color: #6366f1;
        }

        .auth-card .form-check-label {
            color: rgba(255, 255, 255, 0.65);
            font-size: 0.875rem;
        }

        .btn-auth {
            width: 100%;
            padding: 0.9rem;
            border-radius: 12px;
            border: none;
            background: linear-gradient(135deg, #6366f1 0%, #8b5cf6 100%);
            color: white;
            font-weight: 700;
            font-size: 1rem;
            letter-spacing: 0.01em;
            transition: all 0.3s ease;
            box-shadow: 0 4px 20px rgba(99, 102, 241, 0.4);
            cursor: pointer;
        }

        .btn-auth:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 28px rgba(99, 102, 241, 0.55);
            color: white;
        }

        .btn-auth:active {
            transform: translateY(0);
        }

        .auth-divider {
            border-color: rgba(255, 255, 255, 0.1);
            margin: 1.5rem 0;
        }

        .demo-accounts {
            background: rgba(255, 255, 255, 0.04);
            border: 1px solid rgba(255, 255, 255, 0.08);
            border-radius: 12px;
            padding: 1rem 1.25rem;
        }

        .demo-accounts .demo-title {
            color: rgba(255, 255, 255, 0.5);
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 0.6rem;
        }

        .demo-accounts .demo-row {
            display: flex;
            align-items: center;
            padding: 0.3rem 0;
            border-bottom: 1px solid rgba(255, 255, 255, 0.05);
        }

        .demo-accounts .demo-row:last-child {
            border-bottom: none;
        }

        .demo-accounts .demo-role {
            font-size: 0.72rem;
            font-weight: 700;
            padding: 0.2rem 0.5rem;
            border-radius: 6px;
            min-width: 80px;
            text-align: center;
            margin-right: 0.75rem;
        }

        .demo-accounts .demo-email {
            color: rgba(255, 255, 255, 0.55);
            font-size: 0.75rem;
            font-family: monospace;
        }

        .role-manager-pill {
            background: rgba(99, 102, 241, 0.2);
            color: #a5b4fc;
        }

        .role-dispatcher-pill {
            background: rgba(14, 165, 233, 0.2);
            color: #7dd3fc;
        }

        .role-safety-pill {
            background: rgba(16, 185, 129, 0.2);
            color: #6ee7b7;
        }

        .role-finance-pill {
            background: rgba(245, 158, 11, 0.2);
            color: #fcd34d;
        }

        .auth-footer {
            text-align: center;
            margin-top: 1.5rem;
            color: rgba(255, 255, 255, 0.3);
            font-size: 0.75rem;
        }
    </style>
</head>

<body>
    <div class="auth-container">
        
        <div class="auth-brand">
            <div class="brand-icon">
                <i class="bi bi-truck-front-fill"></i>
            </div>
            <h1>FleetFlow</h1>
            <p>Fleet & Logistics Management System</p>
        </div>

        
        <div class="auth-card">
            <?php if(session('error')): ?>
                <div class="alert alert-danger d-flex align-items-center mb-3"
                    style="background: rgba(239,68,68,0.15); border: 1px solid rgba(239,68,68,0.3); border-radius: 10px; color: #fca5a5;">
                    <i class="bi bi-exclamation-circle me-2"></i>
                    <?php echo e(session('error')); ?>

                </div>
            <?php endif; ?>

            <?php if(session('success')): ?>
                <div class="alert d-flex align-items-center mb-3"
                    style="background: rgba(16,185,129,0.15); border: 1px solid rgba(16,185,129,0.3); border-radius: 10px; color: #6ee7b7;">
                    <i class="bi bi-check-circle me-2"></i>
                    <?php echo e(session('success')); ?>

                </div>
            <?php endif; ?>

            <?php echo $__env->yieldContent('content'); ?>
        </div>

        
        <div class="auth-footer">
            &copy; <?php echo e(date('Y')); ?> FleetFlow &mdash; Secure Fleet Management
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <?php echo $__env->yieldContent('scripts'); ?>
</body>

</html><?php /**PATH D:\Hack\F_FleetFlow\resources\views/layouts/auth.blade.php ENDPATH**/ ?>