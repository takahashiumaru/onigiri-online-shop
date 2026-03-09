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
              <div id="modalPrice" class="fs-3 fw-bold" style="color:#00a859;"></div>
              <div><span id="modalStock" class="badge bg-success"></span></div>
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
                <button id="modalAddBtn" type="submit" class="btn btn-success btn-lg d-flex align-items-center" style="background:#00a859; border-color:#00a859; border-radius:12px; padding:12px 22px;">
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

{{-- script to populate and show the modal with improved UI and qty/subtotal handling --}}
<script>
document.addEventListener('DOMContentLoaded', function () {
  const modalEl = document.getElementById('productDetailModal');
  if (!modalEl) return;
  const bsModal = new bootstrap.Modal(modalEl);

  const modalImage = document.getElementById('modalImage');
  const modalName = document.getElementById('modalName');
  const modalCategory = document.getElementById('modalCategory');
  const modalDescription = document.getElementById('modalDescription');
  const modalPrice = document.getElementById('modalPrice');
  const modalStock = document.getElementById('modalStock');
  const modalAddForm = document.getElementById('modalAddForm');
  const qtyInput = document.getElementById('modalQuantityInput');
  const qtyHidden = document.getElementById('modalQuantityHidden');
  const qtyInc = document.getElementById('qtyIncrease');
  const qtyDec = document.getElementById('qtyDecrease');
  const subtotalEl = document.getElementById('modalSubtotal');
  const qtyHint = document.getElementById('qtyHint');

  // placeholder image (SVG data URI)
  const placeholder = 'data:image/svg+xml;charset=UTF-8,%3Csvg%20width%3D%22200%22%20height%3D%22150%22%20xmlns%3D%22http%3A//www.w3.org/2000/svg%22%3E%3Crect%20width%3D%22200%22%20height%3D%22150%22%20fill%3D%22%23fff5f5%22/%3E%3Ctext%20x%3D%2250%25%22%20y%3D%2250%25%22%20dominant-baseline%3D%22middle%22%20text-anchor%3D%22middle%22%20fill%3D%22%23f08a7a%22%20font-size%3D%2224%22%3E%F0%9F%8D%99%3C/text%3E%3C/svg%3E';

  function sanitizeNumber(v, fallback=0){
    const n = parseInt(v);
    return isNaN(n) ? fallback : n;
  }
  function formatCurrency(num){
    if (isNaN(num)) return '';
    return 'Rp ' + num.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
  }

  // keep state for current product
  let current = { id: '', price: 0, stock: 0 };

  function updateSubtotal(){
    const q = sanitizeNumber(qtyInput.value, 1);
    const subtotal = current.price * q;
    subtotalEl.textContent = formatCurrency(subtotal);
    qtyHidden.value = q;
    // hint and button disable logic
    if (current.stock <= 0) {
      qtyHint.textContent = 'Stok habis';
      qtyInc.disabled = true;
      qtyDec.disabled = true;
      qtyInput.disabled = true;
      modalAddForm.querySelector('button[type=submit]').disabled = true;
    } else {
      qtyInput.disabled = false;
      modalAddForm.querySelector('button[type=submit]').disabled = false;
      qtyHint.textContent = (current.stock <= 5) ? ('Sisa ' + current.stock + ' tersisa') : '';
      qtyInc.disabled = q >= current.stock;
      qtyDec.disabled = q <= 1;
    }
  }

  document.querySelectorAll('.js-product-card').forEach(function(card){
    card.addEventListener('click', function(e){
      if (e.target.closest('form') || e.target.closest('a') || e.target.closest('button')) return;

      const id = this.dataset.id || '';
      const name = this.dataset.name || '';
      const description = this.dataset.description || '';
      const priceRaw = sanitizeNumber(this.dataset.priceraw ?? this.dataset.priceRaw, 0);
      const stock = sanitizeNumber(this.dataset.stock, 0);
      const category = this.dataset.category || '';
      const image = (this.dataset.image || '').trim();

      current.id = id;
      current.price = priceRaw;
      current.stock = stock;

      modalName.textContent = name;
      modalCategory.textContent = category ? category.charAt(0).toUpperCase() + category.slice(1) : '';
      modalDescription.textContent = description;
      modalPrice.textContent = formatCurrency(priceRaw);
      modalStock.textContent = (stock > 0) ? ('Sisa ' + stock) : 'Habis';
      if (stock === 0) {
        modalStock.classList.remove('bg-success');
        modalStock.classList.add('bg-secondary');
      } else {
        modalStock.classList.remove('bg-secondary');
        modalStock.classList.add('bg-success');
      }

      // set modal image:
      // 1) gunakan data-image jika ada
      // 2) jika tidak, coba ambil <img> di dalam card (mis. <img src="...">)
      // 3) jika tidak ada, gunakan placeholder
      if (image) {
        modalImage.src = image;
        modalImage.alt = name;
      } else {
        const cardImg = this.querySelector('img');
        if (cardImg && cardImg.src) {
          modalImage.src = cardImg.src;
          modalImage.alt = cardImg.alt || name;
        } else {
          modalImage.src = placeholder;
          modalImage.alt = 'placeholder';
        }
      }

      qtyInput.value = 1;
      qtyHidden.value = 1;
      updateSubtotal();

      // set action (adjust if your route differs)
      if(modalAddForm) modalAddForm.action = '/cart/add/' + id;

      bsModal.show();
    });
  });

  // qty controls
  if (qtyInc) {
    qtyInc.addEventListener('click', function(e){
      e.stopPropagation();
      const curr = sanitizeNumber(qtyInput.value, 1);
      if (current.stock && curr < current.stock) {
        qtyInput.value = curr + 1;
        updateSubtotal();
      }
    });
  }
  if (qtyDec) {
    qtyDec.addEventListener('click', function(e){
      e.stopPropagation();
      const curr = sanitizeNumber(qtyInput.value, 1);
      if (curr > 1) {
        qtyInput.value = curr - 1;
        updateSubtotal();
      }
    });
  }
  if (qtyInput) {
    qtyInput.addEventListener('input', function(e){
      let v = sanitizeNumber(this.value, 1);
      if (v < 1) v = 1;
      if (current.stock && v > current.stock) v = current.stock;
      this.value = v;
      updateSubtotal();
    });
  }

  if (modalAddForm) {
    modalAddForm.addEventListener('submit', function(e){
      const q = sanitizeNumber(qtyHidden.value, 1);
      if (current.stock && q > current.stock) {
        e.preventDefault();
        alert('Jumlah melebihi stok tersedia.');
        return false;
      }
      // allow default submit to server (CSRF token present)
    });
    modalAddForm.addEventListener('click', function(e){ e.stopPropagation(); });
  }
});
</script>
