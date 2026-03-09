@extends('layouts.app')

@section('title', 'Pesanan Saya — Onigiri Shop')

@section('content')
<div class="container section-lg">
    <h4 style="font-weight:700;" class="mb-4">Pesanan Saya</h4>

    @if(isset($orders) && $orders->count())
        @foreach($orders as $order)
        <div class="tp-card mb-3">
            <div class="tp-card-header d-flex justify-content-between align-items-center flex-wrap gap-2">
                <div>
                    <span style="font-weight:700;font-size:.9rem;">{{ $order->order_number ?? '#'.$order->id }}</span>
                    <span class="text-muted ms-2" style="font-size:.8rem;">{{ $order->created_at->format('d M Y, H:i') }}</span>
                </div>
                @php
                    $statusMap = [
                        'waiting_payment' => ['Menunggu Pembayaran', 'status-waiting'],
                        'waiting_confirmation' => ['Dikonfirmasi', 'status-processing'],
                        'processing' => ['Diproses', 'status-processing'],
                        'shipping' => ['Dikirim', 'status-shipping'],
                        'completed' => ['Selesai', 'status-completed'],
                        'cancelled' => ['Dibatalkan', 'status-cancelled'],
                    ];
                    $s = $statusMap[$order->status] ?? ['Unknown', 'status-waiting'];
                @endphp
                <span class="status-badge {{ $s[1] }}">{{ $s[0] }}</span>
            </div>
            <div class="tp-card-body">
                @foreach($order->items as $item)
                <div class="d-flex align-items-center gap-3 {{ !$loop->last ? 'mb-3 pb-3 border-bottom' : '' }}" style="border-color:var(--border-light) !important;">
                    <img src="{{ $item->product && $item->product->image ? asset('storage/'.$item->product->image) : 'https://via.placeholder.com/56/f3f4f6/9ca3af?text=🍙' }}"
                         style="width:56px;height:56px;object-fit:cover;border-radius:var(--radius-sm);flex-shrink:0;">
                    <div class="flex-grow-1">
                        <div style="font-weight:600;font-size:.85rem;">{{ $item->product->name ?? 'Produk' }}</div>
                        <div class="text-muted" style="font-size:.78rem;">{{ $item->quantity }} x Rp {{ number_format($item->price, 0, ',', '.') }}</div>
                    </div>
                </div>
                @endforeach
            </div>
            <div class="tp-card-footer d-flex justify-content-between align-items-center">
                <div>
                    <span class="text-muted" style="font-size:.8rem;">Total:</span>
                    <span style="font-weight:800;color:var(--brand);">Rp {{ number_format($order->total_amount, 0, ',', '.') }}</span>
                </div>
                <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary btn-sm">
                    Detail <i class="bi bi-chevron-right ms-1"></i>
                </a>
            </div>
        </div>
        @endforeach

        @if(method_exists($orders, 'links'))
        <div class="d-flex justify-content-center mt-3">{{ $orders->links() }}</div>
        @endif
    @else
    <div class="empty-state">
        <i class="bi bi-bag"></i>
        <h5>Belum Ada Pesanan</h5>
        <p>Pesanan Anda akan muncul di sini setelah checkout.</p>
        <a href="{{ route('products') }}" class="btn btn-primary">Mulai Belanja</a>
    </div>
    @endif
</div>
@endsection
