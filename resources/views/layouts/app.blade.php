<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'FleetFlow') - Fleet Management System</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        /* ── Design tokens ─────────────────────────────────────── */
        :root {
            --sidebar-width: 235px;
            --sidebar-bg-from: #16123a;
            --sidebar-bg-to:   #1e1548;
            --sidebar-accent:  #7c6fff;
            --sidebar-text:    rgba(255,255,255,0.58);
            --sidebar-text-hover: #fff;
        }

        /* ── Base ──────────────────────────────────────────────── */
        body {
            font-family: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f1f5f9;
        }

        /* ── Sidebar shell ─────────────────────────────────────── */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, var(--sidebar-bg-from) 0%, var(--sidebar-bg-to) 100%);
            border-right: 1px solid rgba(255,255,255,0.06);
            z-index: 1000;
            transition: transform 0.3s cubic-bezier(.4,0,.2,1);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* subtle top glow strip that echoes the dashboard gradient */
        .sidebar::before {
            content: '';
            position: absolute;
            top: 0; left: 0; right: 0;
            height: 3px;
            background: linear-gradient(90deg, #667eea, #764ba2);
        }

        .sidebar.collapsed { transform: translateX(-100%); }

        /* ── Logo area ─────────────────────────────────────────── */
        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 0.6rem;
            padding: 1.25rem 1.25rem 1rem;
            border-bottom: 1px solid rgba(255,255,255,0.07);
            flex-shrink: 0;
        }

        .sidebar-brand .brand-icon {
            width: 32px;
            height: 32px;
            border-radius: 9px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1rem;
            color: #fff;
            flex-shrink: 0;
        }

        .sidebar-brand span {
            font-size: 1.05rem;
            font-weight: 800;
            color: #fff;
            letter-spacing: -0.02em;
        }

        /* ── Nav section label ─────────────────────────────────── */
        .sidebar-section {
            padding: 1rem 1.25rem 0.25rem;
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 0.1em;
            text-transform: uppercase;
            color: rgba(255,255,255,0.25);
        }

        /* ── Nav links ─────────────────────────────────────────── */
        nav.sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 0.5rem 0.75rem;
            scrollbar-width: none;
        }

        nav.sidebar-nav::-webkit-scrollbar { display: none; }

        .sidebar-link {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            padding: 0.6rem 0.85rem;
            margin-bottom: 2px;
            color: var(--sidebar-text);
            text-decoration: none;
            font-size: 0.875rem;
            font-weight: 500;
            border-radius: 10px;
            transition: background 0.18s ease, color 0.18s ease, box-shadow 0.18s ease;
            white-space: nowrap;
        }

        .sidebar-link i {
            font-size: 1rem;
            width: 20px;
            text-align: center;
            flex-shrink: 0;
        }

        .sidebar-link:hover {
            background: rgba(255,255,255,0.08);
            color: var(--sidebar-text-hover);
        }

        .sidebar-link.active {
            background: linear-gradient(135deg, rgba(99,102,241,0.35), rgba(118,75,162,0.35));
            color: #fff;
            box-shadow: 0 0 0 1px rgba(124,111,255,0.25),
                        0 4px 12px rgba(99,102,241,0.2);
        }

        .sidebar-link.active i {
            color: #a5b4fc;
        }

        /* ── Main content offset ───────────────────────────────── */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
            transition: margin-left 0.3s cubic-bezier(.4,0,.2,1);
        }

        .main-content.expanded { margin-left: 0; }

        /* ── Cards ─────────────────────────────────────────────── */
        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.08);
            transition: box-shadow 0.2s ease;
        }

        .card:hover { box-shadow: 0 4px 12px rgba(0,0,0,0.1); }

        .kpi-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 16px;
            padding: 1.5rem;
        }

        .kpi-card .kpi-number {
            font-size: 2.5rem;
            font-weight: 700;
            line-height: 1;
        }

        .kpi-card .kpi-label {
            font-size: 0.875rem;
            opacity: 0.9;
            margin-top: 0.5rem;
        }

        /* ── Status badges ─────────────────────────────────────── */
        .status-badge {
            padding: 0.375rem 0.75rem;
            border-radius: 9999px;
            font-size: 0.75rem;
            font-weight: 600;
            text-transform: uppercase;
        }

        .status-available    { background: #10b981; color: white; }
        .status-on_trip      { background: #3b82f6; color: white; }
        .status-in_shop      { background: #ef4444; color: white; }
        .status-out_of_service { background: #6b7280; color: white; }

        /* ── Top navbar ────────────────────────────────────────── */
        .navbar-brand {
            font-weight: 700;
            font-size: 1.25rem;
            color: #1e293b;
        }

        /* ── Table ─────────────────────────────────────────────── */
        .table { background: white; border-radius: 8px; overflow: hidden; }

        .table thead th {
            background: #f8fafc;
            border-bottom: 2px solid #e2e8f0;
            font-weight: 600;
            color: #475569;
            padding: 1rem;
        }

        .table tbody td {
            padding: 1rem;
            vertical-align: middle;
            border-bottom: 1px solid #f1f5f9;
        }

        /* ── Activity feed ─────────────────────────────────────── */
        .activity-item {
            padding: 1rem;
            border-left: 3px solid #e2e8f0;
            margin-bottom: 0.5rem;
            background: white;
            border-radius: 0 8px 8px 0;
        }

        .activity-item.success { border-left-color: #10b981; }
        .activity-item.warning { border-left-color: #f59e0b; }
        .activity-item.primary { border-left-color: #3b82f6; }

        /* ── Search box ────────────────────────────────────────── */
        .search-box { position: relative; }

        .search-box input {
            padding-left: 2.5rem;
            border-radius: 8px;
            border: 1px solid #e2e8f0;
        }

        .search-box i {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: #94a3b8;
        }

        /* ── Responsive ────────────────────────────────────────── */
        @media (max-width: 768px) {
            .sidebar { transform: translateX(-100%); }
            .sidebar.show { transform: translateX(0); }
            .main-content { margin-left: 0; }
        }
    </style>
</head>

<body>
    @auth
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <div class="sidebar-brand">
                <div class="brand-icon"><i class="bi bi-truck-front-fill"></i></div>
                <span>FleetFlow</span>
            </div>

            <nav class="sidebar-nav">
                <a href="{{ route('dashboard') }}"
                    class="sidebar-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    Dashboard
                </a>

                @if(auth()->user()->role === 'manager' || auth()->user()->role === 'dispatcher')
                    <a href="{{ route('vehicles.index') }}"
                        class="sidebar-link {{ request()->routeIs('vehicles.*') ? 'active' : '' }}">
                        <i class="bi bi-truck"></i>
                        Vehicles
                    </a>
                @endif

                @if(auth()->user()->role === 'manager' || auth()->user()->role === 'safety')
                    <a href="{{ route('drivers.index') }}"
                        class="sidebar-link {{ request()->routeIs('drivers.*') ? 'active' : '' }}">
                        <i class="bi bi-person-badge"></i>
                        Drivers
                    </a>
                @endif

                @if(auth()->user()->role === 'manager' || auth()->user()->role === 'dispatcher')
                    <a href="{{ route('trips.index') }}"
                        class="sidebar-link {{ request()->routeIs('trips.*') ? 'active' : '' }}">
                        <i class="bi bi-route"></i>
                        Trips
                    </a>
                @endif

                @if(auth()->user()->role === 'manager')
                    <a href="{{ route('maintenance.index') }}"
                        class="sidebar-link {{ request()->routeIs('maintenance.*') ? 'active' : '' }}">
                        <i class="bi bi-tools"></i>
                        Maintenance
                    </a>

                    <a href="{{ route('fuel.index') }}" class="sidebar-link {{ request()->routeIs('fuel.*') ? 'active' : '' }}">
                        <i class="bi bi-fuel-pump"></i>
                        Fuel
                    </a>
                @endif

                @if(auth()->user()->role === 'manager' || auth()->user()->role === 'finance')
                    <a href="{{ route('analytics') }}"
                        class="sidebar-link {{ request()->routeIs('analytics') ? 'active' : '' }}">
                        <i class="bi bi-graph-up"></i>
                        Analytics
                    </a>
                @endif
            </nav>
        </div>
    @endauth

    <!-- Main Content -->
    <div class="main-content" id="mainContent">
        <!-- Top Navbar -->
        <nav class="navbar navbar-expand-lg navbar-light bg-white border-bottom">
            <div class="container-fluid">
                <button class="btn btn-link d-md-none" id="sidebarToggle">
                    <i class="bi bi-list fs-4"></i>
                </button>

                <span class="navbar-brand">
                    @yield('title', 'Dashboard')
                </span>

                <div class="ms-auto d-flex align-items-center">
                    <!-- Search Box -->
                    <div class="search-box me-3">
                        <i class="bi bi-search"></i>
                        <input type="text" class="form-control" placeholder="Search..." style="width: 250px;">
                    </div>

                    <!-- Notifications -->
                    <div class="dropdown me-3">
                        <button class="btn btn-link position-relative" type="button" data-bs-toggle="dropdown">
                            <i class="bi bi-bell fs-5"></i>
                            <span
                                class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger">
                                3
                            </span>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#">Vehicle maintenance due</a></li>
                            <li><a class="dropdown-item" href="#">Driver license expiring</a></li>
                            <li><a class="dropdown-item" href="#">Trip completed</a></li>
                        </ul>
                    </div>

                    <!-- Profile Dropdown -->
                    @auth
                        <div class="dropdown">
                            <button class="btn btn-link d-flex align-items-center" type="button" data-bs-toggle="dropdown">
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
                                    style="width: 32px; height: 32px;">
                                    {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                                </div>
                                <span class="ms-2">{{ auth()->user()->name }}</span>
                                <i class="bi bi-chevron-down ms-2"></i>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="{{ route('profile') }}"><i
                                            class="bi bi-person me-2"></i>Profile</a></li>
                                <li><a class="dropdown-item" href="#"><i class="bi bi-gear me-2"></i>Settings</a></li>
                                <li>
                                    <hr class="dropdown-divider">
                                </li>
                                <li>
                                    <form method="POST" action="{{ route('logout') }}" style="display:inline;">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i>Logout
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </div>
                    @endauth
                </div>
            </div>
        </nav>

        <!-- Page Content -->
        <div class="container-fluid p-4">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            @if($errors->any())
                <div class="alert alert-danger">
                    <ul class="mb-0">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script>
        // Mobile sidebar toggle
        document.getElementById('sidebarToggle')?.addEventListener('click', function () {
            document.getElementById('sidebar').classList.toggle('show');
        });

        // Close sidebar when clicking outside on mobile
        document.addEventListener('click', function (event) {
            const sidebar = document.getElementById('sidebar');
            const toggle = document.getElementById('sidebarToggle');

            if (window.innerWidth < 768 &&
                !sidebar.contains(event.target) &&
                !toggle.contains(event.target) &&
                sidebar.classList.contains('show')) {
                sidebar.classList.remove('show');
            }
        });
    </script>

    @yield('scripts')
</body>

</html>