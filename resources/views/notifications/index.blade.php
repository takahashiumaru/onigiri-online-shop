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
                            // helper map untuk label, icon & badge class
                            $statusMap = [
                                'waiting_payment' => ['label'=>'Menunggu Pembayaran','icon'=>'bi-credit-card','badge'=>'bg-warning text-dark'],
                                'waiting_confirmation' => ['label'=>'Menunggu Konfirmasi','icon'=>'bi-clock','badge'=>'bg-secondary'],
                                'processing' => ['label'=>'Pesanan Diproses','icon'=>'bi-arrow-repeat','badge'=>'bg-primary'],
                                'shipping' => ['label'=>'Sedang Dikirim','icon'=>'bi-truck','badge'=>'bg-info text-dark'],
                                'delivered' => ['label'=>'Sampai Tujuan','icon'=>'bi-geo-alt','badge'=>'bg-success'],
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
                                    @php
                                        // determine items count safely
                                        $itemsCount = $order->items_count ?? (isset($order->items) ? count($order->items) : null);
                                        $totalText = $order->total_formatted ?? (isset($order->total) ? 'Rp '.number_format($order->total,0,',','.') : '');
                                        $statusKey = $order->status;

                                        // CHANGED: ensure statusMeta always has icon, label and badge
                                        $defaultMeta = ['label'=>ucfirst($statusKey),'icon'=>'bi-bell','badge'=>'bg-light text-dark'];
                                        $statusMeta = array_merge($defaultMeta, $statusMap[$statusKey] ?? []);

                                        $createdExact = \Carbon\Carbon::parse($order->created_at)->format('d M Y H:i');
                                    @endphp

                                    <li class="list-group-item">
                                        <div class="d-flex align-items-start">
                                            <div class="me-3">
                                                <div class="rounded-circle d-flex align-items-center justify-content-center bg-light" style="width:44px;height:44px;">
                                                    <i class="{{ $statusMeta['icon'] }} fs-5" style="color:var(--brand)"></i>
                                                </div>
                                            </div>

                                            <div class="flex-grow-1">
                                                <div class="d-flex justify-content-between align-items-start">
                                                    <div>
                                                        <div class="fw-semibold">#{{ $order->id }} &middot; <span class="text-muted small">{{ $totalText }}</span></div>
                                                        <div class="small text-muted mt-1">
                                                            @if($itemsCount !== null)
                                                                {{ $itemsCount }} item ·
                                                            @endif
                                                            {{ $createdExact }} · {{ \Carbon\Carbon::parse($order->created_at)->diffForHumans() }}
                                                            @if(!empty($order->tracking_number))
                                                                · No. Resi: <span class="text-monospace">{{ $order->tracking_number }}</span>
                                                            @endif
                                                        </div>
                                                    </div>

                                                    <div class="text-end ms-3">
                                                        <span class="badge {{ $statusMeta['badge'] }} mb-2">{{ $statusMeta['label'] }}</span>
                                                        <div>
                                                            <a href="{{ route('orders.show', $order) }}" class="btn btn-outline-primary btn-sm">Lihat</a>

                                                            @if($statusKey === 'waiting_payment')
                                                                <a href="{{ route('orders.show', $order) }}?action=pay" class="btn btn-success btn-sm ms-1">Bayar Sekarang</a>
                                                            @elseif($statusKey === 'waiting_confirmation')
                                                                <a href="{{ route('orders.show', $order) }}?action=upload" class="btn btn-warning btn-sm ms-1">Unggah Bukti</a>
                                                            @elseif($statusKey === 'shipping')
                                                                <a href="{{ route('orders.show', $order) }}?action=track" class="btn btn-info btn-sm ms-1">Lacak</a>
                                                            @elseif($statusKey === 'delivered')
                                                                <a href="{{ route('orders.show', $order) }}?action=review" class="btn btn-outline-success btn-sm ms-1">Ulas</a>
                                                            @endif
                                                        </div>
                                                    </div>
                                                </div>

                                                @if(!in_array($order->status, ['delivered','cancelled']))
                                                    <div class="mt-2 small text-muted">Status: <span class="fw-semibold">{{ $statusMeta['label'] }}</span></div>
                                                @endif
                                            </div>
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
