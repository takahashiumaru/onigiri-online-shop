@extends('layouts.app')

@section('title', isset($product) ? $product->name . ' — Onigiri Shop' : 'Produk — Onigiri Shop')

@section('content')
<section class="section-lg">
    <div class="container">
        @if(!isset($product))
            <div class="empty-state">
                <i class="bi bi-exclamation-circle"></i>
                <h5>Produk tidak ditemukan</h5>
                <p>Maaf, produk yang Anda cari tidak tersedia.</p>
                <a href="{{ route('products') }}" class="btn btn-ghost">Kembali ke Menu</a>
            </div>
        @else
        <div class="row g-4">
            <div class="col-md-6">
                @if($product->image && \Storage::disk('public')->exists($product->image))
                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="img-fluid rounded">
                @else
                    <div class="d-flex align-items-center justify-content-center rounded" style="height:400px;background:linear-gradient(135deg,#fff5f5 0%,#ffe4e1 100%);">
                        <span style="font-size:6rem;">🍙</span>
                    </div>
                @endif
            </div>

            <div class="col-md-6">
                <h1 style="font-weight:800;">{{ $product->name }}</h1>
                <p class="text-muted mb-2">{{ $product->short_description ?? '' }}</p>

                <div class="mb-3">
                    <span style="font-size:1.25rem;font-weight:700;">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                </div>

                <div class="mb-3">
                    @if($product->stock > 0)
                        <span class="badge bg-success">Tersedia</span>
                    @else
                        <span class="badge bg-secondary">Habis</span>
                    @endif

                    @if(!empty($product->category))
                        <span class="badge bg-light text-dark ms-2">{{ $product->category }}</span>
                    @endif
                </div>

                <p class="text-muted" style="max-width:520px;">
                    {!! nl2br(e($product->description ?? 'Deskripsi tidak tersedia.')) !!}
                </p>

                <div class="d-flex gap-2 mt-4">
                    <a href="{{ route('products') }}" class="btn btn-ghost">Kembali</a>

                    @if($product->stock > 0)
                        <form action="{{ route('cart.add', $product) }}" method="POST" class="d-inline">
                            @csrf
                            <input type="hidden" name="quantity" value="1">
                            <button type="submit" class="btn btn-primary">
                                Tambah ke Keranjang
                            </button>
                        </form>
                    @else
                        <button class="btn btn-outline-secondary" disabled>Habis</button>
                    @endif
                </div>
            </div>
        </div>
        @endif
    </div>
</section>
@endsection
