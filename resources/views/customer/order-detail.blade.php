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
                <div class="card-body p-0">
                    @foreach($order->items as $item)
                    <div class="d-flex align-items-center p-3 border-bottom gap-3">
                        <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px; background: #fff5f5; font-size: 2rem; flex-shrink: 0;">🍙</div>
                        <div class="flex-grow-1">
                            <div class="fw-semibold">{{ $item->product_name }}</div>
                            <small class="text-muted">Rp {{ number_format($item->price, 0, ',', '.') }} x {{ $item->quantity }}</small>
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

                @if($order->payment_status === 'pending' && $order->midtrans_snap_token)
                <div class="card-footer">
                    <a href="{{ route('checkout.success', $order) }}" class="btn btn-primary w-100">
                        <i class="bi bi-credit-card me-2"></i>Selesaikan Pembayaran
                    </a>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
