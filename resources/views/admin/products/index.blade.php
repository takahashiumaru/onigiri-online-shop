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
                            <span class="fw-bold {{ $product->stock == 0 ? 'text-danger' : ($product->stock <= 5 ? 'text-warning' : 'text-success') }}">
                                {{ $product->stock }}
                            </span>
                            <!-- Quick Stock Update -->
                            <button class="btn btn-xs btn-outline-secondary btn-sm px-1 py-0"
                                    data-bs-toggle="modal"
                                    data-bs-target="#stockModal{{ $product->id }}">
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
                            <a href="{{ route('admin.products.edit', $product) }}" class="btn btn-sm btn-outline-primary">
                                <i class="bi bi-pencil"></i>
                            </a>
                            <form action="{{ route('admin.products.destroy', $product) }}" method="POST"
                                  onsubmit="return confirm('Hapus produk {{ $product->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>

                <!-- Stock Modal -->
                <div class="modal fade" id="stockModal{{ $product->id }}" tabindex="-1">
                    <div class="modal-dialog modal-sm">
                        <div class="modal-content">
                            <div class="modal-header">
                                <h6 class="modal-title fw-bold">Update Stok: {{ $product->name }}</h6>
                                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                            </div>
                            <form action="{{ route('admin.products.update-stock', $product) }}" method="POST">
                                @csrf @method('PATCH')
                                <div class="modal-body">
                                    <p class="text-muted small mb-3">Stok saat ini: <strong>{{ $product->stock }}</strong></p>
                                    <div class="mb-3">
                                        <label class="form-label fw-semibold small">Tindakan</label>
                                        <select name="action" class="form-select form-select-sm">
                                            <option value="set">Set stok ke nilai baru</option>
                                            <option value="add">Tambah stok</option>
                                            <option value="subtract">Kurangi stok</option>
                                        </select>
                                    </div>
                                    <div class="mb-0">
                                        <label class="form-label fw-semibold small">Jumlah</label>
                                        <input type="number" name="stock" class="form-control form-control-sm" min="0" value="0" required>
                                    </div>
                                </div>
                                <div class="modal-footer">
                                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                    <button type="submit" class="btn btn-primary btn-sm">Update Stok</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
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
@endsection
