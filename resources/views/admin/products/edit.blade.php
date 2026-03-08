@extends('layouts.admin')
@section('title', 'Edit Produk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">✏️ Edit Produk</h5>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <form action="{{ route('admin.products.update', $product) }}" method="POST" enctype="multipart/form-data">
                    @csrf @method('PUT')

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name', $product->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Harga (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                                    value="{{ old('price', $product->price) }}" min="1000" required>
                            </div>
                            @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Stok <span class="text-danger">*</span></label>
                            <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror"
                                value="{{ old('stock', $product->stock) }}" min="0" required>
                            @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <option value="classic" {{ old('category', $product->category) == 'classic' ? 'selected' : '' }}>Classic</option>
                                <option value="premium" {{ old('category', $product->category) == 'premium' ? 'selected' : '' }}>Premium</option>
                                <option value="fusion" {{ old('category', $product->category) == 'fusion' ? 'selected' : '' }}>Fusion</option>
                                <option value="vegetarian" {{ old('category', $product->category) == 'vegetarian' ? 'selected' : '' }}>Vegetarian</option>
                            </select>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_available" id="isAvailable" value="1"
                                    {{ old('is_available', $product->is_available) ? 'checked' : '' }}>
                                <label class="form-check-label" for="isAvailable">Produk Tersedia</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="4">{{ old('description', $product->description) }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Foto Produk</label>
                            @if($product->image)
                            <div class="mb-2">
                                <img src="{{ Storage::url($product->image) }}" alt="{{ $product->name }}" class="rounded-3" style="max-height: 120px;">
                                <small class="d-block text-muted mt-1">Foto saat ini. Upload baru untuk mengganti.</small>
                            </div>
                            @endif
                            <input type="file" name="image" class="form-control" accept="image/*" id="imageInput" onchange="previewImage(this)">
                            <div id="imagePreview" class="mt-2 d-none">
                                <img id="preview" src="" class="rounded-3" style="max-height: 200px;">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="bi bi-save me-2"></i>Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Quick Stock Update -->
    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h6 class="fw-bold mb-0">⚡ Update Stok Cepat</h6></div>
            <div class="card-body">
                <p class="text-muted small mb-3">Stok saat ini: <strong class="fs-5">{{ $product->stock }}</strong></p>
                <form action="{{ route('admin.products.update-stock', $product) }}" method="POST">
                    @csrf @method('PATCH')
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Tindakan</label>
                        <select name="action" class="form-select form-select-sm">
                            <option value="set">Set ke nilai baru</option>
                            <option value="add">Tambah stok</option>
                            <option value="subtract">Kurangi stok</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Jumlah</label>
                        <input type="number" name="stock" class="form-control form-control-sm" min="0" value="0" required>
                    </div>
                    <button type="submit" class="btn btn-warning btn-sm w-100">
                        <i class="bi bi-box me-2"></i>Update Stok
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    const img = document.getElementById('preview');
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = e => { img.src = e.target.result; preview.classList.remove('d-none'); };
        reader.readAsDataURL(input.files[0]);
    }
}
</script>
@endsection
