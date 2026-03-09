<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Onigiri Shop') 🍙</title>

    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Noto+Sans+JP:wght@400;500;700&family=Playfair+Display:wght@700&family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

    <style>
        :root {
            --brand: #03AC0E;
            --brand-600: #02970c;
            --bg: #f5f6f7;
            --surface: #ffffff;
            --text: #212121;
            --muted: #6b7280;
            --border: rgba(17,24,39,0.08);

            --radius-sm: 10px;
            --radius-md: 14px;
            --radius-lg: 18px;

            --shadow-sm: 0 4px 12px rgba(16,24,40,0.04);
            --shadow-md: 0 10px 30px rgba(16,24,40,0.08);
            --ring: 0 0 0 .25rem rgba(3,172,14,.12);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--bg);
            color: var(--text);
            -webkit-font-smoothing: antialiased;
            -moz-osx-font-smoothing: grayscale;
        }

        a { color: inherit; }
        .text-muted { color: var(--muted) !important; }

        /* Layout spacing helpers */
        .page-wrap { min-height: calc(100vh - 64px); }
        .section { padding: 18px 0; }

        /* Navbar (white, marketplace style) */
        .navbar {
            background: var(--surface) !important;
            box-shadow: none;
            border-bottom: 1px solid var(--border);
            padding: 10px 0;
        }
        .navbar-brand {
            font-family: 'Poppins', sans-serif;
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--text) !important;
            letter-spacing: 0;
        }
        .navbar-brand span { color: var(--brand); }

        .nav-link {
            color: var(--text) !important;
            font-weight: 600;
            transition: opacity .15s ease, color .15s ease;
            opacity: .82;
        }
        .nav-link:hover { opacity: 1; color: var(--brand) !important; }

        .navbar .dropdown-menu {
            border: 1px solid var(--border);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-md);
            padding: 8px;
        }
        .navbar .dropdown-item {
            border-radius: 10px;
            padding: 10px 10px;
            font-weight: 600;
        }
        .navbar .dropdown-item:active { background: var(--brand-50); color: var(--text); }

        /* Badge cart */
        .cart-badge {
            background: var(--brand);
            color: #fff;
            border-radius: 999px;
            font-size: .65rem;
            padding: 2px 7px;
            position: absolute;
            top: -6px;
            right: -10px;
            box-shadow: var(--shadow-sm);
        }

        /* Buttons */
        .btn { border-radius: 12px; font-weight: 700; }
        .btn-primary {
            background: var(--brand);
            border-color: var(--brand);
            box-shadow: 0 8px 20px rgba(3,172,14,.12);
        }
        .btn-primary:hover {
            background: var(--brand-600);
            border-color: var(--brand-600);
            transform: translateY(-1px);
        }
        .btn-outline-primary {
            color: var(--brand);
            border-color: rgba(3,172,14,.35);
            background: #fff;
        }
        .btn-outline-primary:hover { background: var(--brand-50); color: var(--brand-600); border-color: rgba(3,172,14,.45); }

        /* Forms */
        .form-control, .form-select {
            border-radius: 12px;
            border-color: var(--border);
            padding: .65rem .85rem;
        }
        .form-control:focus, .form-select:focus {
            border-color: rgba(3,172,14,.55);
            box-shadow: var(--ring);
        }

        /* Surfaces / Cards */
        .surface, .card {
            background: var(--surface);
            border: 1px solid var(--border);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-sm);
            transition: box-shadow .18s ease, transform .18s ease, border-color .18s ease;
        }

        .card:hover, .surface:hover {
            box-shadow: var(--shadow-md);
            transform: translateY(-4px);
            border-color: rgba(3,172,14,.12);
        }

        .card-header {
            background: transparent;
            border-bottom: 1px solid rgba(17,24,39,0.04);
            padding: 1rem 1.25rem;
            border-radius: inherit;
        }
        .card-body { padding: 1rem 1.25rem; }
        .card-footer { padding: .85rem 1.25rem; background: transparent; border-top: 1px solid rgba(17,24,39,0.03); }

        .product-card {
            border-radius: var(--radius-lg);
            overflow: hidden;
        }
        .product-card img {
            height: 220px;
            object-fit: cover;
            width: 100%;
            background: #f3f4f6;
        }
        .product-card .card-body { padding: 1rem 1.1rem; }

        .product-price {
            font-size: 1.05rem;
            font-weight: 800;
            color: var(--brand);
        }

        .category-badge {
            background: #fbfdfb;
            color: #2d3748;
            border: 1px solid rgba(17,24,39,0.04);
            border-radius: 999px;
            padding: 4px 10px;
            font-size: .72rem;
            font-weight: 700;
        }

        /* Hero (lebih kalem) */
        .hero-section {
            background: radial-gradient(1200px 400px at 15% 0%, rgba(3,172,14,.18) 0%, rgba(3,172,14,0) 60%),
                        linear-gradient(180deg, #ffffff 0%, #f7f8fa 100%);
            color: var(--text);
            padding: 56px 0;
            position: relative;
            overflow: hidden;
            border-bottom: 1px solid var(--border);
        }
        .hero-section::before { content: ''; display: none; }

        /* Alerts */
        .alert {
            border-radius: var(--radius-md);
            border: 1px solid var(--border);
            box-shadow: var(--shadow-sm);
        }
        .alert-success { background: #ECFDF3; border-color: rgba(3,172,14,.18); color: #065f46; }
        .alert-danger { background: #FEF2F2; border-color: rgba(239,68,68,.12); color: #7f1d1d; }

        /* Footer (lebih clean) */
        .footer {
            background: var(--surface);
            color: var(--muted);
            padding: 34px 0 20px;
            margin-top: 0;
            border-top: 1px solid var(--border);
        }
        .footer h5, .footer h6 { color: var(--text) !important; }
        .footer a:hover { color: var(--brand) !important; }

        /* Availability badge */
        .badge-available { background: #ECFDF3; color: #065f46; border: 1px solid rgba(3,172,14,.25); }
        .badge-unavailable { background: #FEF2F2; color: #991b1b; border: 1px solid rgba(239,68,68,.20); }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 8px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb { background: rgba(17,24,39,.14); border-radius: 999px; }
        ::-webkit-scrollbar-thumb:hover { background: rgba(17,24,39,.28); }

        /* Topbar / Search + Icon styles */
        .nav-links { display:flex; gap:1rem; align-items:center; }
        .nav-links a { font-weight:600; padding: .35rem .5rem; border-radius:8px; color:var(--text); }
        .nav-links a:hover { color:var(--brand); background: rgba(3,172,14,0.03); }

        .nav-search {
            flex: 1 1 520px;
            min-width: 340px;
            max-width: 820px;
            margin: 0 1rem;
            position: relative;
            /* z-index only on desktop where needed (see media query below) */
        }
        .nav-search .input-group { width:100%; }

        .top-icons { display: flex; align-items: center; gap: .6rem; }
        .icon-btn {
            position: relative;
            width: 44px;
            height: 44px;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            border-radius: 10px;
            background: transparent;
            border: 1px solid transparent;
            transition: background .12s, border-color .12s, transform .08s;
        }
        .icon-btn:hover { background: rgba(16,24,40,.03); border-color: rgba(16,24,40,.04); transform: translateY(-2px); }
        .notif-badge {
            position: absolute;
            top: 6px;
            right: 6px;
            background: #ff5252;
            color: #fff;
            font-size: .63rem;
            padding: 3px 6px;
            border-radius: 999px;
            box-shadow: 0 6px 12px rgba(0,0,0,.08);
        }

        .avatar-circle {
            width: 40px;
            height: 40px;
            border-radius: 999px;
            overflow: hidden;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #fff;
            background: linear-gradient(135deg,var(--brand),var(--brand-600));
            border: 2px solid #fff;
            box-shadow: 0 6px 14px rgba(3,172,14,.10);
        }

        .navbar .dropdown-menu { min-width: 190px; right: 0; left: auto; }
        .navbar .dropdown-item { font-weight: 600; }

        @media (max-width: 991px) {
            .nav-search { max-width: 100%; margin: .7rem 0; width:100%; }
            .top-icons { gap: .4rem; }
        }

        /* Offcanvas mobile menu styling */
        .mobile-offcanvas .offcanvas-body { padding: 1rem; }
        .mobile-offcanvas .offcanvas-search { margin-bottom: .75rem; }
        .mobile-offcanvas .offcanvas-links a { display:flex; align-items:center; gap:.6rem; padding:.6rem .5rem; border-radius:8px; color:var(--text); text-decoration:none; }
        .mobile-offcanvas .offcanvas-links a:hover { background: rgba(3,172,14,0.04); color:var(--brand); }
        .mobile-offcanvas .offcanvas-footer { margin-top: .8rem; border-top:1px solid var(--border); padding-top:.8rem; }

        /* Mobile header: brand left, compact icons right */
        .mobile-header { display: none; position: relative; z-index: 40; }
        @media (max-width: 991px) {
            .mobile-header { display:flex; width:100%; justify-content:space-between; align-items:center; gap:.5rem; padding: .35rem 0; z-index:40; position: relative; }
            .mobile-header .navbar-brand { font-size:1rem; margin:0; padding:0; }
            .mobile-header .mobile-icons { display:flex; gap:.45rem; align-items:center; }
            .mobile-header .mobile-icon-btn { width:44px; height:44px; display:inline-flex; align-items:center; justify-content:center; border-radius:10px; background:transparent; border:1px solid rgba(0,0,0,0.04); position: relative; z-index:41; }
            .mobile-header .mobile-icon-btn .badge { position: absolute; top:6px; right:6px; font-size:.6rem; padding:2px 6px; }
            /* ensure mobile header elements accept pointer events */
            .mobile-header, .mobile-header * { pointer-events: auto; }
        }

        /* Ensure desktop search never overlaps icons and apply z-index only on desktop */
        @media (min-width: 992px) {
            .nav-search { max-width: 720px; z-index: 20; }
        }

        /* layout fix: make footer stay bottom */
        html, body {
            height: 100%;
        }
        .site-root {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
        }
        .site-content {
            flex: 1 1 auto; /* take remaining space */
        }

        /* PRODUCT GRID & CARD FIXES */
        /* Grid helper — pakai ini di halaman listing: <div class="product-grid"> ... </div> */
        .product-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 1.2rem;
            align-items: stretch;
        }

        /* Ensure product cards keep equal height and layout */
        .product-card {
            display: flex;
            flex-direction: column;
            height: 100%;
            min-height: 340px;
            overflow: hidden;
            background: var(--surface);
            position: relative; /* diperlukan untuk .stretched-link agar klik mengarah ke detail */
        }

        /* Image wrapper fallback: suport card-img-top atau plain img */
        .product-card .img-wrap,
        .product-card .card-img-top,
        .product-card img {
            width: 100%;
            height: 220px;
            display: block;
            object-fit: cover;
            background: #f3f4f6;
        }

        /* Make sure card body stretches so footer/buttons sit to bottom */
        .product-card .card-body {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            gap: .5rem;
            padding: 1rem 1.1rem;
            flex: 1 1 auto;
        }

        /* Small helpers for title/price row */
        .product-card .product-meta {
            display:flex;
            align-items:center;
            justify-content:space-between;
            gap:.5rem;
        }
        .product-card .product-title { font-weight:700; font-size: .98rem; color: var(--text); }
        .product-card .product-price { font-weight:800; color:var(--brand); }

        /* Ensure hover effect still works */
        .product-card:hover { transform: translateY(-4px); box-shadow: var(--shadow-md); border-color: rgba(3,172,14,.12); }

        /* If you used bootstrap's .card-img-top together with .card, make sure parent shows image */
        .card .card-img-top { display: block; }

        /* clickable wrapper for product cards */
        .product-link { display: block; color: inherit; text-decoration: none; }
        .product-link:hover .product-card { transform: translateY(-4px); box-shadow: var(--shadow-md); }

        /* Mobile navbar / offcanvas tweaks */
        @media (max-width: 991px) {
            .navbar { padding: 12px 0; }
            .navbar-brand { font-size: 1rem; }
            .icon-btn { width:48px; height:48px; border-radius:12px; }
            .notif-badge, .cart-badge { top:6px; right:6px; font-size:.6rem; padding:3px 6px; }
        }
    </style>

    @yield('styles')
</head>
<body>
    <div class="site-root">
        <!-- Navbar -->
        <nav class="navbar navbar-expand-lg sticky-top">
            <div class="container d-flex align-items-center">
                <!-- Mobile header (compact) -->
                <div class="mobile-header d-lg-none w-100">
                    <a class="navbar-brand" href="{{ route('home') }}">🍙 <span>Onigiri</span>Shop</a>
                    <div class="mobile-icons">
                        @auth
                            <a href="{{ route('cart.index') }}" class="mobile-icon-btn position-relative" title="Keranjang">
                                <i class="bi bi-bag"></i>
                                @php $cartCount = auth()->user()->cartItems()->count(); @endphp
                                @if($cartCount)<span class="badge bg-danger rounded-pill">{{ $cartCount }}</span>@endif
                            </a>
                            <a href="{{ route('notifications') }}" class="mobile-icon-btn position-relative" title="Notifikasi">
                                <i class="bi bi-bell"></i>
                                @php $orderAlerts = auth()->user()->orders()->whereIn('status', ['waiting_payment','waiting_confirmation','processing','shipping'])->count(); @endphp
                                @if($orderAlerts)<span class="badge bg-danger rounded-pill">{{ $orderAlerts }}</span>@endif
                            </a>
                        @endauth
                        <button class="btn btn-sm border-0 ms-1" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileMenu" aria-controls="mobileMenu">
                            <i class="bi bi-list fs-4"></i>
                        </button>
                    </div>
                </div>

                <!-- LEFT: brand + main links for desktop (hide on mobile to avoid duplicate) -->
                <div class="d-none d-lg-flex align-items-center gap-3 me-3">
                    <a class="navbar-brand" href="{{ route('home') }}">
                        🍙 <span>Onigiri</span>Shop
                    </a>
                    <div class="d-none d-lg-flex nav-links">
                        <a class="nav-link" href="{{ route('home') }}">Beranda</a>
                        <a class="nav-link" href="{{ route('products') }}">Menu</a>
                    </div>
                </div>

                <!-- CENTER: search (flexible) -->
                <div class="nav-search">
                    <form action="{{ route('products') }}" method="GET" class="w-100">
                        <div class="input-group">
                            <input name="q" type="search" class="form-control" placeholder="Cari menu, mis. Onigiri tuna, paket hemat..." aria-label="Search">
                            <button class="btn btn-search" type="submit" aria-label="Cari">
                                <i class="bi bi-search fs-5"></i>
                            </button>
                        </div>
                    </form>
                </div>

                <!-- RIGHT: icons / account (fixed) -->
                <div class="d-none d-lg-flex align-items-center ms-3">
                    <ul class="navbar-nav align-items-center gap-2 d-flex" style="list-style:none; margin:0; padding:0;">
                        @auth
                            <li class="nav-item">
                                <a class="icon-btn" href="{{ route('notifications') }}" title="Notifikasi"><i class="bi bi-bell fs-5"></i>
                                    @php $orderAlerts = auth()->user()->orders()->whereIn('status', ['waiting_payment','waiting_confirmation','processing','shipping'])->count(); @endphp
                                    @if($orderAlerts) <span class="notif-badge">{{ $orderAlerts }}</span> @endif
                                </a>
                            </li>
                            @if(!auth()->user()->isAdmin())
                            <li class="nav-item position-relative">
                                <a class="icon-btn" href="{{ route('cart.index') }}" title="Keranjang"><i class="bi bi-bag fs-5"></i>
                                    @php $cartCount = auth()->user()->cartItems()->count(); @endphp
                                    @if($cartCount) <span class="cart-badge">{{ $cartCount }}</span> @endif
                                </a>
                            </li>
                            @endif
                            <li class="nav-item dropdown">
                                <a class="nav-link d-flex align-items-center gap-2" href="#" data-bs-toggle="dropdown" aria-expanded="false">
                                    @if(auth()->user()->avatar)
                                        <img src="{{ asset('storage/'.auth()->user()->avatar) }}" alt="Avatar" class="rounded-circle" style="width:40px;height:40px;object-fit:cover;border:2px solid #fff;box-shadow:0 6px 14px rgba(3,172,14,.10);">
                                    @else
                                        <div class="avatar-circle">{{ strtoupper(substr(auth()->user()->name,0,1)) }}</div>
                                    @endif
                                    <div class="d-none d-lg-block text-start">
                                        <div style="line-height:1;">{{ auth()->user()->name }}</div>
                                        <small class="text-muted">Akun</small>
                                    </div>
                                </a>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('profile.show') }}"><i class="bi bi-person me-2"></i>Profil</a></li>
                                    <li><a class="dropdown-item" href="{{ route('orders.index') }}"><i class="bi bi-receipt me-2"></i>Pesanan</a></li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('logout') }}" method="POST" class="m-0">
                                            @csrf
                                            <button type="submit" class="dropdown-item text-danger"><i class="bi bi-box-arrow-right me-2"></i>Keluar</button>
                                        </form>
                                    </li>
                                </ul>
                            </li>
                        @else
                            <li class="nav-item d-flex gap-2 align-items-center">
                                <a class="nav-link" href="{{ route('login') }}">Masuk</a>
                                <a class="btn btn-primary btn-sm px-3" href="{{ route('register') }}">Daftar</a>
                            </li>
                        @endauth
                    </ul>
                </div>
            </div>
        </nav>

        <main class="site-content">
            <!-- Flash Messages -->
            @if(session('success'))
            <div class="alert alert-success alert-dismissible m-3" role="alert">
                <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif
            @if(session('error'))
            <div class="alert alert-danger alert-dismissible m-3" role="alert">
                <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="footer">
            <div class="container">
                <div class="row g-4">
                    <div class="col-md-4">
                        <h5 class="mb-3">🍙 OnigiriShop</h5>
                        <p class="small">Onigiri segar dan lezat dibuat dengan bahan premium. Dinikmati dengan cinta dari dapur kami ke meja makan Anda.</p>
                    </div>
                    <div class="col-md-4">
                        <h6 class="mb-3">Menu</h6>
                        <ul class="list-unstyled small">
                            <li><a href="{{ route('home') }}" class="text-decoration-none" style="color: inherit;">Beranda</a></li>
                            <li><a href="{{ route('products') }}" class="text-decoration-none" style="color: inherit;">Semua Menu</a></li>
                        </ul>
                    </div>
                    <div class="col-md-4">
                        <h6 class="mb-3">Kontak</h6>
                        <p class="small mb-1"><i class="bi bi-whatsapp me-2"></i>+62 812-3456-7890</p>
                        <p class="small mb-1"><i class="bi bi-envelope me-2"></i>hello@onigiri.shop</p>
                        <p class="small"><i class="bi bi-geo-alt me-2"></i>Jakarta, Indonesia</p>
                    </div>
                </div>
                <hr class="mt-4" style="border-color: var(--border);">
                <p class="text-center small mb-0">© {{ date('Y') }} OnigiriShop. All rights reserved.</p>
            </div>
        </footer>
    </div>

    {{-- mobile offcanvas: ensure this exists for all mobile togglers that target #mobileMenu --}}
    <div class="offcanvas offcanvas-start mobile-offcanvas" tabindex="-1" id="mobileMenu" aria-labelledby="mobileMenuLabel">
        <div class="offcanvas-header">
            <h5 class="offcanvas-title" id="mobileMenuLabel">Menu</h5>
            <button type="button" class="btn-close text-reset" data-bs-dismiss="offcanvas" aria-label="Close"></button>
        </div>
        <div class="offcanvas-body">
            <div class="offcanvas-search mb-2">
                <form action="{{ route('products') }}" method="GET">
                    <div class="input-group">
                        <input name="q" type="search" class="form-control form-control-sm" placeholder="Cari menu..." aria-label="Search">
                        <button class="btn btn-search btn-sm" type="submit"><i class="bi bi-search"></i></button>
                    </div>
                </form>
            </div>
            <div class="offcanvas-links">
                <a href="{{ route('home') }}"><i class="bi bi-house me-2"></i> Beranda</a>
                <a href="{{ route('products') }}"><i class="bi bi-list-ul me-2"></i> Menu</a>
                @auth
                    <a href="{{ route('orders.index') }}"><i class="bi bi-receipt me-2"></i> Pesanan</a>
                    <a href="{{ route('cart.index') }}"><i class="bi bi-bag me-2"></i> Keranjang @php $cartCount = auth()->user()->cartItems()->count(); @endphp @if($cartCount) <span class="cart-badge">{{ $cartCount }}</span> @endif</a>
                    <a href="{{ route('notifications') }}"><i class="bi bi-bell me-2"></i> Notifikasi @php $orderAlerts = auth()->user()->orders()->whereIn('status', ['waiting_payment','waiting_confirmation','processing','shipping'])->count(); @endphp @if($orderAlerts) <span class="notif-badge">{{ $orderAlerts }}</span> @endif</a>
                    <a href="{{ route('profile.show') }}"><i class="bi bi-person me-2"></i> Profil</a>
                    <form action="{{ route('logout') }}" method="POST" class="mt-2">
                        @csrf
                        <button type="submit" class="btn btn-outline-danger w-100">Keluar</button>
                    </form>
                @else
                    <a href="{{ route('login') }}"><i class="bi bi-box-arrow-in-right me-2"></i> Masuk</a>
                    <a href="{{ route('register') }}" class="btn btn-primary w-100 mt-2">Daftar</a>
                @endauth
            </div>
            <div class="offcanvas-footer text-muted small mt-3">
                Kontak: hello@onigiri.shop
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
