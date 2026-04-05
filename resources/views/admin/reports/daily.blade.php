@extends('layouts.admin')
@section('title', 'Laporan Harian')
@section('breadcrumb', 'Laporan')
@section('subbreadcrumb', 'Harian')

@section('content')
<div class="card mb-4 border-0 shadow-none bg-transparent" id="printable-area">
    <div class="card-body p-0">
        <!-- Compact Print Title -->
        <div class="d-none d-print-block text-center mb-4">
            <h4 class="fw-bold text-uppercase" style="letter-spacing: 1px;">Laporan Penjualan Harian</h4>
            <div class="text-muted small">Periode: {{ $date->translatedFormat('d F Y') }}</div>
            <hr class="my-3">
        </div>

        <form action="{{ route('admin.reports.daily') }}" method="GET" class="row g-3 align-items-end d-print-none mb-4">
            <div class="col-md-4">
                <label class="form-label fw-bold small">Pilih Tanggal</label>
                <input type="date" name="date" class="form-control form-control-sm" value="{{ $date->format('Y-m-d') }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-search me-2"></i>Filter
                </button>
            </div>
            <div class="col-md-6 text-md-end d-flex gap-2 justify-content-md-end mt-3 mt-md-0">
                <button type="button" onclick="exportReportToExcel()" class="btn btn-outline-success btn-sm">
                    <i class="bi bi-file-earmark-excel me-2"></i>Excel
                </button>
                <button type="button" onclick="window.print()" class="btn btn-outline-danger btn-sm">
                    <i class="bi bi-file-earmark-pdf me-2"></i>PDF
                </button>
            </div>
        </form>
    </div>
</div>

<!-- Stats -->
<div class="row g-2 mb-4">
    <div class="col-4">
        <div class="stat-card p-3 h-100 text-center border shadow-none bg-light-subtle">
            <div class="text-muted" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Total Pesanan</div>
            <div class="fw-bold h5 mb-0 text-primary">{{ $stats['total_orders'] }}</div>
        </div>
    </div>
    <div class="col-4">
        <div class="stat-card p-3 h-100 text-center border shadow-none bg-light-subtle">
            <div class="text-muted" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Total Pendapatan</div>
            <div class="fw-bold h5 mb-0 text-success">Rp {{ number_format($stats['total_revenue'], 0, ',', '.') }}</div>
        </div>
    </div>
    <div class="col-4">
        <div class="stat-card p-3 h-100 text-center border shadow-none bg-light-subtle">
            <div class="text-muted" style="font-size: 0.7rem; text-transform: uppercase; letter-spacing: 0.5px;">Onigiri Terjual</div>
            <div class="fw-bold h5 mb-0 text-warning">{{ $stats['total_items'] }} pc</div>
        </div>
    </div>
</div>

