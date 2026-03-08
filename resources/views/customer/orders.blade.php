@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<div class="container py-5">
    <h3 class="fw-bold mb-4"><i class="bi bi-receipt me-2"></i>Pesanan Saya</h3>

    @if($orders->isEmpty())
    <div class="text-center py-5">
        <div style="font-size: 5rem;">📦</div>
        <h5 class="mt-3 text-muted">Belum ada pesanan</h5>
        <a href="{{ route('products') }}" class="btn btn-primary mt-3 px-4">Mulai Belanja</a>
    </div>
    @else
    @foreach($orders as $order)
    @php
        $statusColors = ['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger'];
        $statusLabels = ['pending'=>'Menunggu','processing'=>'Diproses','shipped'=>'Dikirim','delivered'=>'Terkirim','cancelled'=>'Dibatalkan'];
        $payColors    = ['paid'=>'success','pending'=>'warning','failed'=>'danger','expired'=>'secondary'];
        $payLabels    = ['paid'=>'Lunas','pending'=>'Belum Bayar','failed'=>'Gagal','expired'=>'Kedaluwarsa'];
    @endphp
    <div class="card mb-3">
        <div class="card-header d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div>
                <span class="fw-bold">{{ $order->order_number }}</span>
                <small class="text-muted ms-2">{{ $order->created_at->format('d M Y, H:i') }}</small>
            </div>
            <div class="d-flex gap-2">
                <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                    {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                </span>
                <span class="badge bg-{{ $payColors[$order->payment_status] ?? 'secondary' }}">
                    {{ $payLabels[$order->payment_status] ?? ucfirst($order->payment_status) }}
                </span>
            </div>
        </div>
        <div class="card-body">
            <div class="row align-items-center">
                <div class="col-md-8">
                    <div class="d-flex flex-wrap gap-3">
                        @foreach($order->items->take(3) as $item)
                        <div class="d-flex align-items-center gap-2">
                            <span style="font-size: 1.5rem;">🍙</span>
                            <div>
                                <div class="fw-semibold small">{{ $item->product_name }}</div>
                                <small class="text-muted">x{{ $item->quantity }}</small>
                            </div>
                        </div>
                        @endforeach
                        @if($order->items->count() > 3)
                        <span class="text-muted small align-self-center">+{{ $order->items->count() - 3 }} lagi</span>
                        @endif
                    </div>
                </div>
                <div class="col-md-4 text-md-end mt-2 mt-md-0">
                    <div class="fw-bold" style="color: #E63946; font-size: 1.1rem;">Rp {{ number_format($order->total, 0, ',', '.') }}</div>
                    <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary btn-sm mt-1">Detail</a>
                    @if($order->payment_status === 'pending' && $order->midtrans_snap_token)
                    <a href="{{ route('checkout.success', $order) }}" class="btn btn-primary btn-sm mt-1">Bayar</a>
                    @endif
                </div>
            </div>
        </div>
    </div>
    @endforeach

    {{ $orders->links() }}
    @endif
</div>
@endsection
