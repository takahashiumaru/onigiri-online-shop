@extends('layouts.admin')
@section('title', 'Manajemen Pesanan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">🧾 Manajemen Pesanan</h5>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form action="{{ route('admin.orders.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <input type="text" name="search" class="form-control" placeholder="No. pesanan / nama..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="status" class="form-select">
                    <option value="">Semua Status</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                    <option value="processing" {{ request('status') == 'processing' ? 'selected' : '' }}>Processing</option>
                    <option value="shipped" {{ request('status') == 'shipped' ? 'selected' : '' }}>Shipped</option>
                    <option value="delivered" {{ request('status') == 'delivered' ? 'selected' : '' }}>Delivered</option>
                    <option value="cancelled" {{ request('status') == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                </select>
            </div>
            <div class="col-md-3">
                <select name="payment" class="form-select">
                    <option value="">Semua Pembayaran</option>
                    <option value="pending" {{ request('payment') == 'pending' ? 'selected' : '' }}>Menunggu</option>
                    <option value="paid" {{ request('payment') == 'paid' ? 'selected' : '' }}>Lunas</option>
                    <option value="failed" {{ request('payment') == 'failed' ? 'selected' : '' }}>Gagal</option>
                    <option value="expired" {{ request('payment') == 'expired' ? 'selected' : '' }}>Kedaluwarsa</option>
                </select>
            </div>
            <div class="col-md-3">
                <button type="submit" class="btn btn-primary w-100">Filter</button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>No. Pesanan</th>
                    <th>Pelanggan</th>
                    <th>Item</th>
                    <th>Total</th>
                    <th>Pembayaran</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td>
                        <a href="{{ route('admin.orders.show', $order) }}" class="fw-semibold text-decoration-none">
                            {{ $order->order_number }}
                        </a>
                        <div class="text-muted" style="font-size: 0.72rem;">{{ $order->created_at->format('d M Y H:i') }}</div>
                    </td>
                    <td>
                        <div class="fw-semibold small">{{ $order->user->name }}</div>
                        <small class="text-muted">{{ $order->user->email }}</small>
                    </td>
                    <td><span class="badge bg-light text-dark border">{{ $order->items->count() }} item</span></td>
                    <td class="fw-bold">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                    <td>
                        @php $payColors = ['paid'=>'success','pending'=>'warning','failed'=>'danger','expired'=>'secondary']; @endphp
                        <span class="badge bg-{{ $payColors[$order->payment_status] ?? 'secondary' }}">
                            {{ ucfirst($order->payment_status) }}
                        </span>
                    </td>
                    <td>
                        <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                            @csrf @method('PATCH')
                            <div class="d-flex gap-1">
                                <select name="status" class="form-select form-select-sm" style="min-width: 130px;" onchange="this.form.submit()">
                                    @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
                                    <option value="{{ $s }}" {{ $order->status == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </form>
                    </td>
                    <td>
                        <a href="{{ route('admin.orders.show', $order) }}" class="btn btn-sm btn-outline-primary">
                            <i class="bi bi-eye"></i>
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="text-center py-4 text-muted">Tidak ada pesanan</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($orders->hasPages())
    <div class="card-footer">
        {{ $orders->appends(request()->query())->links() }}
    </div>
    @endif
</div>
@endsection
