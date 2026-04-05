@extends('layouts.app')

@section('title', 'Edit Profil')

@section('content')
<div class="container section-lg">
    <div class="row justify-content-center">
        <div class="col-lg-8">
            {{-- Breadcrumb --}}
            <nav aria-label="breadcrumb" class="mb-3">
                <ol class="breadcrumb" style="font-size:.8rem;">
                    <li class="breadcrumb-item"><a href="{{ route('profile.show') }}">Profil</a></li>
                    <li class="breadcrumb-item active">Edit Profil</li>
                </ol>
            </nav>

            <form action="{{ route('profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                {{-- Avatar section --}}
                <div class="tp-card mb-3">
                    <div class="tp-card-header">
                        <h6 class="mb-0" style="font-weight:700;">Foto Profil</h6>
                    </div>
                    <div class="tp-card-body">
                        <div class="d-flex align-items-center gap-3">
                            @php $userPhoto = $user->avatar ?? $user->photo; @endphp
                            @if($userPhoto)
                                <img src="{{ asset('storage/'.$userPhoto) }}" alt="Avatar" id="avatarPreview"
                                     class="rounded-circle"
                                     style="width:72px;height:72px;object-fit:cover;border:3px solid var(--brand-light);">
                            @else
                                <div class="tp-avatar" id="avatarPreview" style="width:72px;height:72px;font-size:1.5rem;">
                                    {{ strtoupper(substr($user->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="flex-grow-1">
                                <input type="file" name="avatar" accept="image/*" class="form-control @error('avatar') is-invalid @enderror" id="avatarInput">
                                @error('avatar') <div class="invalid-feedback">{{ $message }}</div> @enderror
                                <small class="text-muted">Maks 2MB · JPG, PNG, GIF</small>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Personal info --}}
                <div class="tp-card mb-3">
                    <div class="tp-card-header">
                        <h6 class="mb-0" style="font-weight:700;">Informasi Pribadi</h6>
                    </div>
                    <div class="tp-card-body">
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label">Nama Lengkap <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror" value="{{ old('name', $user->name) }}" required>
                                @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control @error('email') is-invalid @enderror" value="{{ old('email', $user->email) }}" required>
                                @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">No. Handphone</label>
                                <div class="input-group">
                                    <span class="input-group-text" style="font-size:.85rem;">+62</span>
                                    <input type="text" name="phone" class="form-control @error('phone') is-invalid @enderror" value="{{ old('phone', $user->phone) }}" placeholder="812xxxxxxxx">
                                </div>
                                @error('phone') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">Alamat Lengkap</label>
                                <textarea name="address" rows="3" class="form-control @error('address') is-invalid @enderror" placeholder="Masukkan alamat lengkap...">{{ old('address', $user->address) }}</textarea>
                                @error('address') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Security --}}
                <div class="tp-card mb-3">
                    <div class="tp-card-header">
                        <h6 class="mb-0" style="font-weight:700;">Keamanan</h6>
                    </div>
                    <div class="tp-card-body">
                        <p class="text-muted small mb-3">Kosongkan jika tidak ingin mengubah password.</p>
                        <div class="row g-3">
                            <div class="col-sm-6">
                                <label class="form-label">Password Baru</label>
                                <input type="password" name="password" class="form-control @error('password') is-invalid @enderror" autocomplete="new-password" placeholder="Min 6 karakter">
                                @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                            </div>
                            <div class="col-sm-6">
                                <label class="form-label">Konfirmasi Password</label>
                                <input type="password" name="password_confirmation" class="form-control" autocomplete="new-password" placeholder="Ulangi password">
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Actions --}}
                <div class="d-flex gap-2 justify-content-end">
                    <a href="{{ route('profile.show') }}" class="btn btn-outline-secondary">Batal</a>
                    <button class="btn btn-primary" type="submit">
                        <i class="bi bi-check-lg me-1"></i> Simpan Perubahan
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script>
    // Live avatar preview
    document.getElementById('avatarInput')?.addEventListener('change', function(e) {
        const file = e.target.files[0];
        if (!file) return;
        const reader = new FileReader();
        reader.onload = function(ev) {
            const preview = document.getElementById('avatarPreview');
            if (preview.tagName === 'IMG') {
                preview.src = ev.target.result;
            } else {
                const img = document.createElement('img');
                img.src = ev.target.result;
                img.className = 'rounded-circle';
                img.style.cssText = 'width:72px;height:72px;object-fit:cover;border:3px solid var(--brand-light);';
                img.id = 'avatarPreview';
                preview.replaceWith(img);
            }
        };
        reader.readAsDataURL(file);
    });
</script>
@endsection
