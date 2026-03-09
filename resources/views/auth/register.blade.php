@extends('layouts.app')

@section('title', 'Daftar — Onigiri Shop')

@section('content')
<div class="container section-lg">
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-7 col-lg-5 col-xl-4">
            <div class="text-center mb-4">
                <div style="font-size:3rem;">🍙</div>
                <h4 style="font-weight:800;">Daftar Akun</h4>
                <p class="text-muted small">Buat akun untuk mulai belanja</p>
            </div>

            <div class="tp-card">
                <div class="tp-card-body" style="padding:28px;">
                    <form method="POST" action="{{ route('register') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                   value="{{ old('name') }}" required autofocus placeholder="Nama lengkap">
                            @error('name') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" required placeholder="nama@email.com">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                   required placeholder="Min 6 karakter">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-4">
                            <label class="form-label">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control"
                                   required placeholder="Ulangi password">
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-lg">Daftar</button>
                    </form>
                </div>
            </div>

            <p class="text-center text-muted mt-3" style="font-size:.85rem;">
                Sudah punya akun? <a href="{{ route('login') }}" style="color:var(--brand);font-weight:600;">Masuk</a>
            </p>
        </div>
    </div>
</div>
@endsection
