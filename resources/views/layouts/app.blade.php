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
            --primary: #E63946;
            --primary-dark: #c1121f;
            --secondary: #F4A261;
            --accent: #2A9D8F;
            --dark: #1a1a2e;
            --light-bg: #FFF8F0;
            --card-shadow: 0 4px 20px rgba(0,0,0,0.08);
        }

        * { box-sizing: border-box; }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: var(--light-bg);
            color: #333;
        }

        /* Navbar */
        .navbar {
            background: linear-gradient(135deg, var(--dark) 0%, #16213e 100%);
            box-shadow: 0 2px 20px rgba(0,0,0,0.3);
            padding: 12px 0;
        }
        .navbar-brand {
            font-family: 'Playfair Display', serif;
            font-size: 1.6rem;
            color: #fff !important;
            letter-spacing: 1px;
        }
        .navbar-brand span { color: var(--secondary); }
        .nav-link { color: rgba(255,255,255,0.85) !important; font-weight: 500; transition: color 0.2s; }
        .nav-link:hover { color: var(--secondary) !important; }
        .cart-badge {
            background: var(--primary);
            color: white;
            border-radius: 50%;
            font-size: 0.65rem;
            padding: 2px 6px;
            position: absolute;
            top: -5px;
            right: -8px;
        }

        /* Buttons */
        .btn-primary {
            background: var(--primary);
            border-color: var(--primary);
            font-weight: 600;
            border-radius: 8px;
            transition: all 0.2s;
        }
        .btn-primary:hover { background: var(--primary-dark); border-color: var(--primary-dark); transform: translateY(-1px); }
        .btn-outline-primary { color: var(--primary); border-color: var(--primary); border-radius: 8px; font-weight: 600; }
        .btn-outline-primary:hover { background: var(--primary); color: white; }

        /* Cards */
        .product-card {
            border: none;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: var(--card-shadow);
            transition: transform 0.3s, box-shadow 0.3s;
            background: white;
        }
        .product-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 12px 35px rgba(0,0,0,0.15);
        }
        .product-card img {
            height: 220px;
            object-fit: cover;
            width: 100%;
        }
        .product-card .card-body { padding: 1.2rem; }
        .product-price {
            font-size: 1.2rem;
            font-weight: 700;
            color: var(--primary);
        }
        .category-badge {
            background: var(--light-bg);
            color: var(--accent);
            border: 1px solid var(--accent);
            border-radius: 20px;
            padding: 2px 12px;
            font-size: 0.75rem;
            font-weight: 600;
        }

        /* Hero */
        .hero-section {
            background: linear-gradient(135deg, var(--dark) 0%, #16213e 50%, #0f3460 100%);
            color: white;
            padding: 80px 0;
            position: relative;
            overflow: hidden;
        }
        .hero-section::before {
            content: '🍙';
            position: absolute;
            font-size: 20rem;
            opacity: 0.05;
            right: -50px;
            top: -50px;
        }

        /* Alert */
        .alert { border-radius: 12px; border: none; }

        /* Footer */
        .footer {
            background: var(--dark);
            color: rgba(255,255,255,0.7);
            padding: 40px 0 20px;
            margin-top: 60px;
        }

        /* Badge */
        .badge-available { background: #d1fae5; color: #065f46; }
        .badge-unavailable { background: #fee2e2; color: #991b1b; }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: #f1f1f1; }
        ::-webkit-scrollbar-thumb { background: var(--primary); border-radius: 3px; }
    </style>

    @yield('styles')
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg sticky-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('home') }}">
                🍙 <span>Onigiri</span>Shop
            </a>
            <button class="navbar-toggler border-0 text-white" type="button" data-bs-toggle="collapse" data-bs-target="#navbarMain">
                <i class="bi bi-list fs-4"></i>
            </button>
            <div class="collapse navbar-collapse" id="navbarMain">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('home') ? 'text-warning' : '' }}" href="{{ route('home') }}">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->routeIs('products*') ? 'text-warning' : '' }}" href="{{ route('products') }}">Menu</a>
                    </li>
                </ul>
                <ul class="navbar-nav ms-auto align-items-center gap-2">
                    @auth
                        @if(!auth()->user()->isAdmin())
                        <li class="nav-item">
                            <a class="nav-link position-relative" href="{{ route('cart.index') }}">
                                <i class="bi bi-bag fs-5"></i>
                                @php $cartCount = auth()->user()->cartItems()->count(); @endphp
                                @if($cartCount > 0)
                                    <span class="cart-badge">{{ $cartCount }}</span>
                                @endif
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('orders.index') }}"><i class="bi bi-receipt me-1"></i>Pesanan</a>
                        </li>
                        @else
                        <li class="nav-item">
                            <a class="nav-link text-warning" href="{{ route('admin.dashboard') }}"><i class="bi bi-speedometer2 me-1"></i>Admin Panel</a>
                        </li>
                        @endif
                        <li class="nav-item dropdown">
                            <a class="nav-link dropdown-toggle" href="#" data-bs-toggle="dropdown">
                                <i class="bi bi-person-circle me-1"></i>{{ auth()->user()->name }}
                            </a>
                            <ul class="dropdown-menu dropdown-menu-end">
                                <li><a class="dropdown-item" href="#"><i class="bi bi-person me-2"></i>Profil</a></li>
                                <li><hr class="dropdown-divider"></li>
                                <li>
                                    <form action="{{ route('logout') }}" method="POST">
                                        @csrf
                                        <button type="submit" class="dropdown-item text-danger">
                                            <i class="bi bi-box-arrow-right me-2"></i>Keluar
                                        </button>
                                    </form>
                                </li>
                            </ul>
                        </li>
                    @else
                        <li class="nav-item">
                            <a class="nav-link" href="{{ route('login') }}">Masuk</a>
                        </li>
                        <li class="nav-item">
                            <a class="btn btn-primary btn-sm px-3" href="{{ route('register') }}">Daftar</a>
                        </li>
                    @endauth
                </ul>
            </div>
        </div>
    </nav>

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

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row g-4">
                <div class="col-md-4">
                    <h5 class="text-white mb-3">🍙 OnigiriShop</h5>
                    <p class="small">Onigiri segar dan lezat dibuat dengan bahan premium. Dinikmati dengan cinta dari dapur kami ke meja makan Anda.</p>
                </div>
                <div class="col-md-4">
                    <h6 class="text-white mb-3">Menu</h6>
                    <ul class="list-unstyled small">
                        <li><a href="{{ route('home') }}" class="text-decoration-none" style="color: inherit;">Beranda</a></li>
                        <li><a href="{{ route('products') }}" class="text-decoration-none" style="color: inherit;">Semua Menu</a></li>
                    </ul>
                </div>
                <div class="col-md-4">
                    <h6 class="text-white mb-3">Kontak</h6>
                    <p class="small mb-1"><i class="bi bi-whatsapp me-2"></i>+62 812-3456-7890</p>
                    <p class="small mb-1"><i class="bi bi-envelope me-2"></i>hello@onigiri.shop</p>
                    <p class="small"><i class="bi bi-geo-alt me-2"></i>Jakarta, Indonesia</p>
                </div>
            </div>
            <hr class="border-secondary mt-4">
            <p class="text-center small mb-0">© {{ date('Y') }} OnigiriShop. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @yield('scripts')
</body>
</html>
