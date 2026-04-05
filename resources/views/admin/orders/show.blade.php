@extends('layouts.admin')
@section('title', 'Detail Pesanan')

@section('styles')
<style>
    .cursor-pointer { cursor: pointer; }
    #imagePreviewModal .modal-body img { max-width: 95vw; max-height: 90vh; }
</style>
@endsection

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">📋 Detail Pesanan: {{ $order->order_number }}</h5>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row g-4">
    <!-- Order Items -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header"><h6 class="fw-bold mb-0">Item Pesanan</h6></div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span style="font-size: 1.8rem;">🍙</span>
                                    <span class="fw-semibold">{{ $item->product_name }}</span>
                                </div>
                            </td>
                            <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td class="fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end fw-semibold">Subtotal</td>
                            <td class="fw-bold">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end fw-semibold">Ongkos Kirim</td>
                            <td class="fw-bold">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total</td>
                            <td class="fw-bold fs-5" style="color: #E63946;">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Shipping Info -->
        <div class="card">
            <div class="card-header"><h6 class="fw-bold mb-0"><i class="bi bi-geo-alt me-2"></i>Informasi Pengiriman</h6></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <small class="text-muted">Nama</small>
                        <div class="fw-semibold">{{ $order->shipping_name }}</div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">No. HP</small>
                        <div class="fw-semibold">{{ $order->shipping_phone }}</div>
                    </div>
                    <div class="col-12 mt-3">
                        <small class="text-muted">Alamat</small>
                        <div class="fw-semibold">{{ $order->shipping_address }}</div>
                    </div>
                    @if($order->notes)
                    <div class="col-12 mt-3">
                        <small class="text-muted">Catatan</small>
                        <div>{{ $order->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Order Info -->
        <div class="card mb-3">
            <div class="card-header"><h6 class="fw-bold mb-0">Info Pesanan</h6></div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">Tanggal</small>
                    <div>{{ $order->created_at->format('d M Y, H:i') }}</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Pelanggan</small>
                    <div class="fw-semibold">{{ $order->user->name }}</div>
                    <small class="text-muted">{{ $order->user->email }}</small>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Metode Bayar</small>
                    <div>{{ $order->payment_method ?? 'Belum dibayar' }}</div>
                </div>
                @if($order->midtrans_transaction_id)
                <div class="mb-2">
                    <small class="text-muted">ID Transaksi</small>
                    <div class="small font-monospace">{{ $order->midtrans_transaction_id }}</div>
                </div>
                @endif
                @if($order->courier)
                <div class="mb-2 mt-3 pt-2 border-top">
                    <small class="text-muted d-block mb-2">Kurir Pengirim</small>
                    <div class="d-flex align-items-center gap-3">
                        @if($order->courier->photo)
                            <img src="{{ asset('storage/' . $order->courier->photo) }}" class="rounded-circle object-fit-cover shadow-sm cursor-pointer" style="width: 45px; height: 45px;" alt="{{ $order->courier->name }}" data-bs-toggle="modal" data-bs-target="#imagePreviewModal" data-img-src="{{ asset('storage/' . $order->courier->photo) }}">
                        @else
                            <div class="avatar-circle" style="width: 45px; height: 45px; font-size: 1.1rem;">
                                {{ strtoupper(substr($order->courier->name, 0, 1)) }}
                            </div>
                        @endif
                        <div>
                            <div class="fw-bold text-dark">{{ $order->courier->name }}</div>
                            <div class="text-muted small">{{ $order->courier->phone ?? 'No HP tidak ada' }}</div>
                        </div>
                    </div>
                </div>
                @endif
            </div>
        </div>

        @if($order->proof_of_delivery)
        <div class="card mb-3 shadow-sm border-0" style="border-radius: 14px; overflow: hidden;">
            <div class="card-header bg-white py-3 border-0"><h6 class="fw-bold mb-0 text-primary"><i class="bi bi-camera me-2"></i>Bukti Pengiriman</h6></div>
            <div class="card-body p-0">
                <div class="position-relative cursor-pointer" data-bs-toggle="modal" data-bs-target="#imagePreviewModal" data-img-src="{{ Storage::url($order->proof_of_delivery) }}">
                    <img src="{{ Storage::url($order->proof_of_delivery) }}" alt="Bukti Pengiriman" class="w-100" style="height: 250px; object-fit: cover;">
                    <div class="position-absolute bottom-0 start-0 w-100 p-2 text-center text-white bg-dark bg-opacity-50">
                        <small><i class="bi bi-fullscreen me-1"></i>Klik untuk perbesar</small>
                    </div>
                </div>
            </div>
        </div>
        @endif

        <!-- Image Preview Modal -->
        <div class="modal fade" id="imagePreviewModal" tabindex="-1" aria-hidden="true">
            <div class="modal-dialog modal-dialog-centered modal-lg">
                <div class="modal-content bg-transparent border-0">
                    <div class="modal-body p-0 text-center position-relative">
                        <button type="button" class="btn-close btn-close-white position-absolute top-0 end-0 m-3" data-bs-dismiss="modal" aria-label="Close" style="z-index: 1060;"></button>
                        <img src="" id="previewImageSource" class="img-fluid rounded shadow-lg" alt="Preview Image">
                    </div>
                </div>
            </div>
        </div>

        <!-- Status Update -->
        <div class="card">
            <div class="card-header"><h6 class="fw-bold mb-0">Update Status</h6></div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Status Pesanan</small>
                    @php $statusColors = ['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger']; @endphp
                    <div><span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }} mt-1">{{ ucfirst($order->status) }}</span></div>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Status Pembayaran</small>
                    @php $payColors = ['paid'=>'success','pending'=>'warning','failed'=>'danger','expired'=>'secondary']; @endphp
                    <div><span class="badge bg-{{ $payColors[$order->payment_status] ?? 'secondary' }} mt-1">{{ ucfirst($order->payment_status) }}</span></div>
                </div>

                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                    @csrf @method('PATCH')
                    
                    <div class="mb-3">
                        <label class="form-label small text-muted">Ubah Status</label>
                        <select name="status" id="statusSelect" class="form-select mb-2">
                            @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
                            <option value="{{ $s }}" {{ $order->status == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3" id="courierSelectGroup" style="display: none;">
                        <label class="form-label small text-muted">Pilih Kurir</label>
                        <select name="courier_id" class="form-select">
                            <option value="">-- Pilih Kurir --</option>
                            @foreach($couriers as $courier)
                            <option value="{{ $courier->id }}" {{ $order->courier_id == $courier->id ? 'selected' : '' }}>{{ $courier->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <button type="submit" class="btn btn-primary w-100 fw-bold">Update Status</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Image Preview logic
    const previewModal = document.getElementById('imagePreviewModal');
    if (previewModal) {
        previewModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const imgSrc = button.getAttribute('data-img-src');
            const modalImg = previewModal.querySelector('#previewImageSource');
            modalImg.src = imgSrc;
        });
    }

    const statusSelect = document.getElementById('statusSelect');
    const courierGroup = document.getElementById('courierSelectGroup');

    function toggleCourierDisplay() {
        if (statusSelect.value === 'shipped') {
            courierGroup.style.display = 'block';
            courierGroup.querySelector('select').setAttribute('required', 'required');
        } else {
            courierGroup.style.display = 'none';
            courierGroup.querySelector('select').removeAttribute('required');
        }
    }

    statusSelect.addEventListener('change', toggleCourierDisplay);
    toggleCourierDisplay(); // Set initial state
});
</script>
@endsection
