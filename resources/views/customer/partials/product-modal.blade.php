<div class="modal fade" id="productDetailModal" tabindex="-1" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered product-modal-dialog">
    <div class="modal-content border-0 shadow-lg" style="border-radius:12px; overflow:hidden;">
      <div class="modal-header border-0 pb-0">
        <h5 class="modal-title fw-bold fs-3" id="productDetailModalLabel">Detail Produk</h5>
        <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>

      <div class="modal-body py-3">
        <div class="row g-4 align-items-center">
          <div class="col-md-5 text-center">
            <div class="bg-light rounded d-flex align-items-center justify-content-center modal-image-wrap" style="height:360px; overflow:hidden; border-radius:12px;">
              <img id="modalImage" src="" alt="" class="img-fluid" style="width:100%; height:100%; object-fit:cover; display:block;">
            </div>
          </div>

          <div class="col-md-7">
            <h3 id="modalName" class="fw-bold mb-1"></h3>
            <div class="mb-2"><small id="modalCategory" class="text-muted"></small></div>
            <p id="modalDescription" class="text-muted mb-3"></p>

            <div class="d-flex align-items-center gap-3 mb-3">
              <div id="modalPrice" class="fs-3 fw-bold" style="color:var(--brand-600);"></div>
              <div><span id="modalStock" class="badge bg-danger"></span></div>
            </div>

            <div class="d-flex align-items-center gap-4 mb-3">
              <div>
                <div class="small text-muted mb-1">Jumlah</div>
                <div class="d-flex align-items-center" style="gap:8px;">
                  <button type="button" class="btn btn-outline-secondary qty-btn" id="qtyDecrease" aria-label="Decrease" style="width:48px; height:48px; border-radius:12px;">−</button>
                  <input type="number" id="modalQuantityInput" name="quantity" class="form-control text-center" value="1" min="1" style="width:84px; height:48px; border-radius:12px; font-size:1.1rem;">
                  <button type="button" class="btn btn-outline-secondary qty-btn" id="qtyIncrease" aria-label="Increase" style="width:48px; height:48px; border-radius:12px;">+</button>
                </div>
                <small id="qtyHint" class="text-muted d-block mt-1"></small>
              </div>

              <div>
                <div class="small text-muted mb-1">Subtotal</div>
                <div id="modalSubtotal" class="fs-5 fw-bold" style="color:#222;"></div>
              </div>
            </div>

            <div class="d-flex gap-3 mt-4">
              <button type="button" class="btn btn-secondary btn-lg" data-bs-dismiss="modal" style="min-width:120px; border-radius:12px;">Tutup</button>

              {{-- add-to-cart form --}}
              <form id="modalAddForm" method="POST" style="display:inline-flex; align-items:center;">
                @csrf
                <input type="hidden" name="quantity" value="1" id="modalQuantityHidden">
                <button id="modalAddBtn" type="submit" class="btn btn-primary btn-lg d-flex align-items-center" style="border-radius:12px; padding:12px 22px;">
                  <i class="bi bi-bag-plus me-2"></i>
                  <span class="fw-bold">Tambah ke Keranjang</span>
                </button>
              </form>
            </div>

          </div>
        </div>
      </div>
    </div>
  </div>
</div>

{{-- local styles --}}
<style>
  /* subtle shadow for modal image area */
  #productDetailModal .bg-light { box-shadow: 0 6px 18px rgba(0,0,0,0.08); }
  .qty-btn { display:inline-flex; align-items:center; justify-content:center; font-size:1.25rem; }

  /* ensure modal image fills its box */
  .modal-image-wrap { background-color: #fff5f5; }
  .modal-image-wrap img#modalImage { width:100%; height:100%; object-fit:cover; }

  /* Desktop / large */
  .product-modal-dialog { max-width: 820px; }

  /* Tablet */
  @media (max-width: 991px) {
    .product-modal-dialog { max-width: 680px; }
    #productDetailModal .modal-body { padding-top: 14px; padding-bottom: 14px; }
    .modal-image-wrap { height:260px; }
    .modal-image-wrap img#modalImage { object-fit:cover; }
  }

  /* Mobile: lebih kecil supaya nyaman di HP */
  @media (max-width: 576px) {
    .product-modal-dialog { max-width: 92%; margin: 0 4%; }
    .modal-image-wrap { height:140px; }
    .modal-image-wrap img#modalImage { object-fit:cover; }
    #modalName { font-size: 1.05rem; }
    #modalPrice { font-size: 1.05rem; }
    #modalSubtotal { font-size: 0.95rem; }
    .qty-btn { width:40px; height:40px; font-size:1.05rem; border-radius:10px; }
    #modalQuantityInput { width:70px; height:40px; font-size:1rem; border-radius:10px; }
    .btn-lg { padding: 8px 14px; font-size: 0.95rem; }
    #productDetailModal .modal-body { padding: 12px; }
  }
</style>

{{-- Partial converted: disable modal and navigate to product page instead --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.js-product-card').forEach(function(card){
        card.style.cursor = 'pointer';
        card.addEventListener('click', function(e){
            // ignore clicks on real links/buttons inside the card
            if (e.target.closest('a') || e.target.closest('button') || e.target.closest('form')) return;
            const id = this.dataset.id;
            if (!id) return;
            // navigate to product detail page (route: /products/{product})
            window.location.href = '/products/' + id;
        });
    });
});
</script>