<div class="card border shadow-none">
    <div class="card-header bg-white py-3">
        <h6 class="fw-bold mb-0 small text-uppercase">Detail Penjualan: {{ $date->translatedFormat('d F Y') }}</h6>
    </div>
    <div class="table-responsive">
        <table class="table table-bordered table-sm align-middle mb-0" style="font-size: 0.85rem;">
            <thead class="table-light text-center">
                <tr>
                    <th style="width: 15%">No. Pesanan</th>
                    <th style="width: 20%">Pelanggan</th>
                    <th>Produk</th>
                    <th style="width: 20%">Total</th>
                    <th style="width: 10%">Waktu</th>
                </tr>
            </thead>
            <tbody>
                @forelse($orders as $order)
                <tr>
                    <td class="text-center"><span class="fw-bold text-primary">{{ $order->order_number }}</span></td>
                    <td>{{ $order->user->name }}</td>
                    <td>
                        <div class="small">
                            @foreach($order->items as $item)
                            <span>• {{ $item->product->name }} (x{{ $item->quantity }})</span>{{ !$loop->last ? ',' : '' }}
                            @endforeach
                        </div>
                    </td>
                    <td class="fw-bold text-end pe-3">Rp {{ number_format($order->total, 0, ',', '.') }}</td>
                    <td class="text-center">{{ $order->created_at->format('H:i') }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="text-center text-muted py-5">Tidak ada penjualan pada tanggal ini</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Simple Page Numbering for Print (Fix to Bottom Right) -->
<div class="d-none d-print-block print-footer">
    <p class="mb-0 small">Halaman 1 dari 1</p>
    </div>
</div>
@endsection

@section('scripts')
<script>
window.onbeforeprint = () => {
    window._originalTitle = document.title;
    document.title = "";
};
window.onafterprint = () => {
    document.title = window._originalTitle;
};

function exportReportToExcel() {
    const title = "LAPORAN PENJUALAN HARIAN";
    const dateStr = "{{ $date->translatedFormat('d F Y') }}";
    
    const totalOrders = "{{ $stats['total_orders'] }}";
    const totalRevenue = "{{ $stats['total_revenue'] }}";
    const totalItems = "{{ $stats['total_items'] }}";

    let csvContent = "data:text/csv;charset=utf-8,";
    csvContent += `"${title}"\r\n`;
    csvContent += `"TANGGAL:","${dateStr}"\r\n\r\n`;
    
    csvContent += `"RINGKASAN OPERASIONAL"\r\n`;
    csvContent += `"Total Pesanan:","${totalOrders}"\r\n`;
    csvContent += `"Total Pendapatan:","Rp ${new Intl.NumberFormat('id-ID').format(totalRevenue)}"\r\n`;
    csvContent += `"Onigiri Terjual:","${totalItems} Pcs"\r\n\r\n`;

    let table = document.querySelector(".table");
    let rows = Array.from(table.rows);

    rows.forEach(row => {
        let cols = Array.from(row.cells).map(cell => {
            let text = cell.innerText.replace(/\n/g, " | ").replace(/\t/g, " ").replace(/"/g, '""');
            return `"${text}"`;
        });
        csvContent += cols.join(",") + "\r\n";
    });

    let encodedUri = encodeURI(csvContent);
    let link = document.createElement("a");
    link.setAttribute("href", encodedUri);
    link.setAttribute("download", `LAPORAN_HARIAN_${dateStr.replace(/ /g, '_')}.csv`);
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
}
</script>
<style>
@media print {
    @page { 
        margin: 0; 
        size: auto;
    }
    html, body {
        height: auto !important;
        min-height: 0 !important;
        margin: 0 !important;
        padding: 0 !important;
        overflow: visible !important;
    }
    body { 
        background: white !important;
        padding: 1.5cm !important; /* Force padding on body */
    }
    #printable-area {
        display: block !important;
        width: 100% !important;
        margin: 0 !important;
        padding: 0 !important;
        position: relative !important;
    }
    /* Hide ALL UI elements */
    .sidebar, .topbar, .card-body form, .btn, .d-print-none, nav, footer, aside, .breadcrumb, .main-header, .navbar { 
        display: none !important; 
        height: 0 !important;
        visibility: hidden !important;
    }
    .main-content, .content-area, .container-fluid, .wrapper, #app { 
        margin: 0 !important; 
        padding: 0 !important; 
        display: block !important;
        position: static !important;
        min-height: 0 !important;
        height: auto !important;
    }
    .card { border: none !important; box-shadow: none !important; margin-bottom: 0 !important; background: transparent !important; }
    .stat-card { border: 1px solid #eee !important; background: #fff !important; }
    .table-responsive { overflow: visible !important; }
    table { width: 100% !important; border-collapse: collapse !important; border: 1px solid #eee !important; margin-bottom: 0 !important; }
    
    .print-footer {
        position: fixed;
        bottom: 1.5cm; /* Match body padding */
        right: 1.5cm;  /* Match body padding */
        text-align: right;
        display: block !important;
        z-index: 99999;
    }
}
</style>
@endsection
