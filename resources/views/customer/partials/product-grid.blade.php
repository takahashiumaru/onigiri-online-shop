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
                @elseif($product->stock <= 5)
                <span class="badge bg-warning position-absolute top-0 end-0 m-2">Sisa {{ $product->stock }}</span>
                @endif
            </div>

            <div class="card-body d-flex flex-column flex-grow-1" style="min-height:0;">
                <h6 class="fw-bold mb-1">{{ $product->name }}</h6>
                <p class="text-muted small flex-grow-1 mb-2" style="display:-webkit-box;-webkit-line-clamp:2;-webkit-box-orient:vertical;overflow:hidden;">
                    {{ $product->description }}
                </p>

                <div class="d-flex justify-content-between align-items-center mt-2">
                    <span class="product-price">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
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
