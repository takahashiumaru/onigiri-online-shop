@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="container section">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card surface">
                <div class="card-header">
                    <h5 class="mb-0">Edit Profil</h5>
                </div>
                <div class="card-body">
                    <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- avatar preview -->
                        <div class="mb-3 d-flex align-items-center gap-3">
                            @if($user->avatar)
                                <img src="{{ asset('storage/'.$user->avatar) }}" alt="Avatar" class="rounded-circle" style="width:64px;height:64px;object-fit:cover;">
                            @else
                                <div class="avatar-circle" style="width:64px;height:64px;font-size:1.25rem;">
                                    {{ strtoupper(substr($user->name,0,1)) }}
                                </div>
                            @endif

                            <div class="flex-grow-1">
                                <label class="form-label mb-1">Ubah Foto Profil (opsional)</label>
                                <input type="file" name="avatar" accept="image/*" class="form-control @error('avatar') is-invalid @enderror">
                                @error('avatar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="text-muted">Max 2MB. JPG/PNG/GIF.</small>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Nama</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">No. Handphone</label>
                            <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}">
                            @error('phone') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Alamat</label>
                            <textarea name="address" rows="3" class="form-control @error('address') is-invalid @enderror">{{ old('address', $user->address) }}</textarea>
                            @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <hr>

                        <p class="small text-muted">Isi password hanya jika ingin mengganti kata sandi.</p>

                        <div class="mb-3">
                            <label class="form-label">Password baru</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Konfirmasi password</label>
                            <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password">
                        </div>

                        <div class="d-flex gap-2">
                            <button class="btn btn-primary" type="submit">Simpan</button>
                            <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">Batal</a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
