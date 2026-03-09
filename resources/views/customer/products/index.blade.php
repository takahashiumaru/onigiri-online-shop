@extends('layouts.app')

@section('title', 'Menu — Suki Onigiri')

@section('content')
<div class="container section-lg">
    {{-- Breadcrumb --}}
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:.8rem;">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item active">Menu</li>
        </ol>
    </nav>

    <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
        <div>
            <h4 style="font-weight:700;margin:0;">Semua Menu</h4>
            <p class="text-muted small mb-0">
                {{ $products->total() ?? $products->count() }} produk ditemukan
                @if(request('q')) untuk "{{ request('q') }}" @endif
            </p>
        </div>
    </div>

    @if($products->count())
    <div class="product-grid">
        @foreach($products as $product)
        <a href="{{ route('products.show', $product) }}" class="product-link">
            <div class="product-card">
                <div class="img-wrap">
                    <img src="{{ $product->image ? asset('storage/'.$product->image) : asset('images/default-product.svg') }}"
                         alt="{{ $product->name }}">
                </div>
                <div class="product-info">
                    <div class="product-title">{{ $product->name }}</div>
                    <div class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                    <div class="product-meta-row">
                        @if($product->stock > 0)
                            <span class="product-badge badge-available">Tersedia</span>
                        @else
                            <span class="product-badge badge-unavailable">Habis</span>
                        @endif
                        @if($product->category)
                            <span class="category-badge">{{ $product->category }}</span>
                        @endif
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>

    @if(method_exists($products, 'links'))
    <div class="d-flex justify-content-center mt-4">
        {{ $products->appends(request()->query())->links() }}
    </div>
    @endif

    @else
    @php
        $suggested = \App\Models\Product::latest()->take(5)->get();
    @endphp
    @if($suggested->count())
    <div class="product-grid">
        @foreach($suggested as $p)
        <a href="{{ route('products.show', $p) }}" class="product-link">
            <div class="product-card">
                <div class="img-wrap">
                    <img src="{{ $p->image ? asset('storage/'.$p->image) : asset('images/default-product.svg') }}" alt="{{ $p->name }}">
                </div>
                <div class="product-info">
                    <div class="product-title">{{ $p->name }}</div>
                    <div class="product-price">Rp {{ number_format($p->price, 0, ',', '.') }}</div>
                    <div class="product-meta-row">
                        @if($p->stock > 0)
                            <span class="product-badge badge-available">Tersedia</span>
                        @else
                            <span class="product-badge badge-unavailable">Habis</span>
                        @endif
                        <span class="category-badge">{{ $p->category }}</span>
                    </div>
                    <div class="mt-2">
                        @guest
                            <a href="{{ route('login') }}" class="btn btn-primary btn-sm w-100">Login untuk membeli</a>
                        @else
                            <a href="{{ route('products') }}" class="btn btn-outline-primary btn-sm w-100">Lihat Menu</a>
                        @endguest
                    </div>
                </div>
            </div>
        </a>
        @endforeach
    </div>
    @else
    <div class="empty-state">
        <i class="bi bi-search"></i>
        <h5>Produk tidak ditemukan</h5>
        <p>Coba kata kunci lain atau lihat semua menu.</p>
        <a href="{{ route('products') }}" class="btn btn-outline-primary btn-sm">Lihat Semua Menu</a>
    </div>
    @endif
    @endif
</div>
@endsection
