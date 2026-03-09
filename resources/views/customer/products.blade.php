@extends('layouts.app')

@section('title', 'Menu Onigiri')

@section('content')
<div class="container py-5">
    <div class="row g-4">
        <!-- Filter Sidebar -->
        <div class="col-lg-3">
            <div class="card sticky-top" style="top: 80px;">
                <div class="card-body">
                    <h6 class="fw-bold mb-3"><i class="bi bi-funnel me-2"></i>Filter</h6>
                    <form action="{{ route('products') }}" method="GET">
                        <div class="mb-3">
                            <input type="text" name="search" class="form-control rounded-pill" placeholder="Cari onigiri..." value="{{ request('search') }}">
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold small">Kategori</label>
                            <div class="d-flex flex-column gap-1">
                                <a href="{{ route('products') }}" class="btn btn-sm {{ !request('category') ? 'btn-primary' : 'btn-outline-secondary' }} text-start">Semua</a>
                                @foreach($categories as $cat)
                                <a href="{{ route('products', ['category' => $cat, 'search' => request('search')]) }}"
                                   class="btn btn-sm {{ request('category') === $cat ? 'btn-primary' : 'btn-outline-secondary' }} text-start">
                                    {{ ucfirst($cat) }}
                                </a>
                                @endforeach
                            </div>
                        </div>
                        <button type="submit" class="btn btn-primary w-100">Cari</button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="col-lg-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h4 class="fw-bold mb-0">
                    @if(request('search'))
                        Hasil pencarian "{{ request('search') }}"
                    @elseif(request('category'))
                        Onigiri {{ ucfirst(request('category')) }}
                    @else
                        Semua Menu 🍙
                    @endif
                </h4>
                <span class="text-muted small">{{ $products->total() }} produk</span>
            </div>

            @if($products->isEmpty())
            <div class="text-center py-5">
                <div style="font-size: 4rem;">🔍</div>
                <h5 class="mt-3">Produk tidak ditemukan</h5>
                <a href="{{ route('products') }}" class="btn btn-outline-primary mt-2">Reset Filter</a>
            </div>
            @else
            <div class="row g-4">
                @foreach($products as $product)
                <div class="col-sm-6 col-xl-4">
                    <div class="product-card h-100 js-product-card"
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
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}">
                            @else
                                <div class="d-flex align-items-center justify-content-center" style="height: 220px; background: linear-gradient(135deg, #fff5f5 0%, #ffe4e1 100%);">
                                    <span style="font-size: 5rem;">🍙</span>
                                </div>
                            @endif
                            <span class="category-badge position-absolute top-0 start-0 m-2">{{ ucfirst($product->category) }}</span>
                            @if(!$product->isInStock())
                            <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.5); border-radius: 16px 16px 0 0;">
                                <span class="badge bg-dark fs-6">Stok Habis</span>
                            </div>
                            @elseif($product->stock <= 5)
                            <span class="badge bg-warning position-absolute top-0 end-0 m-2">Sisa {{ $product->stock }}</span>
                            @endif
                        </div>
                        <div class="card-body d-flex flex-column">
                            <h6 class="fw-bold mb-1">{{ $product->name }}</h6>
                            <p class="text-muted small flex-grow-1 mb-2" style="display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical; overflow: hidden;">{{ $product->description }}</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                @auth
                                    @if(!auth()->user()->isAdmin())
                                    @if($product->isInStock())
                                    <form action="{{ route('cart.add', $product) }}" method="POST">
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
                @endforeach
            </div>

            <div class="mt-4 d-flex justify-content-center">
                {{ $products->appends(request()->query())->links() }}
            </div>
            @endif
        </div>
    </div>
</div>
{{-- include product detail modal so clicking cards can open it --}}
@include('customer.partials.product-modal')
@endsection
