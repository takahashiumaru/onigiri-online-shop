@extends('layouts.app')

@section('title', 'Keranjang Belanja')

@section('content')
<div class="container py-5">
    <h3 class="fw-bold mb-4"><i class="bi bi-bag me-2"></i>Keranjang Belanja</h3>

    @if($cartItems->isEmpty())
    <div class="text-center py-5">
        <div style="font-size: 5rem;">🛒</div>
        <h5 class="mt-3 text-muted">Keranjang belanja kosong</h5>
        <a href="{{ route('products') }}" class="btn btn-primary mt-3 px-4">Mulai Belanja</a>
    </div>
    @else
    @php
        $totalQty = $cartItems->sum('quantity');
    @endphp
    <style>
        /* Responsive cart layout (gunakan palette global --brand / --brand-600 / --brand-light) */
        .cart-item { gap: 1rem; align-items: center; }
        .item-image img,
        .item-image .placeholder {
            border-radius: .5rem;
            object-fit: cover;
        }
        .quantity-input { text-align: center; }

        /* Desktop sizes */
        @media (min-width: 768px) {
            .item-image img,
            .item-image .placeholder { width: 80px; height: 80px; }
            .quantity-input { width: 75px; }
            .order-summary { top: 80px; position: sticky; }
        }

        /* Mobile / small screens */
        @media (max-width: 767.98px) {
            .cart-item { flex-direction: column; align-items: stretch; }
            .item-image { width: 100%; }
            .item-image img,
            .item-image .placeholder { width: 100%; height: 150px; }
            .item-details { width: 100%; margin-top: .5rem; }
            .item-controls { width: 100%; display: flex; justify-content: space-between; align-items: center; margin-top: .5rem; gap: .5rem; }
            .quantity-input { width: 85px; }
            .order-summary { position: static; top: auto; }
            .card .card-header .fw-semibold { font-size: .95rem; }
        }
        /* Hide spinner for number input */
        .quantity-input::-webkit-outer-spin-button,
        .quantity-input::-webkit-inner-spin-button { -webkit-appearance: none; margin: 0; }
        .quantity-input { -moz-appearance: textfield; }

        /* Accent menggunakan palette merah global */
        .tp-accent { background: var(--brand); border-color: var(--brand); color: #fff; }
        .tp-badge { background: var(--brand-light); color: var(--brand); padding: .25rem .5rem; border-radius: 999px; font-size: .8rem; }
    </style>
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span class="fw-semibold">{{ $cartItems->count() }} item</span>
                    <form action="{{ route('cart.clear') }}" method="POST">
                        @csrf @method('DELETE')
                        <button type="submit" class="btn btn-outline-danger btn-sm">
                            <i class="bi bi-trash me-1"></i>Kosongkan
                        </button>
                    </form>
                </div>
                <div class="card-body p-0">
                    @foreach($cartItems as $item)
                    <div class="d-flex align-items-center p-3 border-bottom cart-item">
                        <div class="flex-shrink-0">
                            <div class="item-image">
                                @if($item->product->image && \Storage::disk('public')->exists($item->product->image))
                                    <img src="{{ Storage::url($item->product->image) }}" alt="{{ $item->product->name }}">
                                @else
                                    <div class="placeholder d-flex align-items-center justify-content-center" style="background: var(--brand-light); font-size: 2.5rem;">🍙</div>
                                @endif
                            </div>
                        </div>
                        <div class="flex-grow-1 item-details">
                            <h6 class="fw-bold mb-0">{{ $item->product->name }}</h6>
                            <div class="d-flex align-items-center gap-2">
                                <small class="text-muted">{{ ucfirst($item->product->category) }}</small>
                                <span class="tp-badge">Penjual • Toko</span>
                            </div>
                            <div class="fw-semibold" style="color:var(--brand-600);">Rp {{ number_format($item->product->price, 0, ',', '.') }}</div>
                            <div class="text-muted small">Rp {{ number_format($item->product->price * $item->quantity,0,',','.') }} subtotal</div>
                        </div>
                        <div class="d-flex align-items-center gap-2 item-controls">
                            <form action="{{ route('cart.update', $item) }}" method="POST" class="d-flex align-items-center gap-1">
                                @csrf @method('PATCH')
                                <button type="button" class="btn btn-outline-secondary btn-sm px-2" onclick="decreaseQty(this)" data-max="{{ $item->product->stock }}">-</button>
                                <input type="number" name="quantity" value="{{ $item->quantity }}" min="1" max="{{ $item->product->stock }}" class="form-control form-control-sm text-center quantity-input" onchange="this.form.submit()">
                                <button type="button" class="btn btn-outline-secondary btn-sm px-2" onclick="increaseQty(this)" data-max="{{ $item->product->stock }}">+</button>
                            </form>
                            <div class="fw-bold text-nowrap" style="min-width: 100px; text-align: right;">
                                Rp {{ number_format($item->quantity * $item->product->price, 0, ',', '.') }}
                            </div>
                            <form action="{{ route('cart.remove', $item) }}" method="POST">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-link text-danger p-1">
                                    <i class="bi bi-x-lg"></i>
                                </button>
                            </form>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-lg-4">
            <div class="card order-summary">
                <div class="card-header"><h6 class="fw-bold mb-0">Ringkasan Pesanan</h6></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Subtotal</span>
                        <span class="fw-semibold">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Pengiriman</span>
                        <span class="fw-semibold">Rp 10.000</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mb-3">
                        <span class="fw-bold">Total</span>
                        <span class="fw-bold fs-5" style="color:var(--brand-600);">Rp {{ number_format($total + 10000, 0, ',', '.') }}</span>
                    </div>

                    @if($totalQty < 20)
                    <div class="alert alert-warning py-2 mb-3 border-0" style="font-size: 0.85rem; background-color: #fff9db; color: #856404;">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>Minimal pembelian adalah 20 pcs. Kurang <strong>{{ 20 - $totalQty }} pcs</strong> lagi.
                    </div>
                    @endif

                    <a href="{{ route('checkout.index') }}" 
                       class="btn tp-accent w-100 py-2 {{ $totalQty < 10 ? 'disabled' : '' }}"
                       @if($totalQty < 10) style="pointer-events: none; opacity: 0.6;" @endif>
                        <i class="bi bi-credit-card me-2"></i>Checkout Sekarang
                    </a>
                    <a href="{{ route('products') }}" class="btn btn-outline-secondary w-100 mt-2">
                        <i class="bi bi-arrow-left me-2"></i>Lanjut Belanja
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection

@section('scripts')
<script>
function decreaseQty(btn) {
    const form = btn.closest('form');
    const input = form.querySelector('input[name=quantity]');
    const val = parseInt(input.value);
    if (val > 1) { input.value = val - 1; form.submit(); }
}
function increaseQty(btn) {
    const form = btn.closest('form');
    const input = form.querySelector('input[name=quantity]');
    const max = parseInt(btn.dataset.max);
    const val = parseInt(input.value);
    if (val < max) { input.value = val + 1; form.submit(); }
}
</script>
@endsection
