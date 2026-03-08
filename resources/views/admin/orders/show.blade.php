@extends('layouts.admin')
@section('title', 'Detail Pesanan')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">📋 Detail Pesanan: {{ $order->order_number }}</h5>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row g-4">
    <!-- Order Items -->
    <div class="col-lg-8">
        <div class="card mb-4">
            <div class="card-header"><h6 class="fw-bold mb-0">Item Pesanan</h6></div>
            <div class="table-responsive">
                <table class="table align-middle mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>Produk</th>
                            <th>Harga</th>
                            <th>Qty</th>
                            <th>Subtotal</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($order->items as $item)
                        <tr>
                            <td>
                                <div class="d-flex align-items-center gap-2">
                                    <span style="font-size: 1.8rem;">🍙</span>
                                    <span class="fw-semibold">{{ $item->product_name }}</span>
                                </div>
                            </td>
                            <td>Rp {{ number_format($item->price, 0, ',', '.') }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td class="fw-bold">Rp {{ number_format($item->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot class="table-light">
                        <tr>
                            <td colspan="3" class="text-end fw-semibold">Subtotal</td>
                            <td class="fw-bold">Rp {{ number_format($order->subtotal, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end fw-semibold">Ongkos Kirim</td>
                            <td class="fw-bold">Rp {{ number_format($order->shipping_cost, 0, ',', '.') }}</td>
                        </tr>
                        <tr>
                            <td colspan="3" class="text-end fw-bold">Total</td>
                            <td class="fw-bold fs-5" style="color: #E63946;">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>

        <!-- Shipping Info -->
        <div class="card">
            <div class="card-header"><h6 class="fw-bold mb-0"><i class="bi bi-geo-alt me-2"></i>Informasi Pengiriman</h6></div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4">
                        <small class="text-muted">Nama</small>
                        <div class="fw-semibold">{{ $order->shipping_name }}</div>
                    </div>
                    <div class="col-md-4">
                        <small class="text-muted">No. HP</small>
                        <div class="fw-semibold">{{ $order->shipping_phone }}</div>
                    </div>
                    <div class="col-12 mt-3">
                        <small class="text-muted">Alamat</small>
                        <div class="fw-semibold">{{ $order->shipping_address }}</div>
                    </div>
                    @if($order->notes)
                    <div class="col-12 mt-3">
                        <small class="text-muted">Catatan</small>
                        <div>{{ $order->notes }}</div>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>

    <!-- Sidebar -->
    <div class="col-lg-4">
        <!-- Order Info -->
        <div class="card mb-3">
            <div class="card-header"><h6 class="fw-bold mb-0">Info Pesanan</h6></div>
            <div class="card-body">
                <div class="mb-2">
                    <small class="text-muted">Tanggal</small>
                    <div>{{ $order->created_at->format('d M Y, H:i') }}</div>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Pelanggan</small>
                    <div class="fw-semibold">{{ $order->user->name }}</div>
                    <small class="text-muted">{{ $order->user->email }}</small>
                </div>
                <div class="mb-2">
                    <small class="text-muted">Metode Bayar</small>
                    <div>{{ $order->payment_method ?? 'Belum dibayar' }}</div>
                </div>
                @if($order->midtrans_transaction_id)
                <div>
                    <small class="text-muted">ID Transaksi</small>
                    <div class="small font-monospace">{{ $order->midtrans_transaction_id }}</div>
                </div>
                @endif
            </div>
        </div>

        <!-- Status Update -->
        <div class="card">
            <div class="card-header"><h6 class="fw-bold mb-0">Update Status</h6></div>
            <div class="card-body">
                <div class="mb-3">
                    <small class="text-muted">Status Pesanan</small>
                    @php $statusColors = ['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger']; @endphp
                    <div><span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }} mt-1">{{ ucfirst($order->status) }}</span></div>
                </div>
                <div class="mb-3">
                    <small class="text-muted">Status Pembayaran</small>
                    @php $payColors = ['paid'=>'success','pending'=>'warning','failed'=>'danger','expired'=>'secondary']; @endphp
                    <div><span class="badge bg-{{ $payColors[$order->payment_status] ?? 'secondary' }} mt-1">{{ ucfirst($order->payment_status) }}</span></div>
                </div>

                <form action="{{ route('admin.orders.update-status', $order) }}" method="POST">
                    @csrf @method('PATCH')
                    <select name="status" class="form-select mb-2">
                        @foreach(['pending','processing','shipped','delivered','cancelled'] as $s)
                        <option value="{{ $s }}" {{ $order->status == $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                        @endforeach
                    </select>
                    <button type="submit" class="btn btn-primary w-100">Update Status</button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
