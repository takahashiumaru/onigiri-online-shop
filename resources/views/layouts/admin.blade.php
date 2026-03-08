<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Admin') - OnigiriShop Admin</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --sidebar-width: 260px;
            --primary: #E63946;
            --sidebar-bg: #1a1a2e;
            --sidebar-active: #E63946;
        }
        body { font-family: 'Poppins', sans-serif; background: #f0f2f5; }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: var(--sidebar-bg);
            overflow-y: auto;
            z-index: 1000;
            transition: 0.3s;
        }
        .sidebar-brand {
            padding: 20px 24px;
            border-bottom: 1px solid rgba(255,255,255,0.1);
        }
        .sidebar-brand h5 { color: white; font-weight: 700; margin: 0; }
        .sidebar-brand small { color: rgba(255,255,255,0.5); font-size: 0.7rem; }

        .sidebar-nav { padding: 16px 0; }
        .nav-label {
            color: rgba(255,255,255,0.3);
            font-size: 0.65rem;
            font-weight: 600;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 16px 24px 8px;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 10px 24px;
            color: rgba(255,255,255,0.7);
            text-decoration: none;
            transition: all 0.2s;
            border-left: 3px solid transparent;
            gap: 12px;
        }
        .sidebar-link:hover, .sidebar-link.active {
            background: rgba(255,255,255,0.05);
            color: white;
            border-left-color: var(--primary);
        }
        .sidebar-link.active { background: rgba(230,57,70,0.15); color: white; }
        .sidebar-link i { font-size: 1.1rem; width: 20px; }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        /* Top Bar */
        .topbar {
            background: white;
            padding: 14px 24px;
            box-shadow: 0 1px 10px rgba(0,0,0,0.08);
            display: flex;
            justify-content: space-between;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        /* Content Area */
        .content-area { padding: 24px; }

        /* Cards */
        .stat-card {
            border: none;
            border-radius: 16px;
            padding: 1.5rem;
            background: white;
            box-shadow: 0 2px 15px rgba(0,0,0,0.06);
        }
        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 14px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
        }

        .card { border: none; border-radius: 16px; box-shadow: 0 2px 15px rgba(0,0,0,0.06); }
        .card-header { background: white; border-bottom: 1px solid #f0f0f0; border-radius: 16px 16px 0 0 !important; padding: 1rem 1.25rem; }

        .badge { border-radius: 8px; font-weight: 500; }

        .btn-primary { background: var(--primary); border-color: var(--primary); border-radius: 8px; font-weight: 600; }
        .btn-primary:hover { background: #c1121f; border-color: #c1121f; }

        table th { font-weight: 600; font-size: 0.82rem; text-transform: uppercase; letter-spacing: 0.5px; color: #666; }

        .alert { border: none; border-radius: 12px; }
    </style>
    @yield('styles')
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="sidebar-brand">
            <h5>🍙 OnigiriShop</h5>
            <small>Admin Panel</small>
        </div>
        <nav class="sidebar-nav">
            <div class="nav-label">Utama</div>
            <a href="{{ route('admin.dashboard') }}" class="sidebar-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <div class="nav-label">Kelola</div>
            <a href="{{ route('admin.products.index') }}" class="sidebar-link {{ request()->routeIs('admin.products*') ? 'active' : '' }}">
                <i class="bi bi-box-seam"></i> Produk
            </a>
            <a href="{{ route('admin.orders.index') }}" class="sidebar-link {{ request()->routeIs('admin.orders*') ? 'active' : '' }}">
                <i class="bi bi-receipt"></i> Pesanan
            </a>

            <div class="nav-label">Akun</div>
            <a href="{{ route('home') }}" class="sidebar-link" target="_blank">
                <i class="bi bi-shop"></i> Lihat Toko
            </a>
            <form action="{{ route('logout') }}" method="POST">
                @csrf
                <button type="submit" class="sidebar-link w-100 border-0 bg-transparent text-start">
                    <i class="bi bi-box-arrow-right"></i> Keluar
                </button>
            </form>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div class="topbar">
            <h6 class="mb-0 fw-semibold">@yield('title', 'Dashboard')</h6>
            <div class="d-flex align-items-center gap-3">
                @if(session('success'))
                <span class="badge bg-success"><i class="bi bi-check me-1"></i>{{ session('success') }}</span>
                @endif
                @if(session('error'))
                <span class="badge bg-danger"><i class="bi bi-x me-1"></i>{{ session('error') }}</span>
                @endif
                <span class="text-muted small"><i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}</span>
            </div>
        </div>

        <div class="content-area">
            @if($errors->any())
            <div class="alert alert-danger mb-3">
                <ul class="mb-0 ps-3">
                    @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            @yield('content')
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
