@extends('layouts.app')

@section('title', 'Status Pesanan')

@section('page-styles')
    .success-card {
        border-radius: var(--radius-xl);
        overflow: hidden;
    }
    .success-header {
        background: #fff;
        padding: 40px 20px;
        text-align: center;
        border-bottom: 1px solid var(--border-light);
    }
    .success-icon-wrapper {
        width: 80px;
        height: 80px;
        background: var(--brand-50);
        border-radius: var(--radius-full);
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 20px;
        font-size: 3rem;
        color: var(--brand);
    }
    .success-icon-wrapper.is-cancelled {
        background: #f1f5f9;
        color: #64748b;
    }
    .order-summary-box {
        background: var(--surface-secondary);
        border-radius: var(--radius-lg);
        padding: 20px;
    }
    .payment-amount {
        font-size: 1.75rem;
        font-weight: 800;
        color: var(--brand);
    }
    .payment-amount.is-cancelled {
        text-decoration: line-through;
        color: var(--text-tertiary);
    }
    .item-row {
        padding: 10px 0;
        border-bottom: 1px solid var(--border-light);
    }
    .item-row:last-child {
        border-bottom: none;
    }
@endsection

@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-lg-6">
            <div class="card success-card shadow-sm border-0">
                <div class="success-header">
                    <div class="success-icon-wrapper {{ $order->status === 'cancelled' ? 'is-cancelled' : '' }}">
                        @if($order->payment_status === 'paid')
                            <i class="bi bi-check-circle-fill"></i>
                        @elseif($order->status === 'cancelled')
                            <i class="bi bi-x-circle-fill"></i>
                        @else
                            <i class="bi bi-clock-fill text-warning"></i>
                        @endif
                    </div>
                    
                    <h3 class="fw-bold mb-1">
                        @if($order->payment_status === 'paid')
                            Pembayaran Berhasil!
                        @elseif($order->status === 'cancelled')
                            Pesanan Dibatalkan
                        @else
                            Menunggu Pembayaran
                        @endif
                    </h3>
                    <p class="text-muted small">Nomor Pesanan: #{{ $order->order_number }}</p>
                </div>

                <div class="card-body p-4">
                    <div class="text-center mb-4">
                        <span class="text-muted small text-uppercase fw-bold">Total Tagihan</span>
                        <div class="payment-amount mt-1 {{ $order->status === 'cancelled' ? 'is-cancelled' : '' }}">
                            Rp {{ number_format($order->total, 0, ',', '.') }}
                        </div>
                    </div>

                    @if($order->status === 'cancelled')
                        <div class="alert alert-secondary d-flex align-items-center gap-3 mb-4">
                            <i class="bi bi-exclamation-circle fs-4"></i>
                            <div class="small">
                                Pesanan ini telah dibatalkan karena melewati batas waktu pembayaran.
                            </div>
                        </div>
                        <a href="{{ route('products') }}" class="btn btn-primary w-100 py-3 fw-bold mb-3">
                            <i class="bi bi-cart-plus me-2"></i>Belanja Lagi
                        </a>
                    @elseif($order->payment_status !== 'paid')
                        <div class="alert alert-info d-flex align-items-center gap-3 mb-4">
                            <i class="bi bi-info-circle fs-4"></i>
                            <div class="small">
                                Silakan selesaikan pembayaran Anda melalui Midtrans agar pesanan segera kami proses.
                            </div>
                        </div>

                        @if($snapToken)
                            <button id="pay-btn" class="btn btn-primary w-100 py-3 fw-bold mb-4">
                                <i class="bi bi-credit-card me-2"></i>Bayar Sekarang
                            </button>
                        @else
                            <form action="{{ route('checkout.regenerate', $order) }}" method="POST" class="mb-4">
                                @csrf
                                <button type="submit" class="btn btn-warning w-100 py-3 fw-bold">
                                    <i class="bi bi-arrow-clockwise me-2"></i>Generate Token Pembayaran
                                </button>
                            </form>
                        @endif
                    @endif

                    <div class="order-summary-box mb-4">
                        <h6 class="fw-bold mb-3 small text-uppercase">Ringkasan Pesanan</h6>
                        @foreach($order->items as $item)
                            <div class="item-row d-flex justify-content-between align-items-center">
                                <span class="small text-secondary">{{ $item->product_name }} x{{ $item->quantity }}</span>
                                <span class="small fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</span>
                            </div>
                        @endforeach
                        <div class="d-flex justify-content-between align-items-center mt-3 pt-2 border-top">
                            <span class="small text-muted">Ongkos Kirim</span>
                            <span class="small fw-bold">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</span>
                        </div>
                    </div>

                    <div class="row g-2">
                        <div class="col-6">
                            <a href="{{ route('orders.index') }}" class="btn btn-outline-secondary w-100">Riwayat</a>
                        </div>
                        <div class="col-6">
                            <a href="{{ route('home') }}" class="btn btn-outline-primary w-100">Beranda</a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
@if($snapToken && $order->payment_status !== 'paid' && $order->status !== 'cancelled')
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
        if (typeof snap === 'undefined') return;
        snap.pay(snapToken, {
            onSuccess: function(result) {
                postConfirm(result).then(function() {
                    window.location.href = ordersUrl + '?payment=success';
                });
            },
            onPending: function(result) {
                window.location.href = ordersUrl + '?payment=pending';
            },
            onError: function(result) {
                alert('Pembayaran gagal.');
            }
        });
    }

    const payBtn = document.getElementById('pay-btn');
    if (payBtn) {
        payBtn.addEventListener('click', payNow);
        setTimeout(payNow, 1000);
    }
</script>
@endif
@endsection
