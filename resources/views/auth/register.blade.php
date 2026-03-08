@extends('layouts.app')
@section('title', 'Daftar')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-6">
            <div class="text-center mb-4">
                <div style="font-size: 3rem;">🍙</div>
                <h3 class="fw-bold">Buat Akun Baru</h3>
                <p class="text-muted">Bergabung dan nikmati onigiri lezat kami!</p>
            </div>
            <div class="card">
                <div class="card-body p-4">
                    @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $error) <div>{{ $error }}</div> @endforeach
                    </div>
                    @endif
                    <form action="{{ route('register') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" name="name" class="form-control" value="{{ old('name') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">No. HP</label>
                            <input type="text" name="phone" class="form-control" value="{{ old('phone') }}" placeholder="08xxx" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Konfirmasi Password</label>
                            <input type="password" name="password_confirmation" class="form-control" required>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2">Daftar Sekarang</button>
                    </form>
                </div>
            </div>
            <p class="text-center mt-3 text-muted">
                Sudah punya akun? <a href="{{ route('login') }}" class="fw-bold" style="color: #E63946;">Masuk</a>
            </p>
        </div>
    </div>
</div>
@endsection
