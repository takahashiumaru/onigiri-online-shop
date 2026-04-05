@extends('layouts.app')
@section('title', 'Dashboard Kurir')

@section('content')
<div class="bg-white border-bottom mb-4">
    <div class="container py-4">
        <div class="d-flex align-items-center gap-3">
            <div class="bg-brand-light p-3 rounded-4">
                <i class="bi bi-bicycle text-brand fs-3"></i>
            </div>
            <div>
                <h4 class="fw-800 mb-1">Area Kurir</h4>
                <p class="text-muted small mb-0">{{ auth()->user()->name }} • Antarkan kebahagiaan hari ini!</p>
            </div>
        </div>
    </div>
</div>

<div class="container pb-5">
    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show shadow-sm border-0 mb-4" role="alert" style="border-left: 4px solid #198754 !important;">
            <i class="bi bi-check-circle-fill me-2"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Status Tabs -->
    <div class="bg-white p-2 rounded-4 shadow-sm mb-4 border d-flex gap-2">
        <a href="{{ route('courier.dashboard', ['status' => 'shipped']) }}" 
           class="flex-fill text-center py-2 px-3 rounded-3 text-decoration-none fw-bold transition-all {{ $status == 'shipped' ? 'bg-brand text-white shadow' : 'text-muted hover-bg-light' }}"
           style="font-size: 0.9rem;">
           <i class="bi bi-truck me-2"></i> Perlu Dikirim
        </a>
        <a href="{{ route('courier.dashboard', ['status' => 'delivered']) }}" 
           class="flex-fill text-center py-2 px-3 rounded-3 text-decoration-none fw-bold transition-all {{ $status == 'delivered' ? 'bg-brand text-white shadow' : 'text-muted hover-bg-light' }}"
           style="font-size: 0.9rem;">
           <i class="bi bi-check-all me-2"></i> Telah Selesai
        </a>
    </div>

    <!-- Order List -->
    <div class="row g-3">
        @forelse($orders as $order)
        <div class="col-md-6 col-lg-4">
            <div class="card h-100 shadow-sm border-0 rounded-4 overflow-hidden">
                <div class="card-header bg-white border-bottom-0 pt-4 px-4 pb-0">
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <div class="text-muted small fw-600 text-uppercase mb-1" style="letter-spacing: 0.5px; font-size: 0.7rem;">ID PESANAN</div>
                            <h5 class="fw-800 text-dark mb-0">{{ $order->order_number }}</h5>
                        </div>
                        <div class="fw-800 text-brand fs-5">
                            Rp {{ number_format($order->total, 0, ',', '.') }}
                        </div>
                    </div>
                </div>
                
                <div class="card-body p-4">
                    <div class="p-3 bg-surface-secondary rounded-4 border border-light mb-4">
                        <div class="d-flex align-items-start mb-3">
                            <div class="bg-white p-2 rounded-circle shadow-sm me-3">
                                <i class="bi bi-person-fill text-muted"></i>
                            </div>
                            <div>
                                <div class="fw-bold text-dark fs-6">{{ $order->shipping_name }}</div>
                                <div class="text-muted small"><i class="bi bi-telephone me-1"></i> {{ $order->shipping_phone }}</div>
                            </div>
                        </div>
                        
                        <div class="d-flex align-items-start">
                            <div class="bg-white p-2 rounded-circle shadow-sm me-3">
                                <i class="bi bi-geo-alt-fill text-brand"></i>
                            </div>
                            <div class="small fw-500 text-dark-emphasis lh-sm">
                                {{ $order->shipping_address }}
                            </div>
                        </div>
                    </div>

                    <div class="d-grid">
                        <a href="{{ route('courier.orders.show', $order) }}" 
                           class="btn {{ $status == 'shipped' ? 'btn-brand' : 'btn-outline-dark' }} py-3 rounded-3 fw-bold d-flex align-items-center justify-content-center gap-2">
                            @if($status == 'shipped')
                                <i class="bi bi-camera-fill"></i> UPDATE PENGIRIMAN
                            @else
                                <i class="bi bi-eye"></i> LIHAT DETAIL
                            @endif
                        </a>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12 text-center py-5">
            <div class="bg-white d-inline-block p-4 rounded-circle shadow-sm mb-4">
                <i class="bi bi-box-seam text-muted fs-1"></i>
            </div>
            <h5 class="fw-bold text-dark">Tidak Ada Pesanan</h5>
            <p class="text-muted">Semua pesanan {{ $status == 'shipped' ? 'sudah terkirim' : 'masih dalam proses' }}. Istirahatlah sejenak!</p>
        </div>
        @endforelse
    </div>

    @if($orders->hasPages())
    <div class="mt-5 d-flex justify-content-center">
        {{ $orders->links() }}
    </div>
    @endif
</div>

<style>
    .bg-brand { background-color: var(--brand) !important; }
    .text-brand { color: var(--brand) !important; }
    .bg-brand-light { background-color: var(--brand-light) !important; }
    .btn-brand { 
        background-color: var(--brand); 
        border-color: var(--brand); 
        color: white;
    }
    .btn-brand:hover {
        background-color: var(--brand-600);
        border-color: var(--brand-600);
        color: white;
    }
    .transition-all { transition: all 0.2s ease-in-out; }
    .hover-bg-light:hover { background-color: rgba(0,0,0,0.03); }
    .fw-600 { font-weight: 600; }
</style>
@endsection
