@extends('layouts.app')

@section('title', 'Keranjang — Onigiri Shop')

@section('content')
<div class="container section-lg">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:.8rem;">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item active">Keranjang</li>
        </ol>
    </nav>

    <h4 style="font-weight:700;" class="mb-4">Keranjang Belanja</h4>

    @if(isset($cartItems) && $cartItems->count())
    <div class="row g-4">
        <div class="col-lg-8">
            <div class="tp-card">
                <div class="tp-card-header d-flex justify-content-between align-items-center">
                    <span style="font-weight:600;">{{ $cartItems->count() }} Produk</span>
                    <form action="{{ route('cart.clear') }}" method="POST" onsubmit="return confirm('Hapus semua item?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-ghost btn-sm text-danger" type="submit">
                            <i class="bi bi-trash3 me-1"></i>Hapus Semua
                        </button>
                    </form>
                </div>
                <div class="tp-card-body p-0">
                    @foreach($cartItems as $item)
                    <div class="d-flex align-items-center gap-3 p-3 {{ !$loop->last ? 'border-bottom' : '' }}" style="border-color:var(--border-light) !important;">
                        <img src="{{ $item->product && $item->product->image ? asset('storage/'.$item->product->image) : 'https://via.placeholder.com/80/f3f4f6/9ca3af?text=🍙' }}"
                             alt="" style="width:72px;height:72px;object-fit:cover;border-radius:var(--radius-sm);flex-shrink:0;">
                        <div class="flex-grow-1 min-w-0">
                            <div style="font-weight:600;font-size:.9rem;">{{ $item->product->name ?? 'Produk' }}</div>
                            <div style="font-weight:700;color:var(--brand);font-size:.9rem;">
                                Rp {{ number_format(($item->product->price ?? 0) * $item->quantity, 0, ',', '.') }}
                            </div>
                        </div>
                        <form action="{{ route('cart.update', $item) }}" method="POST" class="d-flex align-items-center gap-2">
                            @csrf @method('PATCH')
                            <input type="number" name="quantity" value="{{ $item->quantity }}" min="1"
                                   class="form-control form-control-sm" style="width:64px;" onchange="this.form.submit()">
                        </form>
                        <form action="{{ route('cart.remove', $item) }}" method="POST">
                            @csrf @method('DELETE')
                            <button class="tp-icon-btn" style="width:36px;height:36px;color:var(--danger);" title="Hapus">
                                <i class="bi bi-trash3"></i>
                            </button>
                        </form>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="tp-card" style="position:sticky;top:80px;">
                <div class="tp-card-header">
                    <h6 class="mb-0" style="font-weight:700;">Ringkasan Belanja</h6>
                </div>
                <div class="tp-card-body">
                    @php
                        $total = $cartItems->sum(fn($i) => ($i->product->price ?? 0) * $i->quantity);
                    @endphp
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Total ({{ $cartItems->count() }} produk)</span>
                        <span style="font-weight:700;">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <hr style="border-color:var(--border-light);">
                    <div class="d-flex justify-content-between mb-3">
                        <span style="font-weight:700;">Total Bayar</span>
                        <span style="font-weight:800;font-size:1.1rem;color:var(--brand);">Rp {{ number_format($total, 0, ',', '.') }}</span>
                    </div>
                    <a href="{{ route('checkout.index') }}" class="btn btn-primary w-100">
                        Checkout <i class="bi bi-arrow-right ms-1"></i>
                    </a>
                </div>
            </div>
        </div>
    </div>
    @else
    <div class="empty-state">
        <i class="bi bi-cart3"></i>
        <h5>Keranjang Kosong</h5>
        <p>Belum ada produk di keranjang. Yuk mulai belanja!</p>
        <a href="{{ route('products') }}" class="btn btn-primary">Belanja Sekarang</a>
    </div>
    @endif
</div>
@endsection
