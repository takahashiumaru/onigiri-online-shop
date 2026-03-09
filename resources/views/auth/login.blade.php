@extends('layouts.app')

@section('title', 'Masuk — Onigiri Shop')

@section('content')
<div class="container section-lg">
    <div class="row justify-content-center">
        <div class="col-sm-10 col-md-7 col-lg-5 col-xl-4">
            <div class="text-center mb-4">
                <div style="font-size:3rem;">🍙</div>
                <h4 style="font-weight:800;">Masuk ke OnigiriShop</h4>
                <p class="text-muted small">Belanja onigiri jadi lebih mudah</p>
            </div>

            <div class="tp-card">
                <div class="tp-card-body" style="padding:28px;">
                    <form method="POST" action="{{ route('login') }}">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                                   value="{{ old('email') }}" required autofocus placeholder="nama@email.com">
                            @error('email') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-3">
                            <label class="form-label">Password</label>
                            <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                                   required placeholder="Masukkan password">
                            @error('password') <div class="invalid-feedback">{{ $message }}</div> @enderror
                        </div>
                        <div class="mb-4 form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember" style="font-size:.85rem;">Ingat saya</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 btn-lg">Masuk</button>
                    </form>
                </div>
            </div>

            <p class="text-center text-muted mt-3" style="font-size:.85rem;">
                Belum punya akun? <a href="{{ route('register') }}" style="color:var(--brand);font-weight:600;">Daftar</a>
            </p>
        </div>
    </div>
</div>
@endsection
