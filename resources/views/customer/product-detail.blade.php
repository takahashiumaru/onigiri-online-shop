@extends('layouts.app')

@section('title', ($product->name ?? 'Produk') . ' — Suki Onigiri')

@section('content')
<div class="container py-3">
    {{-- Breadcrumb breadcrumb-item style --}}
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb mb-0" style="font-size: 0.85rem;">
            <li class="breadcrumb-item"><a href="{{ route('home') }}" class="text-muted">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products') }}" class="text-muted">Semua Menu</a></li>
            <li class="breadcrumb-item active text-dark fw-semibold" aria-current="page">{{ $product->name }}</li>
        </ol>
    </nav>    <div class="row g-4 mb-5">
        {{-- 1. LEFT: Images Section --}}
        <div class="col-lg-4">
            <div class="sticky-top" style="top: 100px; z-index: 10;">
                <div class="product-image-container border rounded-4 bg-white overflow-hidden shadow-sm">
                    @if($product->image && \Storage::disk('public')->exists($product->image))
                        <img src="{{ \Storage::url($product->image) }}" class="img-fluid w-100" style="object-fit:cover; aspect-ratio:1/1;" alt="{{ $product->name }}">
                    @else
                        <div class="d-flex align-items-center justify-content-center bg-light" style="aspect-ratio:1/1;">
                            <span style="font-size: 8rem;">🍙</span>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- 2. MIDDLE: Info Section --}}
        <div class="col-lg-5">
            <div class="product-info-main border-bottom pb-3 mb-4">
                <h4 class="fw-bold text-dark mb-1 d-block" style="font-size: 1.4rem; line-height: 1.3;">{{ $product->name }}</h4>
                
                @php
                    $avg = $reviews->avg('rating') ?? null;
                    $count = $reviews->count();
                @endphp

                <div class="d-flex align-items-center gap-2 mb-3">
                    <span class="text-dark fw-bold small"><i class="bi bi-star-fill text-warning me-1"></i>{{ $avg ?: '0.0' }}</span>
                    <span class="text-muted small">({{ $count }} ulasan)</span>
                    <span class="text-muted small">|</span>
                    <span class="text-muted small">Terjual <span class="text-dark fw-bold ms-1">{{ $soldCount ?? 0 }}</span></span>
                </div>

                <div class="price-section">
                    <h2 class="fw-800 text-dark mb-0" style="font-size: 2.2rem;">Rp {{ number_format($product->price, 0, ',', '.') }}</h2>
                </div>
            </div>

            <div class="description-section">
                <ul class="nav nav-tabs border-bottom-0 mb-3" id="productTab" role="tablist">
                    <li class="nav-item">
                        <button class="nav-link active fw-bold text-brand border-0 border-bottom border-primary border-3 bg-transparent px-0 me-4" id="detail-tab" data-bs-toggle="tab" data-bs-target="#detail" type="button" role="tab">Detail Produk</button>
                    </li>
                </ul>
                <div class="tab-content" id="productTabContent">
                    <div class="tab-pane fade show active" id="detail" role="tabpanel">
                        <div class="text-muted" style="line-height: 1.7; white-space: pre-line; font-size: 0.95rem;">
                            {{ $product->description ?: 'Bahan-bahan segar disiapkan setiap hari. Kami hanya menggunakan beras berkualitas premium untuk onigiri kami untuk menjamin rasa yang paling lezat.' }}
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- 3. RIGHT: Action Box --}}
        <div class="col-lg-3">
            <div class="sticky-top" style="top: 100px;">
                <div class="card border rounded-4 shadow-sm">
                    <div class="card-body p-4">
                        <h6 class="fw-bold mb-3">Atur Jumlah</h6>
                        
                        <form action="{{ route('cart.add', $product) }}" method="POST">
                            @csrf
                            <div class="d-flex align-items-center gap-3 mb-4">
                                <div class="quantity-picker border rounded-3 d-flex align-items-center p-1" style="width: 110px;">
                                    <button type="button" class="btn btn-link btn-sm text-brand p-0 px-2 border-0" onclick="updateQty(-1)"><i class="bi bi-dash-lg"></i></button>
                                    <input type="number" name="quantity" id="qtyInput" value="1" min="1" max="{{ $product->stock }}" class="form-control form-control-sm border-0 text-center fw-bold p-0" onchange="calculateSubtotal()">
                                    <button type="button" class="btn btn-link btn-sm text-brand p-0 px-2 border-0" onclick="updateQty(1)"><i class="bi bi-plus-lg"></i></button>
                                </div>
                                <span class="small text-muted">Stok: <span class="fw-bold">{{ $product->stock }}</span></span>
                            </div>

                            <div class="d-flex justify-content-between align-items-center mb-4 pt-3 border-top">
                                <span class="text-muted small">Subtotal</span>
                                <span class="fw-bold text-dark fs-5" id="subtotalDisplay">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            </div>

                            @auth
                                @if(!auth()->user()->isAdmin())
                                    <button type="submit" class="btn btn-primary w-100 rounded-3 py-2 fw-bold mb-2 shadow-hover">
                                        <i class="bi bi-cart-plus me-1"></i> + Keranjang
                                    </button>
                                    <button type="submit" name="buy_now" value="1" class="btn btn-outline-primary w-100 rounded-3 py-2 fw-bold shadow-hover">
                                        Beli Langsung
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary w-100 rounded-3 py-2 fw-bold shadow-hover">
                                    Login untuk Membeli
                                </a>
                            @endauth
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Review Section (Full Width below) --}}
    <div class="mt-5 border-top pt-5">
        <h4 class="fw-bold text-dark mb-4">Ulasan Pembeli</h4>
        @if($reviews->count())
            <div class="row g-4">
                @foreach($reviews as $r)
                <div class="col-md-6">
                    <div class="card border rounded-4 h-100 bg-white">
                        <div class="card-body p-4">
                            <div class="d-flex align-items-center justify-content-between mb-3">
                                <div class="d-flex align-items-center gap-2">
                                    <div class="bg-primary-subtle text-primary fw-bold small rounded-circle d-flex align-items-center justify-content-center shadow-sm" style="width: 32px; height: 32px;">
                                        {{ strtoupper(substr($r->order->shipping_name ?? 'P', 0, 1)) }}
                                    </div>
                                    <span class="fw-bold small text-dark">{{ $r->order->shipping_name ?? 'Pembeli' }}</span>
                                </div>
                                <div class="text-warning small">
                                    @for($i=1;$i<=5;$i++)
                                        <i class="bi bi-star{{ $i <= $r->rating ? '-fill' : '' }}"></i>
                                    @endfor
                                </div>
                            </div>
                            <p class="text-muted small mb-0" style="line-height: 1.6;">{{ $r->rating_review ?: 'Sangat puas!' }}</p>
                            <div class="mt-2 text-muted small" style="font-size: 0.7rem;">{{ $r->updated_at->format('d M Y') }}</div>
                        </div>
                    </div>
                </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-5 bg-light rounded-4 border border-dashed">
                <i class="bi bi-star text-muted fs-1 opacity-50"></i>
                <p class="text-muted mt-2">Belum ada ulasan untuk produk ini.</p>
            </div>
        @endif
    </div>
</div>
@endsection

@section('styles')
<style>
    .nav-tabs .nav-link:hover { color: var(--brand); }
    .nav-tabs .nav-link.active { border-color: var(--brand) !important; color: var(--brand) !important; }
    .quantity-picker input::-webkit-outer-spin-button,
    .quantity-picker input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
    .quantity-picker input[type=number] { -moz-appearance: textfield; }
    .shadow-hover { transition: all 0.2s ease; }
    .shadow-hover:hover { box-shadow: 0 8px 24px rgba(0,0,0,0.12) !important; }
</style>
@endsection

@section('scripts')
<script>
    const price = {{ $product->price }};
    const subtotalDisplay = document.getElementById('subtotalDisplay');
    const qtyInput = document.getElementById('qtyInput');

    function updateQty(delta) {
        let val = parseInt(qtyInput.value) || 1;
        val += delta;
        if (val < 1) val = 1;
        if (val > {{ $product->stock }}) val = {{ $product->stock }};
        qtyInput.value = val;
        calculateSubtotal();
    }

    function calculateSubtotal() {
        let val = parseInt(qtyInput.value) || 1;
        const total = val * price;
        subtotalDisplay.innerText = 'Rp ' + total.toLocaleString('id-ID');
    }
</script>
@endsection
