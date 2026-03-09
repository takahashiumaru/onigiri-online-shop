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

            --brand: #03AC0E;
            --brand-600: #02970c;

            --text: #111827;
            --muted: #6b7280;
            --bg: #f5f6f7;
            --surface: #ffffff;
            --border: rgba(17, 24, 39, 0.08);

            --sidebar-bg: #0b1220;         /* lebih “premium” */
            --sidebar-bg-2: #0f172a;
            --sidebar-active: rgba(3,172,14,.18);

            --radius-md: 14px;
            --radius-lg: 18px;

            --shadow-sm: 0 4px 12px rgba(16,24,40,0.04);
            --shadow-md: 0 10px 30px rgba(16,24,40,0.08);

            --ring: 0 0 0 .25rem rgba(3,172,14,.12);
        }

        body { font-family: 'Poppins', sans-serif; background: var(--bg); color: var(--text); }

        /* Sidebar */
        .sidebar {
            width: var(--sidebar-width);
            height: 100vh;
            position: fixed;
            top: 0;
            left: 0;
            background: linear-gradient(180deg, var(--sidebar-bg) 0%, var(--sidebar-bg-2) 100%);
            overflow-y: auto;
            z-index: 1000;
            transition: 0.3s;
            border-right: 1px solid rgba(255,255,255,0.06);
        }
        .sidebar-brand {
            padding: 18px 22px;
            border-bottom: 1px solid rgba(255,255,255,0.08);
        }
        .sidebar-brand h5 { color: white; font-weight: 800; margin: 0; letter-spacing: .2px; }
        .sidebar-brand small { color: rgba(255,255,255,0.55); font-size: 0.72rem; }

        .sidebar-nav { padding: 14px 0; }
        .nav-label {
            color: rgba(255,255,255,0.35);
            font-size: 0.65rem;
            font-weight: 700;
            letter-spacing: 1.4px;
            text-transform: uppercase;
            padding: 16px 22px 8px;
        }
        .sidebar-link {
            display: flex;
            align-items: center;
            padding: 10px 14px;
            margin: 2px 12px;
            color: rgba(255,255,255,0.80);
            text-decoration: none;
            transition: all 0.15s ease;
            border-radius: 12px;
            gap: 12px;
            border: 1px solid transparent;
        }
        .sidebar-link:hover {
            background: rgba(255,255,255,0.06);
            color: #fff;
        }
        .sidebar-link.active {
            background: var(--sidebar-active);
            border-color: rgba(3,172,14,.25);
            color: #fff;
        }
        .sidebar-link i { font-size: 1.1rem; width: 20px; opacity: .95; }

        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            min-height: 100vh;
        }

        /* Top Bar */
        .topbar {
            background: var(--surface);
            padding: 12px 22px;
            box-shadow: 0 6px 20px rgba(16,24,40,0.04);
            border-bottom: 1px solid var(--border);
            display: flex;
            gap: 16px;
            align-items: center;
            position: sticky;
            top: 0;
            z-index: 100;
        }

        .topbar-left {
            display:flex;
            flex-direction:column;
            gap:4px;
            min-width: 0;
        }
        .page-breadcrumb {
            font-size: .82rem;
            color: var(--muted);
        }
        .page-title {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--text);
        }

        .topbar-actions {
            margin-left: auto;
            display:flex;
            gap:.6rem;
            align-items:center;
        }
        .topbar-search { width: 320px; max-width: 40vw; }
        .topbar-search .input-group { border-radius: 10px; overflow:hidden; border:1px solid var(--border); }
        .topbar-search .form-control { border:0; padding:.5rem .75rem; }

        .top-action-btn { padding:.55rem .9rem; border-radius:10px; font-weight:700; }
        .top-icon {
            width:44px; height:44px; display:inline-flex; align-items:center; justify-content:center; border-radius:10px; transition:transform .08s; border:1px solid transparent;
        }
        .top-icon:hover{ transform:translateY(-2px); background:rgba(16,24,40,.03); border-color:rgba(16,24,40,.04); }
        .notif-badge { position:absolute; top:8px; right:8px; font-size:.65rem; padding:3px 6px; border-radius:999px; background:#ff5252; color:#fff; }

        .avatar-circle{ width:40px; height:40px; border-radius:999px; display:inline-flex; align-items:center; justify-content:center; background:linear-gradient(135deg,var(--brand),var(--brand-600)); color:#fff; font-weight:800; box-shadow:0 6px 14px rgba(3,172,14,.08); }
        @media (max-width: 991px) {
            .topbar-search { display:none; }
            .page-title{ font-size:.98rem; }
        }

        /* Content Area */
        .content-area { padding: 22px; }

        /* Cards */
        .stat-card {
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            padding: 1.25rem;
            background: var(--surface);
            box-shadow: var(--shadow-sm);
            transition: box-shadow .18s ease, transform .18s ease, border-color .18s ease;
        }
        .stat-card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: rgba(3,172,14,.10);
        }
        .stat-icon {
            width: 52px;
            height: 52px;
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.4rem;
            background: rgba(3,172,14,.08);
            color: var(--brand-600);
        }

        .card {
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            transition: box-shadow .18s ease, transform .18s ease, border-color .18s ease;
        }
        .card:hover {
            transform: translateY(-4px);
            box-shadow: var(--shadow-md);
            border-color: rgba(3,172,14,.10);
        }
        .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(17,24,39,0.04);
            border-radius: var(--radius-lg) var(--radius-lg) 0 0 !important;
            padding: 1rem 1.25rem;
        }
        .card-body {
            padding: 1rem 1.25rem;
        }
        .card-footer {
            padding: .85rem 1.25rem;
            border-top: 1px solid rgba(17,24,39,0.03);
            background: transparent;
        }

        .badge { border-radius: 999px; font-weight: 700; }

        .btn { border-radius: 12px; font-weight: 800; }
        .btn-primary { background: var(--brand); border-color: var(--brand); box-shadow: 0 8px 20px rgba(3,172,14,.12); }
        .btn-primary:hover { background: var(--brand-600); border-color: var(--brand-600); }

        .form-control, .form-select { border-radius: 12px; border-color: var(--border); padding: .65rem .85rem; }
        .form-control:focus, .form-select:focus { border-color: rgba(3,172,14,.45); box-shadow: var(--ring); }

        table th {
            font-weight: 800;
            font-size: 0.78rem;
            text-transform: uppercase;
            letter-spacing: 0.6px;
            color: #64748b;
        }
        .table > :not(caption) > * > * { border-color: rgba(17, 24, 39, 0.08); }

        .alert { border: 1px solid var(--border); border-radius: var(--radius-md); box-shadow: var(--shadow-sm); }
        .alert-danger { background: #FEF2F2; border-color: rgba(239,68,68,.20); color: #7f1d1d; }

        /* layout fix for admin */
        html, body { height: 100%; }
        .site-root { min-height: 100vh; display: flex; flex-direction: row; }
        .main-content { flex: 1 1 auto; display: flex; flex-direction: column; min-height: 100vh; }
        .content-area { flex: 1 1 auto; } /* ensures content-area expands before footer (if any) */
    </style>
    @yield('styles')
</head>
<body>
    <div class="site-root">
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
                <div class="topbar-left">
                    <div class="page-breadcrumb">
                        @yield('breadcrumb', 'Admin') / <span class="text-muted">@yield('subbreadcrumb','Overview')</span>
                    </div>
                    <div class="page-title">@yield('title', 'Dashboard')</div>
                </div>

                <div class="topbar-actions">
                    <div class="topbar-search d-none d-md-block">
                        <form action="{{ route('admin.products.index') }}" method="GET">
                            <div class="input-group">
                                <input type="search" name="q" class="form-control" placeholder="Cari produk, pesanan, atau pelanggan..." aria-label="Cari">
                                <button class="btn btn-light" type="submit"><i class="bi bi-search"></i></button>
                            </div>
                        </form>
                    </div>

                    <a href="{{ route('admin.products.create') }}" class="btn btn-primary top-action-btn d-none d-md-inline-flex">
                        <i class="bi bi-plus-lg me-2"></i>Tambah Produk
                    </a>

                    <div class="position-relative">
                        <a href="{{ route('admin.notifications') }}" class="top-icon" title="Notifikasi">
                            <i class="bi bi-bell fs-5"></i>
                            @php
                                // untuk admin: hitung pesanan baru / butuh konfirmasi (sesuaikan status)
                                $adminAlerts = \App\Models\Order::whereIn('status', ['waiting_payment', 'waiting_confirmation', 'processing'])->count();
                            @endphp
                            @if($adminAlerts > 0)
                                <span class="notif-badge">{{ $adminAlerts }}</span>
                            @endif
                        </a>
                    </div>

                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center gap-2" data-bs-toggle="dropdown">
                            <div class="avatar-circle">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end">
                            <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profil</a></li>
                            <li><a class="dropdown-item" href="{{ route('home') }}"><i class="bi bi-shop me-2"></i>Lihat toko</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                                </form>
                            </li>
                        </ul>
                    </div>
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

            <!-- optional footer for admin can be placed here and will stick to bottom -->
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
