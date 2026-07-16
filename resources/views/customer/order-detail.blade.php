@extends('layouts.app')

@section('title', 'Detail Pesanan')

@section('content')
<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold mb-0"><i class="bi bi-receipt me-2"></i>Detail Pesanan</h3>
        <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-2"></i>Kembali
        </a>
    </div>

    <div class="row g-4">
        <div class="col-lg-8">
            <!-- Items -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between">
                    <h6 class="fw-bold mb-0">Item Pesanan</h6>
                    <span class="text-muted small">{{ $order->order_number }}</span>
                </div>
                <style>
                    /* order detail thumbnails */
                    .order-item-thumb { width:60px; height:60px; border-radius:8px; overflow:hidden; display:flex; align-items:center; justify-content:center; background:#fff5f5; font-size:1.6rem; flex-shrink:0; }
                    .order-item-thumb img { width:100%; height:100%; object-fit:cover; display:block; }
                </style>
                <div class="card-body p-0">
                    @foreach($order->items as $item)
                    <div class="d-flex align-items-center p-3 border-bottom gap-3">
                        <div class="order-item-thumb">
                            @if(isset($item->product) && $item->product->image && \Storage::disk('public')->exists($item->product->image))
                                <img src="{{ \Storage::url($item->product->image) }}" alt="{{ $item->product->name ?? $item->product_name }}">
                            @else
                                <!-- fallback emoji -->
                                🍙
                            @endif
                        </div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $item->product->name ?? $item->product_name }}</div>
                            <small class="text-muted">Rp {{ number_format($item->price, 0, ',', '.') }} x {{ $item->quantity }}</small>

                            {{-- show rating only when order status is "delivered" --}}
                            @if($order->status === 'delivered')
                                @php $canEdit = $order->created_at->greaterThanOrEqualTo(\Carbon\Carbon::now()->subDays(7)); @endphp
                                <div class="mt-2 d-flex align-items-center rating-wrapper" data-item-id="{{ $item->id }}" data-selected="{{ $item->rating ?? '' }}" data-can-edit="{{ $canEdit ? '1' : '0' }}">
                                    <div class="me-2 small text-muted">Nilai:</div>
                                    <div class="stars" style="display:flex; gap:6px; cursor:pointer;">
                                        @for($s=1;$s<=5;$s++)
                                            <span class="star {{ ($item->rating && $s <= $item->rating) ? 'star-selected' : '' }}" data-value="{{ $s }}" title="{{ $s }} dari 5" style="font-size:1.05rem; color: {{ ($item->rating && $s <= $item->rating) ? '#F59E0B' : '#E5E7EB' }};">
                                                ★
                                            </span>
                                        @endfor
                                    </div>
                                    {{-- tombol hanya tampil jika masih boleh edit (maks 1 minggu sejak order dibuat) --}}
                                    @if($canEdit)
                                        <button type="button" class="btn btn-sm btn-link ms-3 toggle-review" style="font-size:.85rem;">{{ $item->rating_review ? 'Edit Ulasan' : 'Tulis Ulasan' }}</button>
                                    @endif
                                </div>

                                <div class="mt-2 review-box" style="display:{{ $item->rating_review ? 'block' : 'none' }};">
                                    <textarea class="form-control form-control-sm rating-review-input" rows="2" placeholder="Tulis ulasan (opsional)">{{ $item->rating_review }}</textarea>
                                    <div class="mt-2 d-flex gap-2">
                                        <button class="btn btn-sm btn-primary save-rating">Simpan</button>
                                        <button class="btn btn-sm btn-outline-secondary cancel-rating">Batal</button>
                                        <div class="ms-2 text-success small saved-msg" style="display:none;">Tersimpan</div>
                                    </div>
                                </div>
                            @endif
                        </div>
                        <div class="fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</div>
                    </div>
                    @endforeach
                </div>
                <div class="card-footer">
                    <div class="d-flex justify-content-between mb-1">
                        <span class="text-muted">Subtotal</span>
                        <span>Rp {{ number_format($order->subtotal, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Ongkos Kirim</span>
                        <span>Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                    </div>
                    <div class="d-flex justify-content-between fw-bold">
                        <span>Total</span>
                        <span style="color: #E63946; font-size: 1.1rem;">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                    </div>
                </div>
            </div>

            <!-- Shipping -->
            <div class="card">
                <div class="card-header"><h6 class="fw-bold mb-0"><i class="bi bi-geo-alt me-2"></i>Alamat Pengiriman</h6></div>
                <div class="card-body">
                    <p class="fw-semibold mb-1">{{ $order->shipping_name }}</p>
                    <p class="text-muted mb-1"><i class="bi bi-phone me-1"></i>{{ $order->shipping_phone }}</p>
                    <p class="text-muted mb-0"><i class="bi bi-geo-alt me-1"></i>{{ $order->shipping_address }}</p>
                    @if($order->notes)
                    <div class="mt-2 p-2 bg-light rounded">
                        <small class="text-muted"><i class="bi bi-chat-left-text me-1"></i>{{ $order->notes }}</small>
                    </div>
                    @endif
                    @if($order->courier)
                    <div class="mt-3 pt-3 border-top">
                        <h6 class="fw-bold mb-3 small text-muted">Kurir Pengirim</h6>
                        <div class="d-flex align-items-center gap-3">
                            @if($order->courier->photo)
                                <img src="{{ asset('storage/' . $order->courier->photo) }}" class="rounded-circle object-fit-cover shadow-sm cursor-pointer" style="width: 50px; height: 50px;" alt="{{ $order->courier->name }}" data-bs-toggle="modal" data-bs-target="#imagePreviewModal" data-img-src="{{ asset('storage/' . $order->courier->photo) }}">
                            @else
                                <div class="avatar-circle" style="width: 50px; height: 50px; font-size: 1.2rem;">
                                    {{ strtoupper(substr($order->courier->name, 0, 1)) }}
                                </div>
                            @endif
                            <div>
                                <div class="fw-bold text-dark">{{ $order->courier->name }}</div>
                                <div class="text-muted small"><i class="bi bi-telephone me-1"></i>{{ $order->courier->phone ?? 'Kontak tidak tersedia' }}</div>
                            </div>
                        </div>
                    </div>
                    @endif
                    @if($order->proof_of_delivery)
                    <div class="mt-3 pt-3 border-top">
                        <h6 class="fw-bold mb-2 small text-muted">Bukti Pengiriman</h6>
                        <div class="position-relative cursor-pointer" data-bs-toggle="modal" data-bs-target="#imagePreviewModal" data-img-src="{{ Storage::url($order->proof_of_delivery) }}" style="max-width: 250px; border-radius: 12px; overflow: hidden;">
                            <img src="{{ Storage::url($order->proof_of_delivery) }}" alt="Bukti Pengiriman" class="img-fluid border" style="height: 150px; width: 100%; object-fit: cover;">
                            <div class="position-absolute bottom-0 start-0 w-100 p-1 text-center text-white bg-dark bg-opacity-50" style="font-size: 0.7rem;">
                                <i class="bi bi-fullscreen me-1"></i>Klik untuk perbesar
                            </div>
                        </div>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Image Preview Modal -->
        <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content bg-transparent border-0">
                    <div class="modal-body p-0 text-center position-relative">
                        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 1060;"></button>
                        <img src="" id="previewImageSource" class="img-fluid rounded shadow-lg" alt="Preview Image" style="max-height: 90vh;">
                    </div>
                </div>
            </div>
        </div>

        <div class="col-lg-4">
            <div class="card">
                <div class="card-header"><h6 class="fw-bold mb-0">Status Pesanan</h6></div>
                <div class="card-body">
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Status Pesanan</small>
                        @php
                            $statusColors = ['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger'];
                            $statusLabels = ['pending'=>'Menunggu','processing'=>'Diproses','shipped'=>'Dikirim','delivered'=>'Terkirim','cancelled'=>'Dibatalkan'];
                        @endphp
                        <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }} px-3 py-2">
                            {{ $statusLabels[$order->status] ?? $order->status }}
                        </span>
                    </div>
                    <div class="mb-3">
                        <small class="text-muted d-block mb-1">Status Pembayaran</small>
                        @php
                            $payColors  = ['paid'=>'success','pending'=>'warning','failed'=>'danger','expired'=>'secondary'];
                            $payLabels  = ['paid'=>'Lunas','pending'=>'Menunggu','failed'=>'Gagal','expired'=>'Kedaluwarsa'];
                        @endphp
                        <span class="badge bg-{{ $payColors[$order->payment_status] ?? 'secondary' }} px-3 py-2">
                            {{ $payLabels[$order->payment_status] ?? $order->payment_status }}
                        </span>
                    </div>
                    <hr>
                    <div class="mb-2">
                        <small class="text-muted">Tanggal Pesanan</small>
                        <div>{{ $order->created_at->format('d M Y, H:i') }}</div>
                    </div>
                    @if($order->payment_method)
                    <div class="mb-2">
                        <small class="text-muted">Metode Bayar</small>
                        <div>{{ strtoupper($order->payment_method) }}</div>
                    </div>
                    @endif
                </div>

                @if($order->payment_status === 'pending' && $order->status !== 'cancelled')
                <div class="card-footer">
                    <a href="{{ route('checkout.success', $order) }}" class="btn btn-primary w-100 mb-2">
                        <i class="bi bi-credit-card me-2"></i>Selesaikan Pembayaran
                    </a>
                </div>
                @endif

                @if(in_array($order->status, ['pending', 'processing']))
                <div class="card-footer border-top-0 pt-0">
                    <form action="{{ route('orders.cancel', $order) }}" method="POST" id="cancelOrderForm">
                        @csrf
                        @method('PATCH')
                        <button type="submit" class="btn btn-outline-danger w-100">
                            <i class="bi bi-x-circle me-2"></i>Batalkan Pesanan
                        </button>
                    </form>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
    <!-- SweetAlert2 for premium confirmation popups -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function () {
        // Image Preview Modal Logic
        const previewModal = document.getElementById('imagePreviewModal');
        if (previewModal) {
            previewModal.addEventListener('show.bs.modal', function (event) {
                const button = event.relatedTarget;
                const imgSrc = button.getAttribute('data-img-src');
                const modalImg = previewModal.querySelector('#previewImageSource');
                modalImg.src = imgSrc;
            });
        }

        // CSRF Token
        const token = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '';

        // -- Cancellation Handler --
        const cancelForm = document.getElementById('cancelOrderForm');
        if (cancelForm) {
            cancelForm.addEventListener('submit', function(e) {
                e.preventDefault();
                
                Swal.fire({
                    title: 'Batalkan Pesanan?',
                    text: "Pesanan yang dibatalkan tidak dapat dikembalikan, namun stok barang akan otomatis bertambah kembali.",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#ef4444', // brand red
                    cancelButtonColor: '#6b7280', // muted gray
                    confirmButtonText: 'Ya, Batalkan',
                    cancelButtonText: 'Kembali',
                    reverseButtons: true,
                    customClass: {
                        popup: 'rounded-4 border-0',
                        confirmButton: 'px-4 py-2 fw-bold',
                        cancelButton: 'px-4 py-2 fw-bold'
                    }
                }).then((result) => {
                    if (result.isConfirmed) {
                        this.submit(); // submit form manually
                    }
                });
            });
        }

        // -- Rating / Review Logic (Existing) --
        function sendRating(itemId, rating, review, btn) {
            const url = "{{ url('/order-items') }}/" + itemId + "/rating";
            if (btn) btn.disabled = true;
            fetch(url, {
                method: 'PUT',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': token,
                    'Accept': 'application/json'
                },
                body: JSON.stringify({ rating: rating, rating_review: review })
            }).then(async res => {
                if (btn) btn.disabled = false;
                if (!res.ok) {
                    const data = await res.json().catch(()=>({}));
                    alert(data.message || 'Gagal menyimpan rating');
                    return;
                }
                const data = await res.json().catch(()=>({}));
                // update stars UI and show saved message
                const wrapper = document.querySelector('.rating-wrapper[data-item-id="'+itemId+'"]');
                if (wrapper) {
                    // reflect saved rating
                    wrapper.dataset.selected = data.rating ?? wrapper.dataset.selected;
                    wrapper.querySelectorAll('.star').forEach(s => {
                        const val = parseInt(s.getAttribute('data-value'),10);
                        s.classList.toggle('star-selected', val <= (data.rating ?? 0));
                        s.style.color = s.classList.contains('star-selected') ? '#F59E0B' : '#E5E7EB';
                    });
                    const box = wrapper.parentElement.querySelector('.review-box');
                    if (box) {
                        const msg = box.querySelector('.saved-msg');
                        if (msg) {
                            msg.style.display = 'block';
                            setTimeout(()=>msg.style.display='none', 1600);
                        }
                    }
                }
            }).catch(err => {
                if (btn) btn.disabled = false;
                alert('Gagal terhubung ke server');
            });
        }

        // Initialize star UI and attach handlers that only change UI state
        document.querySelectorAll('.rating-wrapper').forEach(function(wrapper){
            const itemId = wrapper.getAttribute('data-item-id');
            const stars = Array.from(wrapper.querySelectorAll('.star'));
            const toggleBtn = wrapper.querySelector('.toggle-review');
            const reviewBox = wrapper.parentElement.querySelector('.review-box');
            const textarea = reviewBox ? reviewBox.querySelector('.rating-review-input') : null;
            const saveBtn = reviewBox ? reviewBox.querySelector('.save-rating') : null;
            const cancelBtn = reviewBox ? reviewBox.querySelector('.cancel-rating') : null;

            // read server-provided selected rating and can-edit flag
            const selectedVal = parseInt(wrapper.dataset.selected || 0, 10);
            const canEdit = wrapper.dataset.canEdit === '1';

            // apply selection according to selectedVal
            stars.forEach(s => {
                const val = parseInt(s.getAttribute('data-value'),10);
                const active = selectedVal && val <= selectedVal;
                s.classList.toggle('star-selected', active);
                s.style.color = active ? '#F59E0B' : '#E5E7EB';
                // set appropriate cursor if not editable
                s.style.cursor = canEdit ? 'pointer' : 'default';
            });

            // star click: only update UI selection if canEdit
            stars.forEach(function(st){
                st.addEventListener('click', function(){
                    if (!canEdit) {
                        // brief feedback
                        const el = document.createElement('div');
                        el.textContent = 'Waktu untuk mengedit ulasan telah berakhir.';
                        el.style.position = 'fixed';
                        el.style.bottom = '20px';
                        el.style.left = '50%';
                        el.style.transform = 'translateX(-50%)';
                        el.style.background = 'rgba(0,0,0,0.8)';
                        el.style.color = '#fff';
                        el.style.padding = '8px 12px';
                        el.style.borderRadius = '6px';
                        el.style.zIndex = 9999;
                        document.body.appendChild(el);
                        setTimeout(()=>el.remove(), 1800);
                        return;
                    }

                    const val = parseInt(this.getAttribute('data-value'),10);
                    // set dataset.selected on wrapper
                    wrapper.dataset.selected = val;
                    // visually update stars
                    stars.forEach(s => {
                        const v = parseInt(s.getAttribute('data-value'),10);
                        const selected = v <= val;
                        s.classList.toggle('star-selected', selected);
                        s.style.color = selected ? '#F59E0B' : '#E5E7EB';
                    });
                    // optionally open review box to prompt save
                    if (toggleBtn && reviewBox && reviewBox.style.display === 'none') {
                        reviewBox.style.display = 'block';
                    }
                });
            });

            // hide toggle button UI if not editable (some cases already not rendered)
            if (!canEdit && toggleBtn) toggleBtn.style.display = 'none';

            if (toggleBtn && reviewBox) {
                toggleBtn.addEventListener('click', function(){
                    if (!canEdit) return;
                    reviewBox.style.display = reviewBox.style.display === 'none' ? 'block' : 'none';
                });
            }

            // save: read wrapper.dataset.selected (must exist) and textarea, then send
            if (saveBtn) {
                saveBtn.addEventListener('click', function(){
                    if (!canEdit) {
                        alert('Waktu untuk mengedit ulasan telah berakhir.');
                        return;
                    }
                    const selected = parseInt(wrapper.dataset.selected || 0, 10);
                    if (!selected || selected < 1) {
                        alert('Pilih jumlah bintang terlebih dahulu sebelum menyimpan.');
                        return;
                    }
                    const reviewText = textarea ? textarea.value : '';
                    sendRating(itemId, selected, reviewText, saveBtn);
                });
            }

            if (cancelBtn) {
                cancelBtn.addEventListener('click', function(){
                    // revert UI to server state
                    location.reload();
                });
            }
        });
    });
    </script>

    <style>
    /* small helper so selected stars can be toggled with class */
    .star-selected { color: #F59E0B !important; }

    /* responsive tweaks for order detail */
    @media (max-width: 767.98px) {
        .order-item-thumb { width:48px; height:48px; font-size:1.2rem; }
        .stars { gap:4px; }
        .star { font-size:0.95rem !important; }
        .review-box { padding-top:8px; }
        .d-flex.align-items-center.rating-wrapper { flex-direction:row; gap:8px; flex-wrap:wrap; align-items:center; }
        .fw-semibold { font-size:0.95rem; }
    }
    @media (max-width: 479.98px) {
        .order-item-thumb { width:44px; height:44px; }
        .star { font-size:0.9rem !important; }
        .rating-wrapper .me-2 { display:none; } /* hide "Nilai:" label on very small screens */
    }
    </style>
@endsection
