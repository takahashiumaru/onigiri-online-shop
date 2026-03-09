@extends('layouts.app')

@section('title', 'Pesanan Saya')

@section('content')
<div class="container py-5">
    <h3 class="fw-bold mb-4"><i class="bi bi-receipt me-2"></i>Pesanan Saya</h3>

    <!-- Date filter: compact pill + dropdown with flatpickr + presets -->
    <form id="dateRangeForm" method="GET" action="{{ route('orders.index') }}" class="mb-3">
        <div class="d-flex gap-2 align-items-center">
            <div class="dropdown">
                <button class="btn btn-sm btn-outline-secondary dropdown-toggle d-flex align-items-center" type="button" id="dateDropdownBtn" data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="bi bi-calendar-event me-1"></i>
                    <span id="dateRangePill" style="min-width:140px;">
                        {{ request('from') && request('to') ? (request('from').' s/d '.request('to')) : 'Pilih Tanggal' }}
                    </span>
                </button>
                <div class="dropdown-menu p-3 shadow-sm" aria-labelledby="dateDropdownBtn" style="min-width:320px;">
                    <div class="mb-2">
                        <input id="fpInput" class="form-control form-control-sm" placeholder="Pilih rentang tanggal" readonly>
                        <input type="hidden" id="from" name="from" value="{{ request('from') }}">
                        <input type="hidden" id="to" name="to" value="{{ request('to') }}">
                    </div>
                    <div class="d-flex gap-2 mb-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary w-100" id="preset7">7 Hari</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary w-100" id="preset30">30 Hari</button>
                        <button type="button" class="btn btn-sm btn-outline-secondary w-100" id="preset90">90 Hari</button>
                    </div>
                    <div class="d-flex justify-content-end gap-2">
                        <button type="button" class="btn btn-sm btn-light" data-bs-dismiss="dropdown" id="cancelDate">Batal</button>
                        <button type="submit" class="btn btn-sm btn-primary" id="applyDate">Terapkan</button>
                    </div>
                </div>
            </div>
            <a href="{{ route('orders.index') }}" class="btn btn-sm btn-outline-secondary">Reset</a>
            <div class="text-muted small ms-auto d-none d-sm-block">Filter berdasarkan tanggal pembuatan pesanan</div>
        </div>
    </form>
    <!-- flatpickr CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">

    @php
        // Normalize incoming $orders into a collection for grouping (works with Paginator or Collection)
        if ($orders instanceof \Illuminate\Pagination\LengthAwarePaginator || $orders instanceof \Illuminate\Pagination\Paginator) {
            $ordersCollection = collect($orders->items());
        } else {
            $ordersCollection = collect($orders);
        }

        $waitingStatuses = ['pending','processing'];
        $waiting   = $ordersCollection->filter(fn($o) => in_array($o->status, $waitingStatuses));
        $inDelivery = $ordersCollection->filter(fn($o) => $o->status === 'shipped');
        $completed  = $ordersCollection->filter(fn($o) => $o->status === 'delivered');
        $cancelled  = $ordersCollection->filter(fn($o) => $o->status === 'cancelled');
        $totalCount = $ordersCollection->count();
    @endphp

    <!-- Improved segmented tabs (Tokopedia-like) -->
    @php $tab = request('tab', 'all'); $baseQuery = request()->except('tab','page'); @endphp
    <style>
    :root { --tp-green: #00b14f; --tp-green-soft: #e7f7ee; --tp-muted: #6b7280; }
    /* Tabs / pills */
    .orders-tabs { display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:1rem; }
    .orders-tabs a { padding:.45rem .7rem; border-radius:999px; font-size:.88rem; display:inline-flex; align-items:center; gap:.5rem; text-decoration:none; }
    .orders-tabs .active { background:var(--tp-green-soft); border:1px solid rgba(0,177,79,0.18); color:var(--tp-green); box-shadow:0 2px 8px rgba(0,177,79,0.06); }
    .orders-tabs .inactive { background:transparent; border:1px solid rgba(0,0,0,0.06); color:#374151; }

    /* Order card - compact, Tokopedia-like */
    .compact-order-card { display:flex; gap:1rem; align-items:center; padding:12px; border-radius:10px; border:1px solid rgba(0,0,0,0.04); background:#fff; }
    .compact-order-card:hover { box-shadow:0 8px 30px rgba(2,6,23,0.04); transform: translateY(-2px); }
    .compact-left { min-width:220px; max-width:36%; display:flex; flex-direction:column; gap:6px; }
    .order-num { font-weight:600; color:#111827; display:block; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:260px; }
    .order-meta { color:var(--tp-muted); font-size:.85rem; }
    .tp-badge { background:var(--tp-green-soft); color:var(--tp-green); border-radius:999px; padding:.25rem .5rem; font-size:.75rem; }
    .compact-items { flex:1; display:flex; gap:0.75rem; color:var(--tp-muted); overflow:hidden; align-items:center; }
    .product-thumb { width:56px; height:56px; border-radius:8px; background:#f6f6f6; display:flex; align-items:center; justify-content:center; font-size:1.35rem; overflow:hidden; }
    .product-thumb img { width:100%; height:100%; object-fit:cover; display:block; border-radius:8px; }
    .product-info { min-width:0; }
    .compact-right { min-width:150px; text-align:right; display:flex; flex-direction:column; gap:6px; align-items:flex-end; }
    .tp-accent { background:var(--tp-green); border-color:var(--tp-green); color:#fff; }
    .tp-accent-outline { color:var(--tp-green); border-color:var(--tp-green); background:transparent; }
    @media (max-width:767.98px) {
        .compact-order-card { flex-direction:column; align-items:flex-start; }
        .compact-left { max-width:100%; flex-direction:row; gap:12px; align-items:center; }
        .compact-right { width:100%; text-align:left; align-items:flex-start; }
    }
    </style>

    <div class="orders-tabs">
        @php
            $counts = [
                'all' => $orders->total() ?? $orders->count(),
                'waiting' => $orders->whereIn('status', ['pending','processing'])->count() ?? 0,
                'shipping' => $orders->where('status','shipped')->count() ?? 0,
                'done' => $orders->where('status','delivered')->count() ?? 0,
                'cancelled' => $orders->where('status','cancelled')->count() ?? 0,
            ];
            $tabs = ['all'=>'Semua','waiting'=>'Menunggu','shipping'=>'Dalam Pengiriman','done'=>'Selesai','cancelled'=>'Dibatalkan'];
        @endphp
        @foreach($tabs as $k=>$label)
            <a href="{{ route('orders.index', array_merge($baseQuery, ['tab'=>$k])) }}" class="{{ $tab === $k ? 'active' : 'inactive' }}">
                <span>{{ $label }}</span>
                <span class="badge bg-white text-dark ms-1" style="font-size:.75rem;">{{ $counts[$k] }}</span>
            </a>
        @endforeach
    </div>

    @php
        // rely on controller-provided $orders (already filtered by date/tab)
        $statusColors = ['pending'=>'warning','processing'=>'info','shipped'=>'primary','delivered'=>'success','cancelled'=>'danger'];
        $statusLabels = ['pending'=>'Menunggu','processing'=>'Diproses','shipped'=>'Dikirim','delivered'=>'Terkirim','cancelled'=>'Dibatalkan'];
        $payColors    = ['paid'=>'success','pending'=>'warning','failed'=>'danger','expired'=>'secondary'];
        $payLabels    = ['paid'=>'Lunas','pending'=>'Belum Bayar','failed'=>'Gagal','expired'=>'Kedaluwarsa'];
    @endphp

    @if($totalCount === 0)
    <div class="text-center py-5">
        <div style="font-size: 5rem;">📦</div>
        <h5 class="mt-3 text-muted">Belum ada pesanan</h5>
        <a href="{{ route('products') }}" class="btn btn-primary mt-3 px-4">Mulai Belanja</a>
    </div>
    @else

    <!-- Compact styles (Tokopedia-like tweaks) -->
    <style>
    .compact-order-card { display:flex; gap:1rem; align-items:center; padding:0.85rem; border-radius:12px; border:1px solid rgba(0,0,0,0.06); background:#fff; box-shadow:0 1px 4px rgba(22,28,37,0.03); transition:transform .08s, box-shadow .08s; }
    .compact-order-card:hover { transform:translateY(-2px); box-shadow:0 6px 18px rgba(22,28,37,0.06); }
    .compact-left { display:flex; gap:.75rem; align-items:center; min-width:0; }
    .compact-left .meta { min-width:0; }
    .status-dot { width:8px;height:8px;border-radius:50%;display:inline-block;margin-right:.45rem; }
    .compact-items { display:flex; gap:.75rem; color:#6b7280; overflow:hidden; white-space:nowrap; text-overflow:ellipsis; }
    .compact-right { margin-left:auto; text-align:right; display:flex; flex-direction:column; gap:.35rem; align-items:flex-end; min-width:140px; }
    .compact-right .actions { display:flex; gap:.5rem; }
    .compact-order-card .badge { font-size:.72rem; padding:.3rem .45rem; }
    @media (max-width:767.98px) {
        .compact-order-card { flex-direction:column; align-items:flex-start; gap:.6rem; }
        .compact-right { width:100%; align-items:flex-start; text-align:left; margin-left:0; }
    }
    </style>

    {{-- render orders (controller-filtered paginator) --}}
    @foreach($orders as $order)
        @php $firstItem = $order->items->first(); @endphp
        <div class="card mb-3 border-0">
            <div class="compact-order-card">
                <div class="compact-left">
                    <div class="d-flex align-items-center gap-2">
                        <div class="tp-badge me-1">{{ \Illuminate\Support\Str::upper(substr($order->order_number,0,3)) }}</div>
                        <div>
                            <div class="order-num">{{ $order->order_number }}</div>
                            <div class="order-meta">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}</div>
                        </div>
                    </div>
                    <div class="mt-2">
                        <span class="tp-badge">{{ $statusLabels[$order->status] ?? ucfirst($order->status) }}</span>
                        <span class="badge bg-light text-muted ms-1">{{ $payLabels[$order->payment_status] ?? ucfirst($order->payment_status) }}</span>
                    </div>
                </div>

                <div class="compact-items">
                    <div class="product-thumb">
                        @if($firstItem && isset($firstItem->product) && $firstItem->product->image && \Storage::disk('public')->exists($firstItem->product->image))
                            <img src="{{ \Storage::url($firstItem->product->image) }}" alt="{{ $firstItem->product_name ?? 'Produk' }}">
                        @else
                            🛍️
                        @endif
                    </div>
                    <div class="product-info">
                        <div style="font-weight:600; white-space:nowrap; overflow:hidden; text-overflow:ellipsis; max-width:260px;">
                            {{ $firstItem->product->name ?? $firstItem->product_name ?? 'Produk' }}
                        </div>
                        <div class="text-muted small">
                            {{ $firstItem->quantity ?? 1 }} item · Rp {{ number_format($firstItem->price ?? ($order->total),0,',','.') }}
                        </div>
                        @if($order->items->count() > 1)
                            <div class="text-muted small mt-1">+{{ $order->items->count() - 1 }} item lain</div>
                        @endif
                    </div>
                </div>

                <div class="compact-right">
                    <div class="text-muted small">Total</div>
                    <div class="fw-bold" style="color:var(--tp-green);">Rp {{ number_format($order->total,0,',','.') }}</div>
                    <div class="mt-1 d-flex gap-2">
                        <a href="{{ route('orders.show', $order) }}" class="btn btn-sm tp-accent-outline" style="border:1px solid var(--tp-green); border-radius:6px;">Detail</a>
                        @if($order->payment_status === 'pending' && !empty($order->midtrans_snap_token))
                            <a href="{{ route('checkout.success', $order) }}" class="btn btn-sm tp-accent">Bayar</a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    @endforeach

    <!-- Pagination (preserve filters and tab) -->
    <div class="mt-3">
        @if(method_exists($orders, 'links'))
            {{ $orders->appends(request()->except('page'))->links() }}
        @endif
    </div>

    @endif
</div>
@endsection

@section('scripts')
    <!-- flatpickr JS -->
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
    <script>
    document.addEventListener('DOMContentLoaded', function(){
        const fpInput = document.getElementById('fpInput');
        const pill = document.getElementById('dateRangePill');
        const inputFrom = document.getElementById('from');
        const inputTo = document.getElementById('to');
        // store original to support Cancel
        let originalFrom = inputFrom.value || '';
        let originalTo = inputTo.value || '';

        const fp = flatpickr(fpInput, {
            mode: "range",
            dateFormat: "Y-m-d",
            allowInput: false,
            onChange: function(selectedDates) {
                if (selectedDates.length === 2) {
                    const a = selectedDates[0].toISOString().slice(0,10);
                    const b = selectedDates[1].toISOString().slice(0,10);
                    inputFrom.value = a;
                    inputTo.value = b;
                    pill.textContent = a + ' s/d ' + b;
                } else if (selectedDates.length === 1) {
                    const a = selectedDates[0].toISOString().slice(0,10);
                    inputFrom.value = a;
                    inputTo.value = a;
                    pill.textContent = a;
                }
            },
            showMonths: 2,
        });

        function setRangeDays(days) {
            const end = new Date();
            const start = new Date();
            start.setDate(end.getDate() - (days - 1));
            const a = start.toISOString().slice(0,10);
            const b = end.toISOString().slice(0,10);
            inputFrom.value = a;
            inputTo.value = b;
            pill.textContent = a + ' s/d ' + b;
            fp.setDate([a, b], true);
        }

        document.getElementById('preset7').addEventListener('click', function(){ setRangeDays(7); });
        document.getElementById('preset30').addEventListener('click', function(){ setRangeDays(30); });
        document.getElementById('preset90').addEventListener('click', function(){ setRangeDays(90); });

        // open calendar when dropdown opens (Bootstrap triggers 'show.bs.dropdown')
        const dropdownEl = document.querySelector('#dateDropdownBtn')?.closest('.dropdown');
        if (dropdownEl) {
            dropdownEl.addEventListener('show.bs.dropdown', function () {
                // remember current values for cancel
                originalFrom = inputFrom.value || '';
                originalTo = inputTo.value || '';
                try {
                    if (originalFrom && originalTo) fp.setDate([originalFrom, originalTo], true);
                } catch(e){}
            });
        }

        // Cancel: revert to original values
        document.getElementById('cancelDate').addEventListener('click', function(){
            inputFrom.value = originalFrom;
            inputTo.value = originalTo;
            if (originalFrom && originalTo) pill.textContent = originalFrom + ' s/d ' + originalTo;
            else pill.textContent = 'Pilih Tanggal';
        });

        // initialize pill text from server values
        if (inputFrom.value && inputTo.value) {
            pill.textContent = inputFrom.value + ' s/d ' + inputTo.value;
            try { fp.setDate([inputFrom.value, inputTo.value], true); } catch(e){}
        }
    });
    </script>
@endsection
