@extends('layouts.app')
@section('title', 'Masuk')
@section('content')
<div class="container py-5">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="text-center mb-4">
                <div style="font-size: 3rem;">🍙</div>
                <h3 class="fw-bold">Masuk ke OnigiriShop</h3>
                <p class="text-muted">Pesan onigiri favoritmu sekarang!</p>
            </div>
            <div class="card">
                <div class="card-body p-4">
                    @if($errors->any())
                    <div class="alert alert-danger">
                        @foreach($errors->all() as $error) <div>{{ $error }}</div> @endforeach
                    </div>
                    @endif
                    <form action="{{ route('login') }}" method="POST">
                        @csrf
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control" value="{{ old('email') }}" required autofocus>
                        </div>
                        <div class="mb-3">
                            <label class="form-label fw-semibold">Password</label>
                            <input type="password" name="password" class="form-control" required>
                        </div>
                        <div class="mb-3 form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="remember">
                            <label class="form-check-label" for="remember">Ingat saya</label>
                        </div>
                        <button type="submit" class="btn btn-primary w-100 py-2">Masuk</button>
                    </form>
                </div>
            </div>
            <p class="text-center mt-3 text-muted">
                Belum punya akun? <a href="{{ route('register') }}" class="fw-bold" style="color: #E63946;">Daftar sekarang</a>
            </p>
        </div>
    </div>
</div>
@endsection
