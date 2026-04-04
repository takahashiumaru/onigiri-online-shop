<div class="product-grid">
    @foreach($products as $product)
    {{-- changed: use div with data-href instead of outer anchor --}}
    <div class="product-link" data-href="{{ route('products.show', $product) }}" role="link" tabindex="0">
        <div class="product-card d-flex flex-column h-100">
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
                @endif
            </div>

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
                        <div style="display:flex; align-items:center;">
                            @auth
                                @if(!auth()->user()->isAdmin())
                                    @if($product->isInStock())
                                        <form action="{{ route('cart.add', $product) }}" method="POST" class="m-0">
                                            @csrf
                                            <input type="hidden" name="quantity" value="1">
                                            <button class="btn btn-primary btn-sm px-3 rounded-pill fw-semibold shadow-sm">
                                                <i class="bi bi-cart-plus me-1"></i> Beli
                                            </button>
                                        </form>
                                    @else
                                        <span class="badge bg-secondary text-white-50" style="font-size: 0.7rem;">Habis</span>
                                    @endif
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="btn btn-primary btn-sm px-3 rounded-pill fw-semibold shadow-sm">
                                    <i class="bi bi-cart-plus me-1"></i> Beli
                                </a>
                            @endauth
                        </div>
                    </div>

                    @php $stat = $productStats[$product->id] ?? ['avg'=>null,'count'=>0]; @endphp
                    <div class="d-flex align-items-center gap-2 pt-2 border-top">
                        @includeIf('customer.partials.rating', ['avg' => $stat['avg'] ?? null, 'count' => $stat['count'] ?? 0])
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
    @endforeach
</div>

{{-- duplicate safe: small script ensures cards are clickable when this partial is rendered standalone --}}
<script>
(function(){
    function setupClickableCards(root){
        root.querySelectorAll('.product-link').forEach(function(el){
            el.style.cursor = 'pointer';
            el.addEventListener('click', function(e){
                if (e.target.closest('a,button,form,input,svg')) return;
                var href = el.dataset.href;
                if (href) window.location.href = href;
            });
            el.addEventListener('keydown', function(e){
                if (e.key === 'Enter' || e.key === ' ') {
                    if (document.activeElement && document.activeElement.closest && document.activeElement.closest('a,button,form,input,svg')) return;
                    var href = el.dataset.href;
                    if (href) window.location.href = href;
                }
            });
        });
    }
    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', function(){ setupClickableCards(document); });
    } else {
        setupClickableCards(document);
    }
})();
</script>
