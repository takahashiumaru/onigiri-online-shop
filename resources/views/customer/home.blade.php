@extends('layouts.app')

@section('title', 'Suki Onigiri — Onigiri Segar & Lezat')

@section('content')
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
            {{-- changed: use div with data-href instead of outer anchor to avoid nested anchors --}}
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
                            <span class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                            {{-- action wrapper: fixed height so guest/auth variations don't change card height --}}
                            <div style="min-height:38px; display:flex; align-items:center;">
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

                {{-- embedded JSON for modal population (hidden) --}}
                @php
                    // lightweight SVG placeholder as data URI so modal always has an image to display
                    $placeholder = 'data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22200%22%20height%3D%22150%22%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%3E%3Crect%20width%3D%22200%22%20height%3D%22150%22%20fill%3D%22%23fff5f5%22/%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20dominant-baseline%3D%22middle%22%20text-anchor%3D%22middle%22%20fill%3D%22%23f08a7a%22%20font-size%3D%2224%22%3E%F0%9F%8D%99%3C/text%3E%3C/svg%3E';
                    $imageUrl = ($product->image && \Storage::disk('public')->exists($product->image)) ? Storage::url($product->image) : $placeholder;
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
                    'showUrl' => route('products.show', $product)
                ]) !!}
                </script>

            </div>
            @endforeach
        </div>

        {{-- Product modal (pop-up) --}}
        {{-- gunakan partial modal yang sudah ada agar konsisten dengan view produk --}}
        @includeWhen(View::exists('customer.partials.product-modal'), 'customer.partials.product-modal')
        {{-- jika partial tidak ada, tambahkan partial tersebut atau sesuaikan id/modal yang dipakai di customer.partials.product-modal --}}

        {{-- make product-link open modal populated from embedded JSON; robust modal detection (include productDetailModal) --}}
        <script>
        (function(){
            window.isAuthenticated = @json(auth()->check());
            window.csrfToken = {!! json_encode(csrf_token()) !!};

            function findModalRoot(){
                return document.getElementById('productModal')
                    || document.getElementById('product-modal')
                    || document.getElementById('productDetailModal') // partial uses this id
                    || document.querySelector('[data-role="product-modal"]')
                    || document.querySelector('.product-modal');
            }

            function findField(modal, selectors){
                for(var i=0;i<selectors.length;i++){
                    var sel = selectors[i];
                    try {
                        var el = modal.querySelector(sel);
                        if(el) return el;
                    } catch(e){}
                }
                return null;
            }

            function setupClickableCards(root){
                root.querySelectorAll('.product-link').forEach(function(el){
                    el.style.cursor = 'pointer';
                    el.addEventListener('click', function(e){
                        if (e.target.closest('a,button,form,input,svg')) return;
                        var jsonEl = el.querySelector('.product-json');
                        if (!jsonEl) return;
                        var data;
                        try { data = JSON.parse(jsonEl.textContent); } catch(err){ return; }
                        openProductModal(data);
                    });
                    el.addEventListener('keydown', function(e){
                        if (e.key === 'Enter' || e.key === ' ') {
                            if (document.activeElement && document.activeElement.closest && document.activeElement.closest('a,button,form,input,svg')) return;
                            var jsonEl = el.querySelector('.product-json');
                            if (!jsonEl) return;
                            var data;
                            try { data = JSON.parse(jsonEl.textContent); } catch(err){ return; }
                            openProductModal(data);
                        }
                    });
                });
            }

            function showModalWithBootstrap(modalRoot){
                if (window.bootstrap && typeof bootstrap.Modal === 'function') {
                    return new bootstrap.Modal(modalRoot, { keyboard: true });
                }
                return null;
            }

            function showFallback(modalRoot){
                modalRoot.classList.add('show');
                modalRoot.style.display = 'block';
                modalRoot.setAttribute('aria-modal','true');
                modalRoot.removeAttribute('aria-hidden');
                if (!document.querySelector('.modal-backdrop')) {
                    var bk = document.createElement('div');
                    bk.className = 'modal-backdrop fade show';
                    document.body.appendChild(bk);
                }
                document.body.classList.add('modal-open');
            }

            function openProductModal(data){
                var modalRoot = findModalRoot();
                if (!modalRoot) {
                    console.warn('Product modal not found in DOM.');
                    return;
                }

                // support both generic modal ids and the productDetailModal partial's ids
                var titleEl = findField(modalRoot, ['#productModalTitle','[data-modal-title]','#productDetailModalLabel','.product-modal-title','h5.modal-title']);
                var nameEl = findField(modalRoot, ['#productModalName','[data-modal-name]','#modalName']);
                var priceEl = findField(modalRoot, ['#productModalPrice','[data-modal-price]','#modalPrice']);
                var descEl = findField(modalRoot, ['#productModalDescription','[data-modal-description]','#modalDescription']);
                var catEl = findField(modalRoot, ['#productModalCategory','[data-modal-category]','#modalCategory']);
                var imgEl = findField(modalRoot, ['#productModalImage','[data-modal-image]','img.product-modal-image','#modalImage']);
                var stockEl = findField(modalRoot, ['#productModalStock','[data-modal-stock]','#modalStock']);
                var buyBtn = findField(modalRoot, ['#productModalBuy','[data-modal-buy]','button.product-modal-buy','#modalAddBtn']);
                var feedback = findField(modalRoot, ['#productModalFeedback','[data-modal-feedback]','#productModalFeedback']);
                var addForm = findField(modalRoot, ['#modalAddForm','#productModalForm','#modalAddForm']);
                var subtotalEl = findField(modalRoot, ['#modalSubtotal']);

                if (titleEl) titleEl.textContent = data.name || 'Produk';
                if (nameEl) nameEl.textContent = data.name || '';
                if (priceEl) priceEl.textContent = data.price_formatted || '';
                if (descEl) descEl.textContent = data.description || '';
                if (catEl) catEl.textContent = data.category || '';

                if (imgEl) {
                    if (data.image) {
                        imgEl.src = data.image;
                        imgEl.alt = data.name || '';
                        imgEl.style.display = '';
                    } else {
                        imgEl.src = '';
                        imgEl.alt = '';
                        imgEl.style.display = 'none';
                    }
                }

                if (stockEl) {
                    if (!data.isInStock) {
                        stockEl.innerHTML = '<span class="badge bg-dark">Stok Habis</span>';
                        if (buyBtn) buyBtn.disabled = true;
                    } else if (data.stock !== null && data.stock <= 5) {
                        stockEl.innerHTML = '<span class="badge bg-warning">Sisa '+data.stock+'</span>';
                        if (buyBtn) buyBtn.disabled = false;
                    } else {
                        stockEl.innerHTML = '';
                        if (buyBtn) buyBtn.disabled = false;
                    }
                }

                // if partial has a form, set its action (keeps partial behavior)
                if (addForm && addForm.tagName === 'FORM') {
                    try {
                        addForm.action = data.addCartUrl || addForm.action;
                    } catch(e){}
                }

                // attach buy handler (if present) — keep existing form-submit if form exists
                if (buyBtn) {
                    var newBuy = buyBtn.cloneNode(true);
                    buyBtn.parentNode.replaceChild(newBuy, buyBtn);
                    newBuy.addEventListener('click', function(e){
                        // if there's a form, let form submit normally (safer). Otherwise use fetch.
                        if (addForm && addForm.tagName === 'FORM') {
                            return; // allow native submit (form already has CSRF)
                        }
                        if (feedback) feedback.style.display = 'none';
                        if (!window.isAuthenticated) {
                            window.location.href = '{{ route("login") }}';
                            return;
                        }
                        fetch(data.addCartUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/x-www-form-urlencoded; charset=UTF-8',
                                'X-CSRF-TOKEN': window.csrfToken,
                                'X-Requested-With': 'XMLHttpRequest'
                            },
                            body: new URLSearchParams({ quantity: 1 })
                        }).then(function(res){
                            if (res.ok) return res.json().catch(function(){ return { ok:true }; });
                            throw new Error('Network response was not ok');
                        }).then(function(){
                            if (feedback) {
                                feedback.style.display = 'block';
                                feedback.innerHTML = '<div class="alert alert-success mb-0">Produk ditambahkan ke keranjang</div>';
                            }
                        }).catch(function(){
                            if (feedback) {
                                feedback.style.display = 'block';
                                feedback.innerHTML = '<div class="alert alert-danger mb-0">Gagal menambahkan ke keranjang</div>';
                            }
                        });
                    });
                }

                // update subtotal if partial exposes subtotal element
                if (subtotalEl && data.price) {
                    try {
                        subtotalEl.textContent = data.price_formatted || '';
                    } catch(e){}
                }

                var bsModal = showModalWithBootstrap(modalRoot);
                if (bsModal) bsModal.show();
                else showFallback(modalRoot);
            }

            document.addEventListener('DOMContentLoaded', function(){ setupClickableCards(document); });
        })();
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
