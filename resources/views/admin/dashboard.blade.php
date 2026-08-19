@extends('layouts.admin')
@section('title', 'Dashboard')

@section('content')
<!-- Stats Grid -->
<div class="row g-3 mb-4">
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card h-100 d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted small mb-1">Total Produk</p>
                    <h3 class="fw-bold mb-0 counter-value" data-target="{{ $stats['total_products'] }}">0</h3>
                </div>
                <div class="stat-icon" style="background: #fff5f5; color: #E63946;">📦</div>
            </div>
            <div class="mt-2 small text-muted">
                <span class="text-danger">{{ $stats['out_of_stock'] }} habis</span> •
                <span class="text-warning">{{ $stats['low_stock'] }} menipis</span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card h-100 d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted small mb-1">Total Pesanan</p>
                    <h3 class="fw-bold mb-0 counter-value" data-target="{{ $stats['total_orders'] }}">0</h3>
                </div>
                <div class="stat-icon" style="background: #f0f9ff; color: #0ea5e9;">🧾</div>
            </div>
            <div class="mt-2 small">
                <span class="text-warning">{{ $stats['pending_payment'] }} menunggu lunas</span>
            </div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card h-100 d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted small mb-1">Pelanggan</p>
                    <h3 class="fw-bold mb-0 counter-value" data-target="{{ $stats['total_customers'] }}">0</h3>
                </div>
                <div class="stat-icon" style="background: #f0fdf4; color: #16a34a;">👥</div>
            </div>
            <div class="mt-2 small text-muted">Total terdaftar</div>
        </div>
    </div>
    <div class="col-sm-6 col-xl-3">
        <div class="stat-card h-100 d-flex flex-column justify-content-between">
            <div class="d-flex justify-content-between align-items-start">
                <div>
                    <p class="text-muted small mb-1">Total Pendapatan</p>
                    <h4 class="fw-bold mb-0">Rp <span class="counter-value" data-target="{{ $stats['total_revenue'] }}" data-type="currency">0</span></h4>
                </div>
                <div class="stat-icon" style="background: #fefce8; color: #ca8a04;">💰</div>
            </div>
            <div class="mt-2 small text-muted">Dari pesanan lunas</div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Recent Orders -->
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h6 class="fw-bold mb-0">Pesanan Terbaru</h6>
                <a href="{{ route('admin.orders.index') }}" class="btn btn-sm btn-outline-primary">Lihat Semua</a>
            </div>
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>No. Pesanan</th>
                            <th>Pelanggan</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Pembayaran</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recent_orders as $order)
                        <tr>
                            <td>
                                <a href="{{ route('admin.orders.show', $order) }}" class="fw-semibold text-decoration-none">
                                    {{ $order->order_number }}
                                </a>
                                <div class="text-muted" style="font-size: 0.75rem;">{{ $order->created_at->diffForHumans() }}</div>
                            </td>
                            <td>{{ $order->user->name }}</td>
                            <td class="fw-bold">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                            <td>
                                @php $statusColors = ['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger']; @endphp
                                <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">{{ ucfirst($order->status) }}</span>
                            </td>
                            <td>
                                @php $payColors = ['paid'=>'success','pending'=>'warning','failed'=>'danger','expired'=>'secondary']; @endphp
                                <span class="badge bg-{{ $payColors[$order->payment_status] ?? 'secondary' }}">
                                    {{ ucfirst($order->payment_status) }}
                                </span>
                            </td>
                        </tr>
                        @empty
                        <tr><td colspan="5" class="text-center text-muted py-4">Belum ada pesanan</td></tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Top Products + Quick Actions -->
    <div class="col-lg-4">
        <div class="card mb-3">
            <div class="card-header"><h6 class="fw-bold mb-0">Produk Terlaris</h6></div>
            <div class="card-body p-0">
                @forelse($top_products as $product)
                <div class="d-flex align-items-center p-3 border-bottom gap-3">
                    <span style="font-size: 1.8rem;">🍙</span>
                    <div class="flex-grow-1">
                        <div class="fw-semibold small">{{ $product->name }}</div>
                        <small class="text-muted">{{ $product->sales_count }} terjual</small>
                    </div>
                    <span class="badge {{ $product->stock > 0 ? 'bg-success' : 'bg-danger' }}">
                        Stok: {{ $product->stock }}
                    </span>
                </div>
                @empty
                <div class="p-3 text-muted text-center small">Belum ada data penjualan</div>
                @endforelse
            </div>
        </div>

        <div class="card">
            <div class="card-header"><h6 class="fw-bold mb-0">Aksi Cepat</h6></div>
            <div class="card-body d-flex flex-column gap-2">
                <a href="{{ route('admin.products.create') }}" class="btn btn-primary shadow-sm mb-1">
                    <i class="bi bi-plus-circle me-2"></i>Tambah Produk
                </a>
                <a href="{{ route('admin.orders.ready') }}" class="btn btn-outline-primary shadow-none">
                    <i class="bi bi-bicycle me-2"></i>Butuh Kurir ({{ $stats['needs_processing'] }})
                </a>
                <a href="{{ route('admin.products.index', ['stock' => 'low']) }}" class="btn btn-outline-danger shadow-none">
                    <i class="bi bi-exclamation-triangle me-2"></i>Stok Menipis ({{ $stats['low_stock'] }})
                </a>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    document.addEventListener('DOMContentLoaded', () => {
        const counters = document.querySelectorAll('.counter-value');
        const duration = 800; // Snappier duration in ms

        counters.forEach(counter => {
            const target = +counter.getAttribute('data-target');
            const isCurrency = counter.getAttribute('data-type') === 'currency';
            const startTime = performance.now();

            const updateCount = (currentTime) => {
                const elapsedTime = currentTime - startTime;
                const progress = Math.min(elapsedTime / duration, 1);
                
                // Easing function: easeOutExpo
                const easeProgress = progress === 1 ? 1 : 1 - Math.pow(2, -10 * progress);
                
                const currentValue = Math.floor(easeProgress * target);

                if (isCurrency) {
                    counter.innerText = new Intl.NumberFormat('id-ID').format(currentValue);
                } else {
                    counter.innerText = currentValue;
                }

                if (progress < 1) {
                    requestAnimationFrame(updateCount);
                } else {
                    if (isCurrency) {
                        counter.innerText = new Intl.NumberFormat('id-ID').format(target);
                    } else {
                        counter.innerText = target;
                    }
                }
            };

            requestAnimationFrame(updateCount);
        });
    });
</script>
@endsection
