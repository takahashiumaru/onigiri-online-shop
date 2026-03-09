@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="container section">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card surface">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0">Profil Saya</h5>
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm">Edit Profil</a>
                </div>
                <div class="card-body d-flex gap-3 align-items-center">
                    @if($user->avatar)
                        <img src="{{ asset('storage/'.$user->avatar) }}" alt="Avatar" class="rounded-circle" style="width:96px;height:96px;object-fit:cover;">
                    @else
                        <div class="avatar-circle" style="width:96px;height:96px;font-size:2rem;">
                            {{ strtoupper(substr($user->name,0,1)) }}
                        </div>
                    @endif

                    <div>
                        <p class="mb-1"><strong>Nama:</strong> {{ $user->name }}</p>
                        <p class="mb-1"><strong>Email:</strong> {{ $user->email }}</p>
                        @if($user->phone)
                            <p class="mb-1"><strong>No. HP:</strong> {{ $user->phone }}</p>
                        @endif
                        @if($user->address)
                            <p class="mb-3"><strong>Alamat:</strong> <small class="text-muted">{{ nl2br(e($user->address)) }}</small></p>
                        @endif
                        <p class="text-muted small mb-0">Bergabung: {{ $user->created_at->format('d M Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
