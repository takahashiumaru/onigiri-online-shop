@extends('layouts.admin')
@section('title', 'Manajemen Kurir')
@section('subbreadcrumb', 'Daftar Kurir')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h5 class="fw-bold mb-0"><i class="bi bi-person-badge text-primary me-2"></i>Daftar Kurir</h5>
    <a href="{{ route('admin.couriers.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-2"></i>Tambah Kurir
    </a>
</div>

@if(session('success'))
<div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
    {{ session('success') }}
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
</div>
@endif

<div class="card shadow-sm border-0">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead class="table-light">
                <tr>
                    <th>Nama Kurir</th>
                    <th>Email</th>
                    <th>No. Telepon</th>
                    <th>Terdaftar Pada</th>
                    <th class="text-end">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($couriers as $courier)
                <tr>
                    <td>
                        @php $courierPhoto = $courier->avatar ?? $courier->photo; @endphp
                        <div class="d-flex align-items-center gap-3">
                            @if($courierPhoto)
                                <img src="{{ asset('storage/' . $courierPhoto) }}" class="rounded-circle object-fit-cover" style="width: 40px; height: 40px;" alt="{{ $courier->name }}">
                            @else
                                <div class="avatar-circle" style="width: 40px; height: 40px; font-size: 1rem;">
                                    {{ strtoupper(substr($courier->name, 0, 1)) }}
                                </div>
                            @endif
                            <div class="fw-semibold">{{ $courier->name }}</div>
                        </div>
                    </td>
                    <td>{{ $courier->email }}</td>
                    <td>{{ $courier->phone ?? '-' }}</td>
                    <td>{{ $courier->created_at->format('d M Y') }}</td>
                    <td class="text-end">
                        <a href="{{ route('admin.couriers.edit', $courier) }}" class="btn btn-sm btn-outline-primary mb-1 shadow-none">
                            <i class="bi bi-pencil"></i> Edit
                        </a>
                        <form action="{{ route('admin.couriers.destroy', $courier) }}" method="POST" class="d-inline-block" onsubmit="return confirm('Hapus akun kurir ini?');">
                            @csrf @method('DELETE')
                            <button type="submit" class="btn btn-sm btn-outline-danger mb-1 shadow-none">
                                <i class="bi bi-trash"></i> Hapus
                            </button>
                        </form>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="text-center py-5 text-muted">
                        <i class="bi bi-person-slash fs-1 d-block mb-3"></i>
                        Belum ada kurir yang terdaftar.
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($couriers->hasPages())
    <div class="card-footer bg-white pt-3 pb-2 border-top">
        {{ $couriers->links() }}
    </div>
    @endif
</div>
@endsection
