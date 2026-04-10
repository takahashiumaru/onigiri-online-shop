@extends('layouts.admin')
@section('title', 'Edit Kurir')
@section('subbreadcrumb', 'Edit Kurir')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0">Edit Akun Kurir</h5>
    <a href="{{ route('admin.couriers.index') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
</div>

<div class="card shadow-sm border-0" style="max-width: 600px;">
    <div class="card-body p-4">
        <form action="{{ route('admin.couriers.update', $courier) }}" method="POST" enctype="multipart/form-data">
            @csrf
            @method('PUT')
            
            <div class="mb-3">
                <label class="form-label text-muted fw-semibold small">Foto Profil</label>
                <div class="d-flex align-items-center gap-3 mb-2">
                    @if($courier->photo)
                        <img src="{{ asset('storage/' . $courier->photo) }}" class="rounded-circle object-fit-cover shadow-sm" style="width: 60px; height: 60px;" alt="{{ $courier->name }}">
                    @else
                        <div class="avatar-circle" style="width: 60px; height: 60px; font-size: 1.5rem;">
                            {{ strtoupper(substr($courier->name, 0, 1)) }}
                        </div>
                    @endif
                    <div class="flex-grow-1">
                        <input type="file" name="photo" class="form-control" accept="image/*">
                        <small class="text-muted">Ganti foto (JPG, PNG, maks 2MB).</small>
                    </div>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label text-muted fw-semibold small">Nama Lengkap</label>
                <input type="text" name="name" class="form-control" value="{{ old('name', $courier->name) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-muted fw-semibold small">Alamat Email</label>
                <input type="email" name="email" class="form-control" value="{{ old('email', $courier->email) }}" required>
            </div>

            <div class="mb-3">
                <label class="form-label text-muted fw-semibold small">Nomor Telepon</label>
                <input type="text" name="phone" class="form-control" value="{{ old('phone', $courier->phone) }}">
            </div>

            <div class="mb-3">
                <label class="form-label text-muted fw-semibold small">Alamat Lengkap</label>
                <textarea name="address" class="form-control" rows="3">{{ old('address', $courier->address) }}</textarea>
            </div>

            <hr class="my-4">
            <h6 class="fw-bold mb-3 small">Ubah Password (Kosongkan jika tidak ingin mengubah)</h6>

            <div class="mb-3">
                <label class="form-label text-muted fw-semibold small">Password Baru</label>
                <input type="password" name="password" class="form-control" minlength="8">
            </div>

            <div class="mb-4">
                <label class="form-label text-muted fw-semibold small">Konfirmasi Password Baru</label>
                <input type="password" name="password_confirmation" class="form-control" minlength="8">
            </div>

            <button type="submit" class="btn btn-primary w-100 fw-bold py-2">Perbarui Data Kurir</button>
        </form>
    </div>
</div>
@endsection
