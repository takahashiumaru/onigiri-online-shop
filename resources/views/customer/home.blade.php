@extends('layouts.app')

@section('title', 'Suki Onigiri — Onigiri Segar & Lezat')

@section('content')
{{-- add small back button (uses history.back) --}}
<div class="container">
    <div class="mb-3 d-md-none">
        <button class="btn btn-outline-secondary btn-sm" type="button" onclick="history.back();">
            ← Kembali
        </button>
    </div>
</div>

{{-- Hero --}}
<section class="hero-section">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-lg-7">
                <p class="text-muted mb-2" style="font-size:.85rem;font-weight:500;">🍙 #1 Suki Onigiri di Indonesia</p>
                <h1 style="font-weight:800;font-size:clamp(1.8rem,4vw,2.8rem);line-height:1.2;">
                    Onigiri Segar <span style="color:var(--brand);">Setiap Hari</span>
                </h1>
                <p class="text-muted mt-3 mb-4" style="max-width:480px;font-size:.95rem;">
                    Solusi praktis dan lezat untuk kantin sekolah, event, dan pemesanan dalam jumlah besar.
                </p>
                <div class="d-flex gap-2 flex-wrap">
                    <a href="{{ route('products') }}" class="btn btn-primary btn-lg">
                        <i class="bi bi-grid-3x3-gap me-1"></i> Lihat Menu
                    </a>
                    @guest
                    <a href="{{ route('register') }}" class="btn btn-outline-primary btn-lg">Daftar Gratis</a>
                    @endguest
                </div>
            </div>
            <div class="col-lg-5 d-none d-lg-block text-center">
                <div style="font-size:8rem;line-height:1;">🍙</div>
            </div>
        </div>
    </div>
</section>

