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
                    <div class="product-link" data-href="{{ route('products.show', $product) }}" role="link" tabindex="0">
                        <div class="product-card d-flex flex-column h-100">
                            {{-- image wrapper --}}
                            <div class="position-relative product-image-wrapper" style="height:220px; overflow:hidden; border-radius: 12px 12px 0 0;">
                                @if($product->image && \Storage::disk('public')->exists($product->image))
                                    <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" style="width:100%;height:100%;object-fit:cover;display:block;">
                                @else
                                    <div class="d-flex align-items-center justify-content-center w-100 h-100" style="background: linear-gradient(135deg, #fff5f5 0%, #ffe4e1 100%);">
                                        <span style="font-size: 4rem;">🍙</span>
                                    </div>
                                @endif

                                <span class="category-badge position-absolute top-0 start-0 m-2">{{ ucfirst($product->category) }}</span>

                                @if(!$product->isInStock())
                                <div class="position-absolute top-0 start-0 w-100 h-100 d-flex align-items-center justify-content-center" style="background: rgba(0,0,0,0.45);">
                                    <span class="badge bg-dark fs-6">Stok Habis</span>
                                </div>
                                @endif
                            </div>

                            {{-- card body --}}
                            <div class="card-body d-flex flex-column flex-grow-1" style="padding: 1.15rem;">
                                <div class="mb-2">
                                    <h6 class="fw-bold mb-1 text-dark text-truncate" style="font-size: 1.05rem;">{{ $product->name }}</h6>
                                    <p class="text-muted small mb-0" style="display:-webkit-box;-webkit-line-clamp:1;-webkit-box-orient:vertical;overflow:hidden; line-height: 1.5;">
                                        {{ $product->description }}
                                    </p>
                                </div>

                                <div class="mt-auto">
                                    <div class="d-flex justify-content-between align-items-center mb-3">
                                        <span class="fw-bold fs-5" style="color: var(--brand);">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                        @auth
                                            @if(!auth()->user()->isAdmin() && $product->isInStock())
                                                <form action="{{ route('cart.add', $product) }}" method="POST" class="m-0">
                                                    @csrf
                                                    <input type="hidden" name="quantity" value="1">
                                                    <button class="btn btn-primary btn-sm px-3 rounded-pill fw-semibold shadow-sm">
                                                        <i class="bi bi-cart-plus me-1"></i> Beli
                                                    </button>
                                                </form>
                                            @elseif(!auth()->user()->isAdmin() && !$product->isInStock())
                                                <span class="badge bg-secondary text-white-50" style="font-size: 0.7rem;">Habis</span>
                                            @endif
                                        @else
                                            <a href="{{ route('login') }}" class="btn btn-primary btn-sm px-3 rounded-pill fw-semibold shadow-sm">
                                                <i class="bi bi-cart-plus me-1"></i> Beli
                                            </a>
                                        @endauth
                                    </div>

                                    @php $stat = $productStats[$product->id] ?? ['avg'=>null,'count'=>0]; @endphp
                                    <div class="d-flex align-items-center gap-2 pt-2 border-top">
                                        @includeIf('customer.partials.rating', ['avg' => $stat['avg'], 'count' => $stat['count']])
                                        @if($product->isInStock())
                                            <div class="ms-auto">
                                                <span class="badge bg-light text-muted border-0 fw-normal" style="font-size: 0.75rem; padding: 0.25rem 0.6rem; border-radius: 6px;">
                                                    <i class="bi bi-box-seam me-1"></i> {{ $product->stock }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>
                                </div>
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

<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.product-link').forEach(function(el){
        el.style.cursor = 'pointer';
        el.addEventListener('click', function(e){
            // If click was on button, form, or interactive element, don't navigate
            if (e.target.closest('a,button,form,input,svg')) return;
            var href = el.dataset.href;
            if (href) {
                window.location.href = href;
            }
        });
        el.addEventListener('keydown', function(e){
            if (e.key === 'Enter' || e.key === ' ') {
                if (document.activeElement && document.activeElement.closest && document.activeElement.closest('a,button,form,input,svg')) return;
                var href = el.dataset.href;
                if (href) window.location.href = href;
            }
        });
    });
});
</script>
@endsection
