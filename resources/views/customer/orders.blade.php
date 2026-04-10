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
        $totalCount = $counts['all'] ?? 0;
        $tab = request('tab', 'all');
        $baseQuery = request()->except('tab','page');
    @endphp

    <style>
    :root { --tp-muted: #6b7280; }
    /* Tabs / pills */
    .orders-tabs { display:flex; gap:.5rem; flex-wrap:wrap; margin-bottom:1rem; }
    .orders-tabs a { padding:.45rem .7rem; border-radius:999px; font-size:.88rem; display:inline-flex; align-items:center; gap:.5rem; text-decoration:none; }
    .orders-tabs .active { background:var(--brand-light); border:1px solid rgba(var(--brand-rgb),0.18); color:var(--brand); box-shadow:0 2px 8px rgba(var(--brand-rgb),0.06); }
    .orders-tabs .inactive { background:transparent; border:1px solid rgba(0,0,0,0.06); color:#374151; }
    </style>

    <div class="orders-tabs">
        @php
            $tabChoices = [
                'all' => 'Semua',
                'waiting' => 'Menunggu',
                'shipping' => 'Dalam Pengiriman',
                'done' => 'Selesai',
                'cancelled' => 'Dibatalkan'
            ];
        @endphp
        @foreach($tabChoices as $k => $label)
            <a href="{{ route('orders.index', array_merge($baseQuery, ['tab'=>$k])) }}" class="{{ $tab === $k ? 'active' : 'inactive' }}">
                <span>{{ $label }}</span>
                <span class="badge bg-white text-dark ms-1" style="font-size:.75rem;">{{ $counts[$k] ?? 0 }}</span>
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
    {{-- render orders (controller-filtered paginator) --}}
    @foreach($orders as $order)
        @php $firstItem = $order->items->first(); @endphp
        <div class="card mb-3 border-0 shadow-sm rounded-4 bg-white overflow-hidden">
            <div class="card-body p-3">
                <div class="row align-items-center">
                    {{-- 1. Left: Order Info (Slightly narrower) --}}
                    <div class="col-md-2">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="badge bg-primary-subtle text-primary fw-bold" style="font-size: 0.65rem; border-radius: 4px;">ONI</span>
                            <span class="fw-bold text-dark" style="font-size: 0.95rem;">{{ $order->order_number }}</span>
                        </div>
                        <div class="text-muted small mb-2" style="font-size: 0.8rem;">{{ \Carbon\Carbon::parse($order->created_at)->format('d M Y, H:i') }}</div>
                        
                        <div class="d-flex flex-wrap gap-2">
                            @php
                                $statusColors = [
                                    'pending' => ['bg' => '#f8f9fa', 'text' => '#666'],
                                    'processing' => ['bg' => '#eef2ff', 'text' => '#4f46e5'],
                                    'shipped' => ['bg' => '#e0f2fe', 'text' => '#0369a1'],
                                    'delivered' => ['bg' => '#fef2f2', 'text' => '#ef4444'],
                                    'cancelled' => ['bg' => '#f3f4f6', 'text' => '#9ca3af'],
                                ];
                                $s = $statusColors[$order->status] ?? ['bg' => '#f8f9fa', 'text' => '#666'];
                            @endphp
                            <span class="badge rounded-pill px-3 py-1.5 fw-medium" style="font-size: 0.68rem; border: 1px solid rgba(0,0,0,0.03); background-color: {{ $s['bg'] }}; color: {{ $s['text'] }};">
                                {{ $statusLabels[$order->status] ?? ucfirst($order->status) }}
                            </span>
                            <span class="badge rounded-pill px-3 py-1.5 fw-medium bg-light text-muted" style="font-size: 0.68rem; border: 1px solid rgba(0,0,0,0.03);">
                                {{ $payLabels[$order->payment_status] ?? ucfirst($order->payment_status) }}
                            </span>
                        </div>
                    </div>

                    {{-- 2. Middle: Items Info (Wider) --}}
                    <div class="col-md-7 border-start" style="border-left: 2px solid #f8f9fa !important;">
                        <div class="ms-md-4 d-flex align-items-center gap-3">
                            <div class="product-img-box border rounded-4 overflow-hidden flex-shrink-0" style="width: 64px; height: 64px; background: #fcfcfc;">
                                @if($firstItem && isset($firstItem->product) && $firstItem->product->image && \Storage::disk('public')->exists($firstItem->product->image))
                                    <img src="{{ \Storage::url($firstItem->product->image) }}" class="w-100 h-100" style="object-fit: cover;" alt="">
                                @else
                                    <div class="w-100 h-100 d-flex align-items-center justify-content-center fs-3">🍙</div>
                                @endif
                            </div>
                            <div class="product-text">
                                <h6 class="fw-bold mb-1 text-dark" style="font-size: 0.95rem;">{{ $firstItem->product->name ?? $firstItem->product_name ?? 'Produk' }}</h6>
                                <div class="text-muted small">
                                    {{ $firstItem->quantity ?? 1 }} item · Rp {{ number_format($firstItem->price ?? ($order->total),0,',','.') }}
                                </div>
                                @if($order->items->count() > 1)
                                    <div class="small mt-1 fw-bold" style="color: #ef4444; font-size: 0.8rem;">
                                        +{{ $order->items->count() - 1 }} item lain
                                    </div>
                                @endif
                            </div>
                        </div>
                    </div>

                    {{-- 3. Right: Total & Actions --}}
                    <div class="col-md-3 text-end">
                        <div class="mb-2">
                            <span class="text-muted small d-block mb-0" style="font-size: 0.75rem;">Total Pesanan</span>
                            <span class="fw-bold fs-5" style="color: #ef4444;">Rp {{ number_format($order->total, 0, ',', '.') }}</span>
                        </div>
                        <div class="d-flex gap-2 justify-content-end align-items-center">
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-sm px-3 rounded-3 text-muted fw-bold border bg-white" style="font-size: 0.8rem;">Detail</a>
                            @if($order->payment_status === 'pending' && !empty($order->midtrans_snap_token))
                                <a href="{{ route('checkout.success', $order) }}" class="btn btn-sm px-3 rounded-3 fw-bold shadow-sm" style="background-color: #ef4444; border-color: #ef4444; color: #fff; font-size: 0.8rem;">Bayar</a>
                            @endif
                        </div>
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
