@extends('layouts.app')

@section('title', 'Pembayaran')

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-7">
            <div class="card text-center">
                <div class="card-body p-5">
                    @if($order->payment_status === 'paid')
                        <div style="font-size: 5rem;">✅</div>
                        <h3 class="fw-bold mt-3 text-success">Pembayaran Berhasil!</h3>
                        <p class="text-muted">Pesanan <strong>{{ $order->order_number }}</strong> sedang diproses.</p>
                    @else
                        <div style="font-size: 5rem;">🍙</div>
                        <h3 class="fw-bold mt-3">Pesanan Dibuat!</h3>
                        <p class="text-muted mb-1">Nomor Pesanan: <strong>{{ $order->order_number }}</strong></p>
                        <p class="text-muted">Total: <strong style="color: #E63946;">Rp {{ number_format($order->total, 0, ',', '.') }}</strong></p>

                        <div class="alert alert-info my-4">
                            <i class="bi bi-info-circle me-2"></i>
                            Selesaikan pembayaran Anda melalui Midtrans. Klik tombol di bawah.
                        </div>

                        @if($snapToken)
                        <button id="pay-btn" class="btn btn-primary btn-lg px-5 py-3 fw-bold">
                            <i class="bi bi-credit-card me-2"></i>Bayar Sekarang
                        </button>
                        <p class="text-muted small mt-3">Pilih metode pembayaran: QRIS, Transfer Bank, GoPay, OVO, dll.</p>
                        @else
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle me-2"></i>
                            Token pembayaran tidak tersedia. Silakan hubungi admin.
                        </div>
                        @endif
                    @endif

                    <!-- Order Summary -->
                    <div class="card mt-4 text-start">
                        <div class="card-body p-3">
                            <h6 class="fw-semibold mb-2">Ringkasan Pesanan:</h6>
                            @foreach($order->items as $item)
                            <div class="d-flex justify-content-between small text-muted mb-1">
                                <span>{{ $item->product_name }} x{{ $item->quantity }}</span>
                                <span>Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                            @endforeach
                            <hr class="my-2">
                            <div class="d-flex justify-content-between fw-bold">
                                <span>Total</span>
                                <span style="color:#E63946;">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 justify-content-center mt-4">
                        <a href="{{ route('orders.index') }}" class="btn btn-outline-primary">
                            <i class="bi bi-receipt me-2"></i>Riwayat Pesanan
                        </a>
                        <a href="{{ route('products') }}" class="btn btn-outline-secondary">
                            <i class="bi bi-shop me-2"></i>Lanjut Belanja
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if($snapToken && $order->payment_status !== 'paid')
@php
    $midtransScript = config('midtrans.is_production')
        ? 'https://app.midtrans.com/snap/snap.js'
        : 'https://app.sandbox.midtrans.com/snap/snap.js';
    $midtransClientKey = config('midtrans.client_key');
@endphp
<script src="{{ $midtransScript }}" data-client-key="{{ $midtransClientKey }}"></script>
<script>
    var snapToken = '{{ $snapToken }}';
    var ordersUrl = '{{ route("orders.index") }}';
    var csrfToken = '{{ csrf_token() }}';
    var confirmUrl = '{{ route("payment.confirm") }}';
    var orderId = '{{ $order->id }}';
    var orderNumber = '{{ $order->order_number }}';

    function postConfirm(result) {
        // Sertakan credentials agar cookie/session dikirim (auth middleware diterima)
        return fetch(confirmUrl, {
            method: 'POST',
            credentials: 'same-origin',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': csrfToken,
                'Accept': 'application/json'
            },
            body: JSON.stringify({ order_id: orderId, order_number: orderNumber, result: result })
        });
    }

    function payNow() {
        snap.pay(snapToken, {
            onSuccess: function(result) {
                // update segera via client, lalu redirect
                postConfirm(result)
                    .then(function(response) {
                        // lanjutkan meskipun response bukan 200 (server-side notification adalah sumber kebenaran)
                        window.location.href = ordersUrl + '?payment=success';
                    })
                    .catch(function() {
                        // jika fetch gagal, tetap redirect supaya user melihat halaman pesanan
                        window.location.href = ordersUrl + '?payment=success';
                    });
            },
            onPending: function(result) {
                window.location.href = ordersUrl + '?payment=pending';
            },
            onError: function(result) {
                alert('Pembayaran gagal. Silakan coba lagi dari halaman pesanan.');
            },
            onClose: function() {
                // User closed popup, do nothing
            }
        });
    }

    document.getElementById('pay-btn').addEventListener('click', payNow);

    // Auto open after 800ms
    setTimeout(payNow, 800);
</script>
@endif
@endsection
