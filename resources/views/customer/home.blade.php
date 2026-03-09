@extends('layouts.app')

@section('title', 'Beranda')

@section('content')
<!-- Hero -->
<section class="hero-section">
    <div class="container">
        <!-- page-specific responsive tweaks -->
        <style>
        /* Mobile-friendly hero and product adjustments */
        @media (max-width: 767.98px) {
          .hero-section { padding: 28px 0; }
          .hero-section .display-4 { font-size: 1.75rem; line-height:1.15; }
          .hero-section .lead { font-size: .95rem; }
          .hero-section .d-flex.gap-3.flex-wrap { flex-direction: column; gap: .6rem; }
          .hero-section .display-4 span { color: #F4A261; }

          /* product card images shorter on mobile */
          .product-card .img-wrap,
          .product-card .card-img-top,
          .product-card img { height: 140px; }

          .product-card .card-body { padding: .75rem; }
          .product-card .product-title { font-size: .95rem; }

          /* make action buttons full width on phone */
          .product-card .btn-sm.w-100-on-mobile { display:block; width:100%; }
          .product-card .btn-sm.w-100-on-mobile + .btn-sm { margin-top: .5rem; }

          /* tighten hero stats */
          .hero-section .fw-bold.fs-5 { font-size: 1rem; }
        }

        /* small tablets */
        @media (min-width: 768px) and (max-width: 991.98px) {
          .product-card .img-wrap,
          .product-card .card-img-top,
          .product-card img { height: 180px; }
        }

        /* keep desktop appearance unchanged but ensure buttons not too large */
        .product-card .btn-sm { padding-top: .38rem; padding-bottom: .38rem; }
        </style>

        <div class="row align-items-center">
            <div class="col-lg-6">
                <span class="badge bg-warning text-dark mb-3 px-3 py-2">🆕 Menu Terbaru</span>
                <h1 class="display-4 fw-bold mb-3">Onigiri Segar<br><span style="color: #F4A261;">Setiap Hari!</span></h1>
                <p class="lead mb-4" style="color: rgba(0, 0, 0, 0.8);">
                    Nasi kepal Jepang autentik dengan beragam isian lezat. Dibuat fresh setiap hari dengan bahan pilihan terbaik.
                </p>
                <!-- CTAs: stack on mobile -->
                <div class="d-flex gap-3 flex-column flex-sm-row">
                    <a href="{{ route('products') }}" class="btn btn-warning btn-lg fw-bold px-4 w-100 w-sm-auto">
                        <i class="bi bi-grid me-2"></i>Lihat Menu
                    </a>
                    @guest
                    <a href="{{ route('register') }}" class="btn btn-outline-light btn-lg px-4 w-100 w-sm-auto">
                        Daftar Sekarang
                    </a>
                    @endguest
                </div>
                <div class="d-flex gap-4 mt-4">
                    <div>
                        <div class="fw-bold fs-5 text-black">10+</div>
                        <small style="color: rgba(24, 24, 24, 0.6);">Menu Tersedia</small>
                    </div>
                    <div>
                        <div class="fw-bold fs-5 text-black">1000+</div>
                        <small style="color: rgba(24, 24, 24, 0.6);">Pelanggan Happy</small>
                    </div>
                    <div>
                        <div class="fw-bold fs-5" style="color: #F4A261;">★ 4.9</div>
                        <small style="color: rgba(24, 24, 24, 0.6);">Rating</small>
                    </div>
                </div>
            </div>
            <div class="col-lg-6 text-center d-none d-lg-block">
                <div style="font-size: 12rem; line-height: 1; opacity: 0.9; filter: drop-shadow(0 10px 30px rgba(0,0,0,0.3));">🍙</div>
            </div>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="py-5">
    <div class="container">
        <div class="d-flex flex-wrap gap-2 justify-content-center mb-5">
            <a href="{{ route('products') }}" class="btn btn-outline-primary rounded-pill px-4">
                🍙 Semua Menu
            </a>
            @foreach($categories as $cat)
            <a href="{{ route('products', ['category' => $cat]) }}" class="btn btn-outline-secondary rounded-pill px-4">
                {{ ucfirst($cat) }}
            </a>
            @endforeach
        </div>

        <div class="text-center mb-5">
            <h2 class="fw-bold">Menu Unggulan</h2>
            <p class="text-muted">Pilihan onigiri terfavorit pelanggan kami</p>
        </div>

        <div class="row g-4">
            @foreach($featured as $product)
            <div class="col-sm-6 col-md-4 col-lg-4">
                <div class="product-card js-product-card"
                     role="button"
                     style="cursor: pointer;"
                     data-id="{{ $product->id }}"
                     data-name="{{ $product->name }}"
                     data-description="{{ $product->description }}"
                     data-price="{{ number_format($product->price, 0, ',', '.') }}"
                     data-price-raw="{{ $product->price }}"
                     data-stock="{{ $product->stock }}"
                     data-category="{{ $product->category }}"
                     data-image="{{ ($product->image && \Storage::disk('public')->exists($product->image)) ? Storage::url($product->image) : '' }}">
                    <div class="position-relative">
                        @if($product->image && \Storage::disk('public')->exists($product->image))
                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="card-img-top">
                        @else
                            <div class="d-flex align-items-center justify-content-center" style="height: 220px; background: linear-gradient(135deg, #fff5f5 0%, #ffe4e1 100%);">
                                <span style="font-size: 5rem;">🍙</span>
                            </div>
                        @endif
                        <span class="category-badge position-absolute top-0 start-0 m-2">{{ ucfirst($product->category) }}</span>
                        @if($product->stock <= 5 && $product->stock > 0)
                        <span class="badge bg-warning position-absolute top-0 end-0 m-2">Sisa {{ $product->stock }}</span>
                        @endif
                    </div>
                    <div class="card-body">
                        <h6 class="fw-bold mb-1">{{ $product->name }}</h6>
                        <p class="text-muted small mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $product->description }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            @auth
                            @if(!auth()->user()->isAdmin())
                                @if($product->isInStock())
                                <form action="{{ route('cart.add', $product) }}" method="POST" class="w-100 w-sm-auto d-sm-inline-block ms-sm-2">
                                    @csrf
                                    <input type="hidden" name="quantity" value="1">
                                    <button class="btn btn-primary btn-sm px-3 w-100-on-mobile">
                                        <i class="bi bi-bag-plus"></i>
                                    </button>
                                </form>
                                @else
                                <span class="badge bg-secondary">Habis</span>
                                @endif
                            @endif
                            @else
                            <a href="{{ route('login') }}" class="btn btn-primary btn-sm px-3 w-100-on-mobile">
                                <i class="bi bi-bag-plus"></i>
                            </a>
                            @endauth
                        </div>
                    </div>
                </div>
            </div>
            @endforeach
        </div>

        <div class="text-center mt-5">
            <a href="{{ route('products') }}" class="btn btn-outline-primary btn-lg px-5 rounded-pill">
                Lihat Semua Menu <i class="bi bi-arrow-right ms-2"></i>
            </a>
        </div>
    </div>
</section>

<!-- Features -->
<section class="py-5" style="background: white;">
    <div class="container">
        <div class="row g-4 text-center">
            <div class="col-md-3">
                <div class="py-3">
                    <div style="font-size: 3rem;" class="mb-3">🥗</div>
                    <h6 class="fw-bold">Bahan Segar</h6>
                    <p class="text-muted small">Dibuat setiap hari dengan bahan premium pilihan</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="py-3">
                    <div style="font-size: 3rem;" class="mb-3">🚀</div>
                    <h6 class="fw-bold">Pengiriman Cepat</h6>
                    <p class="text-muted small">Order sebelum jam 10, tiba hari yang sama</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="py-3">
                    <div style="font-size: 3rem;" class="mb-3">💳</div>
                    <h6 class="fw-bold">Bayar Mudah</h6>
                    <p class="text-muted small">QRIS, transfer bank, kartu kredit via Midtrans</p>
                </div>
            </div>
            <div class="col-md-3">
                <div class="py-3">
                    <div style="font-size: 3rem;" class="mb-3">❤️</div>
                    <h6 class="fw-bold">Dibuat dengan Cinta</h6>
                    <p class="text-muted small">Resep autentik Jepang yang sudah teruji</p>
                </div>
            </div>
        </div>
    </div>
</section>
{{-- make modal available on home page as well --}}
@include('customer.partials.product-modal')
@endsection
