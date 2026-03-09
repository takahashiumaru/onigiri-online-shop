@extends('layouts.app')

@section('title', 'Profil Saya')

@section('content')
<div class="container section-lg">
    <div class="row g-4">
        {{-- Sidebar --}}
        <div class="col-lg-4">
            <div class="tp-card text-center">
                <div class="tp-card-body" style="padding:32px 20px;">
                    @if($user->avatar)
                        <img src="{{ asset('storage/'.$user->avatar) }}" alt="Avatar"
                             class="rounded-circle mb-3"
                             style="width:100px;height:100px;object-fit:cover;border:3px solid var(--brand-light);">
                    @else
                        <div class="tp-avatar mx-auto mb-3" style="width:100px;height:100px;font-size:2.5rem;">
                            {{ strtoupper(substr($user->name,0,1)) }}
                        </div>
                    @endif
                    <h5 class="mb-1" style="font-weight:700;">{{ $user->name }}</h5>
                    <p class="text-muted small mb-3">Bergabung {{ $user->created_at->format('d M Y') }}</p>
                    <a href="{{ route('profile.edit') }}" class="btn btn-outline-primary btn-sm w-100">
                        <i class="bi bi-pencil-square me-1"></i> Edit Profil
                    </a>
                </div>
            </div>
        </div>

        {{-- Main info --}}
        <div class="col-lg-8">
            <div class="tp-card">
                <div class="tp-card-header">
                    <h6 class="mb-0" style="font-weight:700;">Informasi Pribadi</h6>
                </div>
                <div class="tp-card-body">
                    <div class="row g-3">
                        <div class="col-sm-6">
                            <label class="form-label">Nama Lengkap</label>
                            <p class="mb-0" style="font-weight:500;">{{ $user->name }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">Email</label>
                            <p class="mb-0" style="font-weight:500;">{{ $user->email }}</p>
                        </div>
                        <div class="col-sm-6">
                            <label class="form-label">No. Handphone</label>
                            <p class="mb-0" style="font-weight:500;">{{ $user->phone ?: '-' }}</p>
                        </div>
                        <div class="col-12">
                            <label class="form-label">Alamat</label>
                            <p class="mb-0" style="font-weight:500;">{!! $user->address ? nl2br(e($user->address)) : '-' !!}</p>
                        </div>
                    </div>
                </div>
            </div>

            {{-- Quick stats --}}
            <div class="row g-3 mt-1">
                <div class="col-4">
                    <div class="tp-card text-center" style="padding:20px 12px;">
                        <div style="font-size:1.5rem;font-weight:800;color:var(--brand);">
                            {{ $user->orders()->count() }}
                        </div>
                        <div class="text-muted" style="font-size:.78rem;">Total Pesanan</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="tp-card text-center" style="padding:20px 12px;">
                        <div style="font-size:1.5rem;font-weight:800;color:var(--brand);">
                            {{ $user->orders()->where('status','completed')->count() }}
                        </div>
                        <div class="text-muted" style="font-size:.78rem;">Selesai</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="tp-card text-center" style="padding:20px 12px;">
                        <div style="font-size:1.5rem;font-weight:800;color:var(--brand);">
                            {{ $user->orders()->whereIn('status',['waiting_payment','processing','shipping'])->count() }}
                        </div>
                        <div class="text-muted" style="font-size:.78rem;">Aktif</div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
