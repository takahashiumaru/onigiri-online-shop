@extends('layouts.admin')

@section('title', 'Notifikasi Admin')
@section('breadcrumb', 'Notifikasi')

@section('content')
<div class="container-fluid">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Notifikasi Admin</h5>
                </div>
                <div class="card-body">
                    @auth
                        @php $notes = auth()->user()->notifications; @endphp
                        @if($notes->isEmpty())
                            <div class="text-center py-4 text-muted">Tidak ada notifikasi.</div>
                        @else
                            <ul class="list-group">
                                @foreach($notes as $note)
                                    <li class="list-group-item d-flex justify-content-between align-items-start">
                                        <div>
                                            <div class="fw-semibold">{{ $note->data['title'] ?? 'Notifikasi' }}</div>
                                            <div class="small text-muted">{{ $note->created_at->diffForHumans() }}</div>
                                            @if(!empty($note->data['message']))
                                                <div class="small mt-1 text-muted">{{ $note->data['message'] }}</div>
                                            @endif
                                        </div>
                                        @if(empty($note->read_at))
                                            <span class="badge bg-success">Baru</span>
                                        @endif
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    @endauth
                </div>
                <div class="card-footer text-end">
                    <a href="#" class="btn btn-outline-primary">Tandai semua</a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