{{-- Products --}}
<section class="section-lg">
    <div class="container">
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h4 style="font-weight:700;margin:0;">Menu Terbaru</h4>
                <p class="text-muted small mb-0">Pilihan onigiri segar hari ini</p>
            </div>
            <a href="{{ route('products') }}" class="btn btn-ghost btn-sm">
                Lihat Semua <i class="bi bi-arrow-right ms-1"></i>
            </a>
        </div>

        @if(isset($products) && $products->count())
        <div class="product-grid">
            @foreach($products as $product)
            <div class="product-link" data-href="{{ route('products.show', $product) }}" role="link" tabindex="0">
                <div class="product-card d-flex flex-column h-100">
                    {{-- image wrapper: fixed height to avoid layout shifts --}}
                    <div class="position-relative product-image-wrapper" style="height:220px; overflow:hidden;">
                        @if($product->image && \Storage::disk('public')->exists($product->image))
                            <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;display:block;">
                        @else
                            <div class="d-flex align-items-center justify-content-center w-100 h-100" style="background: linear-gradient(135deg, #fff5f5 0%, #ffe4e1 100%);">
                                <span style="font-size: 5rem;">🍙</span>
                            </div>
                        @endif

                        <span class="category-badge position-absolute top-0 start-0 m-2">{{ ucfirst($product->category) }}</span>

                        @if(!$product->isInStock())
                        <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.45);">
                            <span class="badge bg-dark fs-6">Stok Habis</span>
                        </div>
                        @elseif($product->stock <= 5)
                        <span class="badge bg-warning position-absolute top-0 end-0 m-2">Sisa {{ $product->stock }}</span>
                        @endif
                    </div>

                    {{-- body: allow description to take available space, actions stick to bottom --}}
                    <div class="card-body d-flex flex-column flex-grow-1" style="min-height:0;">
                        <h6 class="fw-bold mb-1">{{ $product->name }}</h6>
                        <p class="text-muted small flex-grow-1 mb-2" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                            {{ $product->description }}
                        </p>

                        <div class="d-flex justify-content-between align-items-center mt-2">
                            @php $stat = $productStats[$product->id] ?? ['avg'=>null,'count'=>0]; @endphp
                            <div class="d-flex align-items-start w-100 justify-content-between">
                                <div class="price-rating d-flex flex-column">
                                    <span class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                    <div class="rating-below-price mt-1">
                                        @includeIf('customer.partials.rating', ['avg' => $stat['avg'], 'count' => $stat['count']])
                                    </div>
                                </div>

                                <div class="ms-3 product-actions" style="min-width:86px; display:flex; align-items:center; justify-content:flex-end;">
                                    @auth
                                        @if(!auth()->user()->isAdmin())
                                            @if($product->isInStock())
                                                <form action="{{ route('cart.add', $product) }}" method="POST" class="m-0">
                                                    @csrf
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button class="btn btn-primary btn-sm px-3"><i class="bi bi-bag-plus"></i> Beli</button>
                                                </form>
                                            @else
                                                <span class="badge bg-secondary">Habis</span>
                                            @endif
                                        @endif
                                    @else
                                        <a href="{{ route('login') }}" class="btn btn-primary btn-sm px-3"><i class="bi bi-bag-plus"></i> Beli</a>
                                    @endauth
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- embedded JSON for navigation and data (include avg/count) --}}
                @php
                    $placeholder = 'data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22200%22%20height%3D%22150%22%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%3E%3Crect%20width%3D%22200%22%20height%3D%22150%22%20fill%3D%22%23fff5f5%22/%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20dominant-baseline%3D%22middle%22%20text-anchor%3D%22middle%22%20fill%3D%22%23f08a7a%22%20font-size%3D%2224%22%3E%F0%9F%8D%99%3C/text%3E%3C/svg%3E';
                    $imageUrl = ($product->image && \Storage::disk('public')->exists($product->image)) ? Storage::url($product->image) : $placeholder;
                    $stat = $productStats[$product->id] ?? ['avg'=>null,'count'=>0];
                @endphp
                <script type="application/json" class="product-json" data-product-id="{{ $product->id }}">
                {!! json_encode([
                    'id' => $product->id,
                    'name' => $product->name,
                    'price' => $product->price,
                    'price_formatted' => 'Rp '.number_format($product->price,0,',','.'),
                    'description' => strip_tags($product->description),
                    'image' => $imageUrl,
                    'stock' => $product->stock,
                    'isInStock' => $product->isInStock(),
                    'category' => ucfirst($product->category),
                    'addCartUrl' => route('cart.add', $product),
                    'showUrl' => route('products.show', $product),
                    'avg' => $stat['avg'],
                    'reviews_count' => $stat['count'],
                ]) !!}
                </script>
            </div>
            @endforeach
        </div>

        {{-- responsive rating CSS: ensure stars wrap/scale on small screens --}}
        <style>
        /* rating / price / actions layout */
        .product-card .price-rating { min-width:0; }
        .product-card .rating-below-price { display:block; }

        /* rating stars responsive */
        .product-card .stars { display:flex; gap:.25rem; align-items:center; flex-wrap:wrap; }
        .product-card .star { color:#E5E7EB; font-size:1rem; line-height:1; display:inline-block; }
        .product-card .star-selected { color:#F59E0B !important; }

        /* ensure actions column keeps height and alignment */
        .product-card .product-actions { white-space:nowrap; }

        @media (min-width: 1200px) {
            .product-card .star { font-size:1.05rem; }
        }
        @media (max-width: 991.98px) {
            .product-card .star { font-size:0.95rem; }
        }
        @media (max-width: 576px) {
            .product-card .star { font-size:0.85rem; }
            .product-card .product-price { font-size:0.95rem; }
            .product-card .small.text-muted { font-size:0.75rem; }
        }
        </style>

        {{-- remove modal-open logic: navigate to product page instead --}}
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('.product-link').forEach(function(el){
                el.style.cursor = 'pointer';
                el.addEventListener('click', function(e){
                    if (e.target.closest('a,button,form,input,svg')) return;
                    var jsonEl = el.querySelector('.product-json');
                    if (!jsonEl) return;
                    var data;
                    try { data = JSON.parse(jsonEl.textContent); } catch(err){ return; }
                    if (data && data.showUrl) {
                        window.location.href = data.showUrl;
                    }
                });
                el.addEventListener('keydown', function(e){
                    if (e.key === 'Enter' || e.key === ' ') {
                        if (document.activeElement && document.activeElement.closest && document.activeElement.closest('a,button,form,input,svg')) return;
                        var jsonEl = el.querySelector('.product-json');
                        if (!jsonEl) return;
                        var data;
                        try { data = JSON.parse(jsonEl.textContent); } catch(err){ return; }
                        if (data && data.showUrl) window.location.href = data.showUrl;
                    }
                });
            });
        });
        </script>
        @else
        @php
            $fallback = \App\Models\Product::latest()->take(5)->get();
        @endphp
        @if($fallback->count())
            @include('customer.partials.product-grid', ['products' => $fallback])
        @else
            <div class="empty-state">
                <i class="bi bi-box-seam"></i>
                <h5>Belum ada produk</h5>
                <p>Menu akan segera hadir. Pantau terus ya!</p>
            </div>
        @endif
        @endif
    </div>
</section>

@include('customer.partials.product-modal')
@endsection
