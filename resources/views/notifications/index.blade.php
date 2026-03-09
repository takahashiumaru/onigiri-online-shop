@extends('layouts.app')

@section('title', 'Notifikasi')

@section('content')
<div class="container section">
    <div class="row justify-content-center">
        <div class="col-lg-10">
            <div class="card surface">
                <div class="card-header d-flex align-items-center justify-content-between">
                    <h5 class="mb-0">Notifikasi</h5>
                    <a href="{{ route('orders.index') }}" class="text-success">Lihat Semua Pesanan</a>
                </div>

                <div class="card-body">
                    @auth
                        @php
                            $orders = auth()->user()->orders()->latest()->limit(10)->get();
                            $counts = [
                                'waiting_payment' => auth()->user()->orders()->where('status','waiting_payment')->count(),
                                'waiting_confirmation' => auth()->user()->orders()->where('status','waiting_confirmation')->count(),
                                'processing' => auth()->user()->orders()->where('status','processing')->count(),
                                'shipping' => auth()->user()->orders()->where('status','shipping')->count(),
                                'delivered' => auth()->user()->orders()->where('status','delivered')->count(),
                            ];
                            // helper map untuk label & icon
                            $statusMap = [
                                'waiting_payment' => ['label'=>'Menunggu Pembayaran','icon'=>'bi-credit-card'],
                                'waiting_confirmation' => ['label'=>'Menunggu Konfirmasi','icon'=>'bi-clock'],
                                'processing' => ['label'=>'Pesanan Diproses','icon'=>'bi-arrow-repeat'],
                                'shipping' => ['label'=>'Sedang Dikirim','icon'=>'bi-truck'],
                                'delivered' => ['label'=>'Sampai Tujuan','icon'=>'bi-geo-alt'],
                            ];
                        @endphp

                        <!-- tabs simple: Transaksi / Update -->
                        <ul class="nav nav-tabs mb-3">
                            <li class="nav-item"><a class="nav-link active" href="#">Transaksi</a></li>
                            <li class="nav-item"><a class="nav-link" href="#">Update</a></li>
                        </ul>

                        <!-- status summary (ikon mirip screenshot) -->
                        <div class="mb-4">
                            <h6 class="fw-bold">Pembelian</h6>
                            <div class="d-flex gap-4 mt-3 flex-wrap">
                                @foreach($statusMap as $key => $meta)
                                    <a href="{{ route('orders.index') }}?status={{ $key }}" class="text-decoration-none text-dark">
                                        <div class="d-flex flex-column align-items-center" style="min-width:86px;">
                                            <div class="rounded-circle d-flex align-items-center justify-content-center" style="width:56px;height:56px;border:1px solid rgba(0,0,0,0.06);color:var(--brand)">
                                                <i class="{{ $meta['icon'] }} fs-4"></i>
                                            </div>
                                            <div class="mt-2 text-center small fw-semibold">{{ $meta['label'] }}</div>
                                            <div class="small text-muted">{{ $counts[$key] ?? 0 }}</div>
                                        </div>
                                    </a>
                                @endforeach
                            </div>
                        </div>

                        <hr>

                        <!-- recent orders list -->
                        <h6 class="fw-bold mb-3">Pesanan Terbaru</h6>
                        @if($orders->isEmpty())
                            <div class="text-center text-muted py-4">Belum ada pesanan terbaru.</div>
                        @else
                            <ul class="list-group">
                                @foreach($orders as $order)
                                    <li class="list-group-item d-flex justify-content-between align-items-center">
                                        <div>
                                            <div class="fw-semibold">#{{ $order->id }} — {{ $order->total_formatted ?? (isset($order->total) ? 'Rp '.number_format($order->total,0,',','.') : '') }}</div>
                                            <div class="small text-muted">
                                                {{ \Carbon\Carbon::parse($order->created_at)->diffForHumans() }} ·
                                                {{ $statusMap[$order->status]['label'] ?? ucfirst($order->status) }}
                                            </div>
                                        </div>
                                        <div class="text-end">
                                            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary btn-sm">Lihat</a>
                                            @if(!in_array($order->status, ['delivered','cancelled']))
                                                <div class="mt-1 small text-muted">Status: <span class="fw-semibold">{{ $statusMap[$order->status]['label'] ?? $order->status }}</span></div>
                                            @endif
                                        </div>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                    @else
                        <div class="text-center py-4">
                            <p class="mb-2">Silakan masuk untuk melihat notifikasi pesanan Anda.</p>
                            <a href="{{ route('login') }}" class="btn btn-primary btn-sm">Masuk</a>
                        </div>
                    @endauth
                </div>

                <div class="card-footer text-end">
                    <a href="{{ route('orders.index') }}" class="btn btn-outline-primary">Lihat Semua Pesanan</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
