@extends('layouts.app')

@section('title', $product->name . ' — Suki Onigiri')

@section('content')
<div class="container section-lg">
    <nav aria-label="breadcrumb" class="mb-3">
        <ol class="breadcrumb" style="font-size:.8rem;">
            <li class="breadcrumb-item"><a href="{{ route('home') }}">Beranda</a></li>
            <li class="breadcrumb-item"><a href="{{ route('products') }}">Menu</a></li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row g-4">
        {{-- Image --}}
        <div class="col-md-5">
            <div class="tp-card overflow-hidden">
                <img src="{{ $product->image ? asset('storage/'.$product->image) : 'https://via.placeholder.com/600x600/f3f4f6/9ca3af?text=🍙' }}"
                     alt="{{ $product->name }}"
                     style="width:100%;aspect-ratio:1/1;object-fit:cover;display:block;">
            </div>
        </div>

        {{-- Info --}}
        <div class="col-md-7">
            <div class="tp-card">
                <div class="tp-card-body" style="padding:28px;">
                    @if($product->category)
                        <span class="category-badge mb-2 d-inline-block">{{ $product->category }}</span>
                    @endif
                    <h2 style="font-weight:800;margin-bottom:8px;">{{ $product->name }}</h2>

                    <div style="font-size:1.75rem;font-weight:800;color:var(--brand);margin-bottom:16px;">
                        Rp {{ number_format($product->price, 0, ',', '.') }}
                    </div>

                    <div class="d-flex align-items-center gap-2 mb-3">
                        @if($product->stock > 0)
                            <span class="status-badge status-completed"><i class="bi bi-check-circle-fill"></i> Stok: {{ $product->stock }}</span>
                        @else
                            <span class="status-badge status-cancelled"><i class="bi bi-x-circle-fill"></i> Stok Habis</span>
                        @endif
                    </div>

                    @if($product->description)
                    <div class="mb-4">
                        <h6 style="font-weight:700;font-size:.85rem;color:var(--text-secondary);">Deskripsi</h6>
                        <p style="color:var(--text-secondary);font-size:.9rem;line-height:1.7;">{{ $product->description }}</p>
                    </div>
                    @endif

                    @auth
                        @if(!auth()->user()->isAdmin() && $product->stock > 0)
                        <form action="{{ route('cart.add', $product) }}" method="POST">
                            @csrf
                            <div class="d-flex align-items-center gap-3">
                                <div class="d-flex align-items-center gap-2">
                                    <label class="form-label mb-0" style="font-size:.85rem;">Jumlah</label>
                                    <input type="number" name="quantity" value="1" min="1" max="{{ $product->stock }}"
                                           class="form-control" style="width:80px;">
                                </div>
                                <button type="submit" class="btn btn-primary flex-grow-1">
                                    <i class="bi bi-cart-plus me-1"></i> Tambah ke Keranjang
                                </button>
                            </div>
                        </form>
                        @elseif($product->stock <= 0)
                        <button class="btn btn-outline-secondary w-100" disabled>Stok Habis</button>
                        @endif
                    @else
                        <a href="{{ route('login') }}" class="btn btn-primary w-100">
                            <i class="bi bi-box-arrow-in-right me-1"></i> Login untuk Memesan
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
