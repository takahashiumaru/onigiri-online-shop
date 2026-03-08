@extends('layouts.admin')
@section('title', 'Tambah Produk')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">➕ Tambah Produk Baru</h5>
    <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-body p-4">
                <form action="{{ route('admin.products.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf

                    <div class="row g-3">
                        <div class="col-12">
                            <label class="form-label fw-semibold">Nama Produk <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                value="{{ old('name') }}" placeholder="Contoh: Onigiri Tuna Mayo" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Harga (Rp) <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">Rp</span>
                                <input type="number" name="price" class="form-control @error('price') is-invalid @enderror"
                                    value="{{ old('price') }}" placeholder="15000" min="1000" required>
                            </div>
                            @error('price') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Stok Awal <span class="text-danger">*</span></label>
                            <input type="number" name="stock" class="form-control @error('stock') is-invalid @enderror"
                                value="{{ old('stock', 0) }}" min="0" required>
                            @error('stock') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kategori <span class="text-danger">*</span></label>
                            <select name="category" class="form-select @error('category') is-invalid @enderror" required>
                                <option value="">Pilih Kategori</option>
                                <option value="classic" {{ old('category') == 'classic' ? 'selected' : '' }}>Classic</option>
                                <option value="premium" {{ old('category') == 'premium' ? 'selected' : '' }}>Premium</option>
                                <option value="fusion" {{ old('category') == 'fusion' ? 'selected' : '' }}>Fusion</option>
                                <option value="vegetarian" {{ old('category') == 'vegetarian' ? 'selected' : '' }}>Vegetarian</option>
                            </select>
                            @error('category') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Status</label>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" name="is_available" id="isAvailable" value="1"
                                    {{ old('is_available', true) ? 'checked' : '' }}>
                                <label class="form-check-label" for="isAvailable">Produk Tersedia</label>
                            </div>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="4"
                                placeholder="Deskripsi produk...">{{ old('description') }}</textarea>
                        </div>

                        <div class="col-12">
                            <label class="form-label fw-semibold">Foto Produk</label>
                            <input type="file" name="image" class="form-control @error('image') is-invalid @enderror"
                                accept="image/*" id="imageInput" onchange="previewImage(this)">
                            <small class="text-muted">Format: JPG, PNG, WebP. Maks 2MB.</small>
                            @error('image') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            <div id="imagePreview" class="mt-2 d-none">
                                <img id="preview" src="" alt="Preview" class="rounded-3" style="max-height: 200px; max-width: 100%;">
                            </div>
                        </div>
                    </div>

                    <div class="d-flex gap-2 mt-4">
                        <button type="submit" class="btn btn-primary px-5">
                            <i class="bi bi-save me-2"></i>Simpan Produk
                        </button>
                        <a href="{{ route('admin.products.index') }}" class="btn btn-outline-secondary">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="card">
            <div class="card-header"><h6 class="fw-bold mb-0">💡 Tips</h6></div>
            <div class="card-body">
                <ul class="list-unstyled small text-muted mb-0">
                    <li class="mb-2">✅ Gunakan nama yang jelas dan deskriptif</li>
                    <li class="mb-2">✅ Foto yang menarik meningkatkan penjualan</li>
                    <li class="mb-2">✅ Deskripsi detail membantu pelanggan memilih</li>
                    <li class="mb-2">✅ Update stok secara rutin</li>
                </ul>
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
