<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Suki Onigiri') 🍙</title>

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        /* ========== DESIGN TOKENS ========== */
        :root {
            /* PALETTE: mudah diubah di sini */
            --brand: #ef4444;                 /* primary red */
            --brand-600: #dc2626;             /* hover / darker */
            --brand-hover: var(--brand-600);
            --brand-light: #fff1f2;           /* light red background */
            --brand-rgb: 239,68,68;           /* used for rgba(...) */
            --brand-50: rgba(var(--brand-rgb), .06);

            --bg: #f3f4f5;
            --surface: #ffffff;
            --surface-secondary: #f7f8f9;
            --text-primary: #212121;
            --text-secondary: #6d7588;
            --text-tertiary: #9fa6b2;
            --border: #e5e7eb;
            --border-light: #f0f0f0;
            /* Danger / emphasis teks gelap untuk kontras */
            --danger: #7f1d1d;
            --warning: #f59e0b;
            --info: #3b82f6;

            --radius-xs: 6px;
            --radius-sm: 8px;
            --radius-md: 12px;
            --radius-lg: 16px;
            --radius-xl: 20px;
            --radius-full: 9999px;

            --shadow-xs: 0 1px 2px rgba(0,0,0,.04);
            --shadow-sm: 0 1px 3px rgba(0,0,0,.06), 0 1px 2px rgba(0,0,0,.04);
            --shadow-md: 0 4px 6px -1px rgba(0,0,0,.07), 0 2px 4px -2px rgba(0,0,0,.05);
            --shadow-lg: 0 10px 15px -3px rgba(0,0,0,.08), 0 4px 6px -4px rgba(0,0,0,.04);
            --shadow-xl: 0 20px 25px -5px rgba(0,0,0,.08), 0 8px 10px -6px rgba(0,0,0,.04);

            --font: 'Inter', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            --transition: 150ms cubic-bezier(.4,0,.2,1);
        }

        /* ========== RESET & BASE ========== */
        *, *::before, *::after { box-sizing: border-box; }

        html { scroll-behavior: smooth; }

        body {
            font-family: var(--font);
            background: var(--bg);
            color: var(--text-primary);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
            line-height: 1.6;
            margin: 0;
        }

        a { color: inherit; text-decoration: none; transition: color var(--transition); }
        a:hover { color: var(--brand); }
        img { max-width: 100%; height: auto; }

        /* ========== LAYOUT ========== */
        .site-root {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .site-content { flex: 1 1 auto; }

        /* ========== TYPOGRAPHY ========== */
        .text-muted { color: var(--text-secondary) !important; }
        .text-brand { color: var(--brand) !important; }
        .fw-800 { font-weight: 800 !important; }
        .section { padding: 24px 0; }
        .section-lg { padding: 40px 0; }

        /* ========== NAVBAR — Tokopedia Style ========== */
        .tp-navbar {
            background: var(--surface);
            border-bottom: 1px solid var(--border);
            position: sticky;
            top: 0;
            z-index: 1030;
            padding: 0;
        }

        /* Top micro bar */
        .tp-topbar {
            background: var(--surface-secondary);
            border-bottom: 1px solid var(--border-light);
            font-size: .75rem;
            color: var(--text-secondary);
            padding: 6px 0;
        }
        .tp-topbar a { font-weight: 500; }
        .tp-topbar a:hover { color: var(--brand); }

        /* Main navbar row */
        .tp-navbar-main {
            padding: 12px 0;
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .tp-brand {
            font-size: 1.25rem;
            font-weight: 800;
            color: var(--text-primary);
            white-space: nowrap;
            flex-shrink: 0;
        }
        .tp-brand span { color: var(--brand); }

        /* Category button */
        .tp-category-btn {
            display: none;
            align-items: center;
            gap: 6px;
            padding: 8px 14px;
            border: 1px solid var(--border);
            border-radius: var(--radius-sm);
            background: var(--surface);
            font-size: .85rem;
            font-weight: 600;
            color: var(--text-primary);
            cursor: pointer;
            transition: all var(--transition);
            white-space: nowrap;
            flex-shrink: 0;
        }
        .tp-category-btn:hover { border-color: var(--brand); color: var(--brand); }
        @media (min-width: 992px) { .tp-category-btn { display: inline-flex; } }

        /* Search */
        .tp-search {
            flex: 1 1 0;
            min-width: 0;
            position: relative;
        }
        .tp-search .form-control {
            border-radius: var(--radius-sm);
            border: 2px solid var(--border);
            padding: 10px 44px 10px 16px;
            font-size: .875rem;
            transition: border-color var(--transition), box-shadow var(--transition);
            background: var(--surface);
        }
        .tp-search .form-control:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(var(--brand-rgb),.1);
        }
        .tp-search .btn-search {
            position: absolute;
            right: 4px;
            top: 50%;
            transform: translateY(-50%);
            background: var(--brand);
            color: #fff;
            border: none;
            border-radius: var(--radius-xs);
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: background var(--transition);
        }
        .tp-search .btn-search:hover { background: var(--brand-hover); }

        /* Navbar icons */
        .tp-nav-icons {
            display: flex;
            align-items: center;
            gap: 4px;
            flex-shrink: 0;
        }
        .tp-icon-btn {
            position: relative;
            width: 40px;
            height: 40px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: var(--radius-sm);
            border: none;
            background: transparent;
            color: var(--text-secondary);
            font-size: 1.2rem;
            cursor: pointer;
            transition: all var(--transition);
        }
        .tp-icon-btn:hover { background: var(--brand-50); color: var(--brand); }
        .tp-icon-btn .tp-badge {
            position: absolute;
            top: 2px; right: 2px;
            background: var(--brand);
            color: #fff;
            font-size: .6rem;
            font-weight: 700;
            padding: 1px 5px;
            border-radius: var(--radius-full);
            line-height: 1.4;
        }

        /* Divider in navbar */
        .tp-nav-divider {
            width: 1px;
            height: 24px;
            background: var(--border);
            margin: 0 8px;
            flex-shrink: 0;
        }

        /* User menu */
        .tp-user-btn {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 4px 8px 4px 4px;
            border-radius: var(--radius-sm);
            cursor: pointer;
            border: none;
            background: transparent;
            transition: background var(--transition);
            flex-shrink: 0;
        }
        .tp-user-btn:hover { background: var(--brand-50); }

        .tp-avatar {
            width: 36px;
            height: 36px;
            border-radius: var(--radius-full);
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            font-size: .85rem;
            color: #fff;
            background: linear-gradient(135deg, var(--brand), var(--brand-600));
            flex-shrink: 0;
        }
        .tp-avatar img { width: 100%; height: 100%; object-fit: cover; }

        .tp-user-info { text-align: left; line-height: 1.2; }
        .tp-user-name { font-size: .8rem; font-weight: 600; color: var(--text-primary); }
        .tp-user-label { font-size: .68rem; color: var(--text-tertiary); }

        /* Dropdown */
        .tp-dropdown {
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            padding: 6px;
            min-width: 200px;
            margin-top: 8px !important;
        }
        .tp-dropdown .dropdown-item {
            border-radius: var(--radius-sm);
            padding: 10px 12px;
            font-size: .85rem;
            font-weight: 500;
            color: var(--text-primary);
            display: flex;
            align-items: center;
            gap: 10px;
            transition: background var(--transition);
        }
        .tp-dropdown .dropdown-item:hover { background: var(--brand-50); color: var(--brand); }
        .tp-dropdown .dropdown-item.text-danger:hover { background: var(--brand-light); color: var(--danger); }
        .tp-dropdown .dropdown-divider { border-color: var(--border-light); margin: 4px 0; }

        /* Auth buttons in navbar */
        .tp-auth-btns { display: flex; gap: 8px; flex-shrink: 0; }
        .tp-btn-login {
            padding: 8px 20px;
            border: 2px solid var(--brand);
            border-radius: var(--radius-sm);
            color: var(--brand);
            font-weight: 700;
            font-size: .85rem;
            background: var(--surface);
            transition: all var(--transition);
        }
        .tp-btn-login:hover { background: var(--brand-50); color: var(--brand); }
        .tp-btn-register {
            padding: 8px 20px;
            border: 2px solid var(--brand);
            border-radius: var(--radius-sm);
            color: #fff;
            font-weight: 700;
            font-size: .85rem;
            background: var(--brand);
            transition: all var(--transition);
        }
        .tp-btn-register:hover { background: var(--brand-hover); border-color: var(--brand-hover); color: #fff; }

        /* ========== BUTTONS ========== */
        .btn {
            border-radius: var(--radius-sm);
            font-weight: 600;
            font-size: .875rem;
            padding: 10px 20px;
            transition: all var(--transition);
        }
        .btn-primary {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }
        .btn-primary:hover, .btn-primary:focus {
            background: var(--brand-hover);
            border-color: var(--brand-hover);
            box-shadow: 0 4px 12px rgba(var(--brand-rgb),.25);
        }
        .btn-outline-primary {
            color: var(--brand);
            border-color: var(--brand);
            background: transparent;
        }
        .btn-outline-primary:hover {
            background: var(--brand);
            border-color: var(--brand);
            color: #fff;
        }
        .btn-outline-secondary {
            color: var(--text-secondary);
            border-color: var(--border);
        }
        .btn-outline-secondary:hover {
            background: var(--surface-secondary);
            border-color: var(--border);
            color: var(--text-primary);
        }
        .btn-ghost {
            background: transparent;
            border: none;
            color: var(--text-secondary);
            padding: 8px 12px;
        }
        .btn-ghost:hover { background: var(--surface-secondary); color: var(--text-primary); }
        .btn-sm { padding: 6px 14px; font-size: .8rem; }
        .btn-lg { padding: 14px 28px; font-size: 1rem; }

        /* ========== FORMS ========== */
        .form-control, .form-select {
            border-radius: var(--radius-sm);
            border: 1.5px solid var(--border);
            padding: 10px 14px;
            font-size: .875rem;
            transition: border-color var(--transition), box-shadow var(--transition);
        }
        .form-control:focus, .form-select:focus {
            border-color: var(--brand);
            box-shadow: 0 0 0 3px rgba(var(--brand-rgb),.1);
        }
        .form-label {
            font-size: .8rem;
            font-weight: 600;
            color: var(--text-secondary);
            margin-bottom: 6px;
        }

        /* ========== CARDS — no hover lift by default ========== */
        .tp-card {
            background: var(--surface);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xs);
            overflow: hidden;
        }
        .tp-card-header {
            padding: 16px 20px;
            border-bottom: 1px solid var(--border-light);
            background: var(--surface);
        }
        .tp-card-body { padding: 20px; }
        .tp-card-footer {
            padding: 16px 20px;
            border-top: 1px solid var(--border-light);
            background: var(--surface-secondary);
        }

        /* Cards that ARE hoverable (product cards, etc.) */
        .tp-card-hover {
            transition: box-shadow var(--transition), transform var(--transition), border-color var(--transition);
        }
        .tp-card-hover:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
            border-color: var(--border);
        }

        /* Legacy .card + .surface — remove the aggressive hover */
        .card, .surface {
            background: var(--surface);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xs);
        }
        /* REMOVE the old blanket hover effect that caused everything to float */
        .card:hover, .surface:hover {
            box-shadow: var(--shadow-xs);
            transform: none;
            border-color: var(--border-light);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid var(--border-light);
            padding: 16px 20px;
        }
        .card-body { padding: 20px; }
        .card-footer {
            padding: 16px 20px;
            background: var(--surface-secondary);
            border-top: 1px solid var(--border-light);
        }

        /* ========== PRODUCT GRID ========== */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(200px, 1fr));
            gap: 16px;
        }
        @media (min-width: 768px) {
            .product-grid { grid-template-columns: repeat(auto-fill, minmax(210px, 1fr)); }
        }
        @media (min-width: 1200px) {
            .product-grid { grid-template-columns: repeat(5, 1fr); gap: 16px; }
        }

        .product-card {
            background: var(--surface);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-md);
            overflow: hidden;
            display: flex;
            flex-direction: column;
            height: 100%;
            transition: box-shadow var(--transition), transform var(--transition), border-color var(--transition);
        }
        .product-card:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-2px);
            border-color: var(--border);
        }
        .product-card .product-img {
            width: 100%;
            aspect-ratio: 1/1;
            object-fit: cover;
            background: var(--surface-secondary);
            display: block;
        }
        .product-card .product-info {
            padding: 12px;
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 6px;
        }
        .product-card .product-title {
            font-size: .85rem;
            font-weight: 500;
            color: var(--text-primary);
            line-height: 1.4;
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .product-card .product-price {
            font-size: 1rem;
            font-weight: 800;
            color: var(--text-primary);
        }
        .product-card .product-meta-row {
            display: flex;
            align-items: center;
            gap: 6px;
            margin-top: auto;
        }
        .product-card .product-badge {
            font-size: .68rem;
            font-weight: 600;
            padding: 2px 8px;
            border-radius: var(--radius-full);
        }
        .product-link { display: block; color: inherit; text-decoration: none; }

        .category-badge {
            background: var(--surface-secondary);
            color: var(--text-secondary);
            border: 1px solid var(--border-light);
            border-radius: var(--radius-full);
            padding: 3px 10px;
            font-size: .72rem;
            font-weight: 600;
        }

        .badge-available { background: var(--brand-light); color: var(--brand-600); }
        /* ubah unavailable / cancelled ke rentang warna brand */
        .badge-unavailable { background: var(--brand-light); color: var(--danger); }

        /* ========== HERO ========== */
        .hero-section {
            background: linear-gradient(135deg, var(--brand-light) 0%, #ffffff 50%, var(--surface-secondary) 100%);
            padding: 48px 0;
            border-bottom: 1px solid var(--border-light);
        }

        /* ========== ALERTS ========== */
        .alert {
            border-radius: var(--radius-sm);
            font-size: .875rem;
            font-weight: 500;
            padding: 12px 16px;
            border: none;
        }
        .alert-success { background: var(--brand-light); color: var(--brand-600); }
        /* alert-danger / negative messages use brand-light + danger text */
        .alert-danger { background: var(--brand-light); color: var(--danger); }
        .alert-warning { background: #fffbeb; color: #92400e; }
        .alert-info { background: #eff6ff; color: #1e40af; }

        /* ========== STATUS BADGES ========== */
        .status-badge {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            padding: 4px 12px;
            border-radius: var(--radius-full);
            font-size: .75rem;
            font-weight: 600;
        }
        .status-waiting { background: #fffbeb; color: #92400e; }
        .status-processing { background: #eff6ff; color: #1e40af; }
        .status-shipping { background: #f0fdf4; color: #166534; }
        .status-completed { background: var(--brand-light); color: var(--brand-600); }
        .status-cancelled { background: var(--brand-light); color: var(--danger); }

        /* ========== FOOTER ========== */
        .tp-footer {
            background: var(--surface);
            border-top: 1px solid var(--border);
            padding: 40px 0 20px;
            color: var(--text-secondary);
            font-size: .85rem;
        }
        .tp-footer h6 {
            font-size: .8rem;
            font-weight: 700;
            color: var(--text-primary);
            text-transform: uppercase;
            letter-spacing: .5px;
            margin-bottom: 16px;
        }
        .tp-footer ul { list-style: none; padding: 0; margin: 0; }
        .tp-footer ul li { margin-bottom: 8px; }
        .tp-footer ul li a { color: var(--text-secondary); font-weight: 400; }
        .tp-footer ul li a:hover { color: var(--brand); }
        .tp-footer-bottom {
            border-top: 1px solid var(--border-light);
            padding-top: 20px;
            margin-top: 32px;
            text-align: center;
            font-size: .78rem;
            color: var(--text-tertiary);
        }

        /* ========== SCROLLBAR ========== */
        ::-webkit-scrollbar { width: 7px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(0,0,0,.12); border-radius: 10px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(0,0,0,.2); }

        /* ========== MOBILE OFFCANVAS ========== */
        .tp-offcanvas .offcanvas-header {
            border-bottom: 1px solid var(--border-light);
            padding: 16px 20px;
        }
        .tp-offcanvas .offcanvas-body { padding: 16px 20px; }
        .tp-offcanvas-link {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 8px;
            border-radius: var(--radius-sm);
            color: var(--text-primary);
            font-weight: 500;
            font-size: .9rem;
            transition: background var(--transition);
        }
        .tp-offcanvas-link:hover { background: var(--brand-50); color: var(--brand); }
        .tp-offcanvas-link i { font-size: 1.15rem; color: var(--text-secondary); width: 22px; text-align: center; }
        .tp-offcanvas-link:hover i { color: var(--brand); }
        .tp-offcanvas-divider { height: 1px; background: var(--border-light); margin: 8px 0; }
        .tp-offcanvas-user {
            display: flex;
            align-items: center;
            gap: 12px;
            padding: 12px 8px;
            margin-bottom: 8px;
        }
        .tp-offcanvas-user .tp-avatar { width: 44px; height: 44px; font-size: 1rem; }

        /* ========== EMPTY STATE ========== */
        .empty-state {
            text-align: center;
            padding: 48px 20px;
            color: var(--text-tertiary);
        }
        .empty-state i { font-size: 3rem; margin-bottom: 16px; display: block; }
        .empty-state h5 { color: var(--text-secondary); font-weight: 600; margin-bottom: 8px; }
        .empty-state p { font-size: .875rem; max-width: 360px; margin: 0 auto 16px; }

        /* ========== RESPONSIVE HELPERS ========== */
        @media (max-width: 991px) {
            .tp-navbar-desktop { display: none !important; }
            .tp-navbar-mobile { display: flex !important; }
            .section { padding: 16px 0; }
            .product-grid { grid-template-columns: repeat(2, 1fr); gap: 10px; }
            .product-card .product-info { padding: 10px; }
            .product-card .product-title { font-size: .8rem; }
            .product-card .product-price { font-size: .9rem; }
        }
        @media (min-width: 992px) {
            .tp-navbar-desktop { display: flex !important; }
            .tp-navbar-mobile { display: none !important; }
        }
        @media (max-width: 575px) {
            .product-grid { grid-template-columns: repeat(2, 1fr); gap: 8px; }
            .product-card .product-img { aspect-ratio: 1/1; }
            .tp-card-body, .card-body { padding: 16px; }
        }

        /* ========== PAGE-SPECIFIC ========== */
        @yield('page-styles')
        /* make bootstrap danger visuals use theme colors */
        .text-danger { color: var(--danger) !important; }
        .bg-danger { background-color: var(--brand) !important; color: #fff !important; }
    </style>

    @yield('styles')
</head>
<body>
    <div class="site-root">
        <!-- ============ NAVBAR ============ -->
        <nav class="tp-navbar">
            {{-- Top micro bar --}}
            <div class="tp-topbar d-none d-lg-block">
                <div class="container d-flex justify-content-between align-items-center">
                    <div class="d-flex gap-3">
                        <a href="{{ route('home') }}">Tentang Kami</a>
                        <a href="{{ route('products') }}">Promo</a>
                    </div>
                    <div class="d-flex gap-3">
                        @auth
                            @if(auth()->user()->isAdmin())
                                <a href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-1"></i>Dashboard Admin</a>
                            @endif
                        @endauth
                        <span><i class="bi bi-headset me-1"></i>Bantuan</span>
                    </div>
                </div>
            </div>

            {{-- Desktop navbar --}}
            <div class="container tp-navbar-main tp-navbar-desktop" style="display:none;">
                <a class="tp-brand" href="{{ route('home') }}">🍙 <span>Suki</span>Onigiri</a>

                <a href="{{ route('products') }}" class="tp-category-btn">
                    <i class="bi bi-grid"></i> Kategori
                </a>

                <div class="tp-search">
                    <form action="{{ route('products') }}" method="GET">
                        <input name="q" type="search" class="form-control" placeholder="Cari menu favorit..." value="{{ request('q') }}">
                        <button class="btn-search" type="submit"><i class="bi bi-search"></i></button>
                    </form>
                </div>

                <div class="tp-nav-icons">
                    @auth
                        <a href="{{ route('notifications') }}" class="tp-icon-btn" title="Notifikasi">
                            <i class="bi bi-bell"></i>
                            @php $orderAlerts = auth()->user()->orders()->whereIn('status',['waiting_payment','waiting_confirmation','processing','shipping'])->count(); @endphp
                            @if($orderAlerts)<span class="tp-badge">{{ $orderAlerts }}</span>@endif
                        </a>
                        @if(!auth()->user()->isAdmin())
                        <a href="{{ route('cart.index') }}" class="tp-icon-btn" title="Keranjang">
                            <i class="bi bi-cart3"></i>
                            @php $cartCount = auth()->user()->cartItems()->count(); @endphp
                            @if($cartCount)<span class="tp-badge">{{ $cartCount }}</span>@endif
                        </a>
                        @endif
                    @endauth
                </div>

                @auth
                    <div class="tp-nav-divider"></div>
                    <div class="dropdown">
                        <button class="tp-user-btn" data-bs-toggle="dropdown" aria-expanded="false">
                            <div class="tp-avatar">
                                @if(auth()->user()->avatar)
                                    <img src="{{ asset('storage/'.auth()->user()->avatar) }}" alt="">
                                @else
                                    {{ strtoupper(substr(auth()->user()->name,0,1)) }}
                                @endif
                            </div>
                            <div class="tp-user-info d-none d-xl-block">
                                <div class="tp-user-name">{{ Str::limit(auth()->user()->name, 16) }}</div>
                                <div class="tp-user-label">{{ auth()->user()->isAdmin() ? 'Admin' : 'Member' }}</div>
                            </div>
                            <i class="bi bi-chevron-down" style="font-size:.7rem;color:var(--text-tertiary);"></i>
                        </button>
                        <ul class="dropdown-menu tp-dropdown dropdown-menu-end">
                            <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="bi bi-person-circle"></i>Profil Saya</a></li>
                            <li><a class="dropdown-item" href="{{ route('orders.index') }}"><i class="bi bi-bag-check"></i>Pesanan</a></li>
                            @if(auth()->user()->isAdmin())
                            <li><a class="dropdown-item" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2"></i>Dashboard</a></li>
                            @endif
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" class="m-0">
                                    @csrf
                                    <button class="dropdown-item text-danger"><i class="bi bi-box-arrow-right"></i>Keluar</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                @else
                    <div class="tp-nav-divider"></div>
                    <div class="tp-auth-btns">
                        <a href="{{ route('login') }}" class="tp-btn-login">Masuk</a>
                        <a href="{{ route('register') }}" class="tp-btn-register">Daftar</a>
                    </div>
                @endauth
            </div>

            {{-- Mobile navbar --}}
            <div class="container tp-navbar-main tp-navbar-mobile" style="display:none;">
                <a class="tp-brand" href="{{ route('home') }}" style="font-size:1.05rem;">🍙 <span>Onigiri</span>Shop</a>
                <div class="tp-search" style="min-width:0;">
                    <form action="{{ route('products') }}" method="GET">
                        <input name="q" type="search" class="form-control form-control-sm" placeholder="Cari..." value="{{ request('q') }}" style="padding:8px 40px 8px 12px; font-size:.8rem;">
                        <button class="btn-search" type="submit" style="width:30px;height:30px;"><i class="bi bi-search" style="font-size:.8rem;"></i></button>
                    </form>
                </div>
                <div class="tp-nav-icons">
                    @auth
                        <a href="{{ route('cart.index') }}" class="tp-icon-btn" style="width:36px;height:36px;font-size:1.1rem;">
                            <i class="bi bi-cart3"></i>
                            @php $cartCount = auth()->user()->cartItems()->count(); @endphp
                            @if($cartCount)<span class="tp-badge" style="font-size:.55rem;">{{ $cartCount }}</span>@endif
                        </a>
                    @endauth
                    <button class="tp-icon-btn" style="width:36px;height:36px;font-size:1.1rem;" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu">
                        <i class="bi bi-list"></i>
                    </button>
                </div>
            </div>
        </nav>

        <!-- ============ MAIN CONTENT ============ -->
        <main class="site-content">
            @if(session('success'))
            <div class="container mt-3">
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>{{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
            @endif
            @if(session('error'))
            <div class="container mt-3">
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>{{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            </div>
            @endif

            @yield('content')
        </main>

        <!-- ============ FOOTER ============ -->
        <footer class="tp-footer">
            <div class="container">
                <div class="row g-4">
                    <div class="col-6 col-md-3">
                        <h6>SukiOnigiri</h6>
                        <ul>
                            <li><a href="{{ route('home') }}">Tentang Kami</a></li>
                            <li><a href="{{ route('products') }}">Semua Menu</a></li>
                            <li><a href="#">Blog</a></li>
                        </ul>
                    </div>
                    <div class="col-6 col-md-3">
                        <h6>Bantuan</h6>
                        <ul>
                            <li><a href="#">Pusat Bantuan</a></li>
                            <li><a href="#">Cara Belanja</a></li>
                            <li><a href="#">Pengembalian</a></li>
                        </ul>
                    </div>
                    <div class="col-6 col-md-3">
                        <h6>Kebijakan</h6>
                        <ul>
                            <li><a href="#">Syarat & Ketentuan</a></li>
                            <li><a href="#">Kebijakan Privasi</a></li>
                        </ul>
                    </div>
                    <div class="col-6 col-md-3">
                        <h6>Hubungi Kami</h6>
                        <ul>
                            <li><i class="bi bi-whatsapp me-2"></i>+62 812-3456-7890</li>
                            <li><i class="bi bi-envelope me-2"></i>hello@onigiri.shop</li>
                            <li><i class="bi bi-geo-alt me-2"></i>Jakarta, Indonesia</li>
                        </ul>
                    </div>
                </div>
                <div class="tp-footer-bottom">
                    © {{ date('Y') }} SukiOnigiri. All rights reserved.
                </div>
            </div>
        </footer>
    </div>

    <!-- ============ MOBILE OFFCANVAS ============ -->
    <div class="offcanvas offcanvas-end tp-offcanvas" tabindex="-1" id="mobileMenu">
        <div class="offcanvas-header">
            <h6 class="offcanvas-title mb-0">🍙 SukiOnigiri</h6>
            <button type="button" class="btn-close" data-bs-dismiss="offcanvas"></button>
        </div>
        <div class="offcanvas-body p-0" style="padding:16px 20px !important;">
            @auth
            <div class="tp-offcanvas-user">
                <div class="tp-avatar">
                    @if(auth()->user()->avatar)
                        <img src="{{ asset('storage/'.auth()->user()->avatar) }}" alt="">
                    @else
                        {{ strtoupper(substr(auth()->user()->name,0,1)) }}
                    @endif
                </div>
                <div>
                    <div style="font-weight:600;font-size:.9rem;">{{ auth()->user()->name }}</div>
                    <div style="font-size:.75rem;color:var(--text-tertiary);">{{ auth()->user()->email }}</div>
                </div>
            </div>
            <div class="tp-offcanvas-divider"></div>
            @endauth

            <a href="{{ route('home') }}" class="tp-offcanvas-link"><i class="bi bi-house"></i> Beranda</a>
            <a href="{{ route('products') }}" class="tp-offcanvas-link"><i class="bi bi-grid-3x3-gap"></i> Menu</a>

            @auth
                <div class="tp-offcanvas-divider"></div>
                <a href="{{ route('orders.index') }}" class="tp-offcanvas-link"><i class="bi bi-bag-check"></i> Pesanan Saya</a>
                <a href="{{ route('cart.index') }}" class="tp-offcanvas-link">
                    <i class="bi bi-cart3"></i> Keranjang
                    @php $cartCount = auth()->user()->cartItems()->count(); @endphp
                    @if($cartCount)<span class="badge bg-danger rounded-pill ms-auto">{{ $cartCount }}</span>@endif
                </a>
                <a href="{{ route('notifications') }}" class="tp-offcanvas-link">
                    <i class="bi bi-bell"></i> Notifikasi
                    @php $orderAlerts = auth()->user()->orders()->whereIn('status',['waiting_payment','waiting_confirmation','processing','shipping'])->count(); @endphp
                    @if($orderAlerts)<span class="badge bg-danger rounded-pill ms-auto">{{ $orderAlerts }}</span>@endif
                </a>
                <a href="{{ route('profile.show') }}" class="tp-offcanvas-link"><i class="bi bi-person-circle"></i> Profil</a>

                @if(auth()->user()->isAdmin())
                <div class="tp-offcanvas-divider"></div>
                <a href="{{ route('admin.dashboard') }}" class="tp-offcanvas-link"><i class="bi bi-speedometer2"></i> Dashboard Admin</a>
                @endif

                <div class="tp-offcanvas-divider"></div>
                <form action="{{ route('logout') }}" method="POST">
                    @csrf
                    <button type="submit" class="tp-offcanvas-link w-100 text-start border-0 bg-transparent" style="color:var(--danger);">
                        <i class="bi bi-box-arrow-right" style="color:var(--danger);"></i> Keluar
                    </button>
                </form>
            @else
                <div class="tp-offcanvas-divider"></div>
                <div class="d-grid gap-2 mt-2 px-2">
                    <a href="{{ route('login') }}" class="btn btn-outline-primary">Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary">Daftar</a>
                </div>
            @endauth
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-dismiss alerts after 5s
        document.querySelectorAll('.alert-dismissible').forEach(el => {
            setTimeout(() => {
                const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
                bsAlert.close();
            }, 5000);
        });
    </script>
    @yield('scripts')
</body>
</html>
