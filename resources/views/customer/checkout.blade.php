@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container py-5">
    <h3 class="fw-bold mb-4"><i class="bi bi-credit-card me-2"></i>Checkout</h3>

    <form id="checkout-form" action="{{ route('checkout.process') }}" method="POST">
        @csrf
        <div class="row g-4">
            <!-- Shipping Info -->
            <div class="col-lg-7">
                <div class="card mb-4">
                    <div class="card-header"><h6 class="fw-bold mb-0"><i class="bi bi-geo-alt me-2"></i>Informasi Pengiriman</h6></div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">Nama Penerima</label>
                                <input type="text" name="shipping_name" class="form-control @error('shipping_name') is-invalid @enderror"
                                    value="{{ old('shipping_name', auth()->user()->name) }}" required>
                                @error('shipping_name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-semibold">No. HP</label>
                                <input type="text" name="shipping_phone" class="form-control @error('shipping_phone') is-invalid @enderror"
                                    value="{{ old('shipping_phone', auth()->user()->phone) }}" placeholder="08xxx" required>
                                @error('shipping_phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Alamat Lengkap</label>
                                <textarea name="shipping_address" class="form-control @error('shipping_address') is-invalid @enderror"
                                    rows="3" required placeholder="Jl. Contoh No. 123, Kelurahan, Kecamatan, Kota, Kode Pos">{{ old('shipping_address', auth()->user()->address) }}</textarea>
                                @error('shipping_address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-semibold">Catatan (opsional)</label>
                                <textarea name="notes" class="form-control" rows="2" placeholder="Catatan untuk kurir...">{{ old('notes') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card">
                    <div class="card-header"><h6 class="fw-bold mb-0"><i class="bi bi-credit-card me-2"></i>Metode Pembayaran</h6></div>
                    <div class="card-body">
                        <div class="d-flex align-items-center p-3 rounded-3 border" style="background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);">
                            <img src="https://upload.wikimedia.org/wikipedia/commons/thumb/a/a4/Midtrans.svg/200px-Midtrans.svg.png" alt="Midtrans" height="28" class="me-3">
                            <div>
                                <div class="fw-semibold">Midtrans Payment Gateway</div>
                                <small class="text-muted">QRIS, Transfer Bank, Kartu Kredit, Gopay, OVO, Dana, dan lainnya</small>
                            </div>
                            <i class="bi bi-check-circle-fill text-success ms-auto fs-5"></i>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-lg-5">
                <div class="card sticky-top" style="top: 80px;">
                    <div class="card-header"><h6 class="fw-bold mb-0">Ringkasan Pesanan</h6></div>
                    <div class="card-body p-0">
                        @foreach($cartItems as $item)
                        <div class="d-flex align-items-center p-3 border-bottom gap-3">
                            <div class="rounded-3 d-flex align-items-center justify-content-center flex-shrink-0" style="width: 50px; height: 50px; background: #fff5f5; font-size: 1.8rem;">🍙</div>
                            <div class="flex-grow-1">
                                <div class="fw-semibold small">{{ $item->product->name }}</div>
                                <small class="text-muted">x{{ $item->quantity }}</small>
                            </div>
                            <div class="fw-bold small text-nowrap">
                                Rp {{ number_format($item->quantity * $item->product->price, 0, ',', '.') }}
                            </div>
                        </div>
                        @endforeach

                        <div class="p-3">
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Subtotal</span>
                                <span>Rp {{ number_format($subtotal, 0, ',', '.') }}</span>
                            </div>
                            <div class="d-flex justify-content-between mb-2">
                                <span class="text-muted">Pengiriman</span>
                                <span>Rp {{ number_format($shippingCost, 0, ',', '.') }}</span>
                            </div>
                            <hr class="my-2">
                            <div class="d-flex justify-content-between">
                                <span class="fw-bold">Total Pembayaran</span>
                                <span class="fw-bold fs-5" style="color: #E63946;">Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <button id="checkout-button" type="submit" class="btn btn-primary w-100 py-2 fw-bold fs-6">
                            <i class="bi bi-lock me-2"></i>Bayar Sekarang
                        </button>
                        <p class="text-center text-muted small mt-2 mb-0">
                            <i class="bi bi-shield-check me-1"></i>Pembayaran aman & terenkripsi
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

<!-- Minimal JS to prevent double submit and show spinner -->
<script>
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('checkout-form');
    const btn  = document.getElementById('checkout-button');

    if (form && btn) {
        form.addEventListener('submit', function (e) {
            // prevent double submit UI-wise
            btn.disabled = true;
            btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2" role="status" aria-hidden="true"></span>Memproses...';
        });
    }
});
</script>
@endsection
