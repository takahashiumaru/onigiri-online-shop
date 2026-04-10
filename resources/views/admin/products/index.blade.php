@extends('layouts.admin')
@section('title', 'Manajemen Produk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">🍙 Manajemen Produk</h5>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Tambah Produk
    </a>
</div>

<!-- Filter -->
<div class="card mb-4">
    <div class="card-body py-3">
        <form action="{{ route('admin.products.index') }}" method="GET" class="row g-2 align-items-end">
            <div class="col-md-4">
                <input type="text" name="search" class="form-control" placeholder="Cari nama produk..." value="{{ request('search') }}">
            </div>
            <div class="col-md-3">
                <select name="category" class="form-select">
                    <option value="">Semua Kategori</option>
                    @foreach($categories as $cat)
                    <option value="{{ $cat }}" {{ request('category') == $cat ? 'selected' : '' }}>{{ ucfirst($cat) }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <select name="stock" class="form-select">
                    <option value="">Semua Stok</option>
                    <option value="low" {{ request('stock') == 'low' ? 'selected' : '' }}>Stok Menipis (≤5)</option>
                    <option value="out" {{ request('stock') == 'out' ? 'selected' : '' }}>Stok Habis</option>
                </select>
            </div>
            <div class="col-md-2">
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
                    <th>Produk</th>
                    <th>Kategori</th>
                    <th>Harga</th>
                    <th>Stok</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody>
@forelse($products as $product)
                <tr>
                    <td>
                        <div class="d-flex align-items-center gap-3">
                            @if($product->image && \Storage::disk('public')->exists($product->image))
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="rounded-3" style="width: 50px; height: 50px; object-fit: cover;">
                            @else
                                <div class="rounded-3 d-flex align-items-center justify-content-center" style="width: 50px; height: 50px; background: #fff5f5; font-size: 1.8rem;">🍙</div>
                            @endif
                            <div>
                                <div class="fw-semibold">{{ $product->name }}</div>
                                <small class="text-muted">{{ \Illuminate\Support\Str::limit($product->description, 40) }}</small>
                            </div>
                        </div>
                    </td>
                    <td><span class="badge bg-light text-dark border">{{ ucfirst($product->category) }}</span></td>
                    <td class="fw-bold">Rp {{ number_format($product->price, 0, ',', '.') }}</td>
                    <td>
                        <div class="d-flex align-items-center gap-2">
                            <span class="fw-bold stock-val-{{ $product->id }} {{ $product->stock == 0 ? 'text-danger' : ($product->stock <= 5 ? 'text-warning' : 'text-success') }}">
                                {{ $product->stock }}
                            </span>
                            <!-- Quick Stock Update Trigger -->
                            <button class="btn btn-xs btn-outline-secondary btn-sm px-1 py-0 btn-edit-stock"
                                    data-id="{{ $product->id }}"
                                    data-name="{{ $product->name }}"
                                    data-stock="{{ $product->stock }}">
                                <i class="bi bi-pencil" style="font-size: 0.7rem;"></i>
                            </button>
                        </div>
                    </td>
                    <td>
                        <span class="badge {{ $product->is_available ? 'bg-success' : 'bg-secondary' }}">
                            {{ $product->is_available ? 'Aktif' : 'Nonaktif' }}
                        </span>
                    </td>
                    <td>
                        <div class="d-flex gap-1">
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary shadow-none">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                  onsubmit="return confirm('Hapus produk {{ $product->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger shadow-none">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="text-center py-4 text-muted">Tidak ada produk ditemukan</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($products->hasPages())
    <div class="card-footer">
        {{ $products->appends(request()->query())->links() }}
    </div>
    @endif
</div>

<!-- SINGLE Stock Modal -->
<div class="modal fade" id="stockModal" tabindex="-1" aria-hidden="true" data-bs-backdrop="static">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content shadow border-0">
            <div class="modal-header">
                <h6 class="modal-title fw-bold">Update Stok</h6>
                <button type="button" class="btn-close shadow-none" data-bs-dismiss="modal"></button>
            </div>
            <form id="stockForm" method="POST">
                @csrf @method('PATCH')
                <div class="modal-body">
                    <h6 id="modalProdName" class="fw-bold mb-3 text-brand"></h6>
                    <p class="text-muted small mb-3">Stok saat ini: <strong id="modalCurrStock">0</strong></p>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Tindakan</label>
                        <select name="action" class="form-select form-select-sm shadow-none">
                            <option value="set">Set stok ke nilai baru</option>
                            <option value="add">Tambah stok</option>
                            <option value="subtract">Kurangi stok</option>
                        </select>
                    </div>
                    <div class="mb-0">
                        <label class="form-label fw-semibold small">Jumlah</label>
                        <input type="number" name="stock" class="form-control form-control-sm shadow-none" min="0" value="0" required>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" id="btnSaveStock" class="btn btn-primary btn-sm px-3">Update Stok</button>
                </div>
            </form>
        </div>
    </div>
</div>

@section('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const stockForm = document.getElementById('stockForm');
    const modalProdName = document.getElementById('modalProdName');
    const modalCurrStock = document.getElementById('modalCurrStock');
    const stockModalEl = document.getElementById('stockModal');
    const btnSaveStock = document.getElementById('btnSaveStock');
    let activeProductId = null;

    // Helper: Dynamic URL safely
    const getUpdateUrl = (id) => `{{ url('admin/products') }}/${id}/update-stock`;

    // Modal population
    document.querySelectorAll('.btn-edit-stock').forEach(btn => {
        btn.addEventListener('click', function() {
            activeProductId = this.dataset.id;
            modalProdName.textContent = this.dataset.name;
            modalCurrStock.textContent = this.dataset.stock;
            
            // USE setAttribute to avoid conflict with <select name="action">
            stockForm.setAttribute('action', getUpdateUrl(activeProductId));
            stockForm.querySelector('input[name="stock"]').value = 0;
            
            bootstrap.Modal.getOrCreateInstance(stockModalEl).show();
        });
    });

    // Handle form submit strictly
    stockForm.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        const originalText = btnSaveStock.innerHTML;
        const formData = new FormData(this);
        const requestUrl = stockForm.getAttribute('action'); // Consistent URL access

        // UI Feedback: Button Loading
        btnSaveStock.disabled = true;
        btnSaveStock.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Menyimpan...';

        try {
            const response = await fetch(requestUrl, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                }
            });

            // Check if JSON
            const contentType = response.headers.get('content-type');
            if (!contentType || !contentType.includes('application/json')) {
                throw new Error('Server returned non-JSON response. Check your server logs.');
            }

            const data = await response.json();

            if (response.ok && data.success) {
                // Update table row values
                const valSpan = document.querySelector(`.stock-val-${activeProductId}`);
                if (valSpan) {
                    valSpan.textContent = data.new_stock;
                    
                    // Update color state
                    valSpan.classList.remove('text-danger', 'text-warning', 'text-success');
                    if (data.new_stock == 0) valSpan.classList.add('text-danger');
                    else if (data.new_stock <= 5) valSpan.classList.add('text-warning');
                    else valSpan.classList.add('text-success');
                    
                    // Update trigger button data-stock for next modal open
                    const triggerBtn = document.querySelector(`.btn-edit-stock[data-id="${activeProductId}"]`);
                    if (triggerBtn) triggerBtn.dataset.stock = data.new_stock;
                }

                // Close modal
                bootstrap.Modal.getOrCreateInstance(stockModalEl).hide();
            } else {
                alert(data.message || 'Terjadi kesalahan saat mengupdate stok.');
            }
        } catch (error) {
            console.error('Update Stock Error:', error);
            alert('Kesalahan sistem: ' + error.message);
        } finally {
            // ALWAYS restore button state
            btnSaveStock.disabled = false;
            btnSaveStock.innerHTML = originalText;
        }
    });
});
</script>
@endsection
@endsection
