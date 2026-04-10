@extends('layouts.admin')
@section('title', 'Tambah Kurir')
@section('subbreadcrumb', 'Tambah Kurir')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Tambah Akun Kurir</h5>
    <a href="{{ route('admin.couriers.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="card shadow-sm border-0" style="max-width: 600px;">
    <div class="card-body p-4">
        <form action="{{ route('admin.couriers.store') }}" method="POST" enctype="multipart/form-data">
            @csrf
            
            <div class="mb-3">
                <label class="form-label text-muted fw-semibold small">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-muted fw-semibold small">Alamat Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-muted fw-semibold small">Foto Profil (Opsional)</label>
                <input type="file" name="photo" class="form-control" accept="image/*">
                <small class="text-muted">Format: JPG, PNG. Maks 2MB.</small>
            </div>

            <div class="mb-3">
                <label class="form-label text-muted fw-semibold small">Nomor Telepon</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone') }}">
            </div>

            <div class="mb-3">
                <label class="form-label text-muted fw-semibold small">Alamat Lengkap</label>
                <textarea name="address" class="form-control" rows="3">{{ old('address') }}</textarea>
            </div>

            <div class="mb-3">
                <label class="form-label text-muted fw-semibold small">Password</label>
                <input type="password" name="password" class="form-control" required minlength="8">
            </div>

            <div class="mb-4">
                <label class="form-label text-muted fw-semibold small">Konfirmasi Password</label>
                <input type="password" name="password_confirmation" class="form-control" required minlength="8">
            </div>

            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Simpan Kurir</button>
        </form>
    </div>
</div>
@endsection
