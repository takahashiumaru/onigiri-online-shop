@extends('layouts.app')

@section('title', $product->name ?? 'Produk')

@section('content')
<div class="container py-5">
    <!-- Back button -->
    <div class="mb-3">
        <button class="btn btn-outline-secondary" type="button" onclick="history.back();">← Kembali</button>
    </div>

	<div class="row g-4">
		<div class="col-md-6">
			<div class="bg-light rounded p-2" style="border-radius:12px; overflow:hidden;">
				@if($product->image && \Storage::disk('public')->exists($product->image))
					<img src="{{ \Storage::url($product->image) }}" class="img-fluid" alt="{{ $product->name }}">
				@else
					<div class="d-flex align-items-center justify-content-center" style="height:320px;">🍙</div>
				@endif
			</div>
		</div>

		<div class="col-md-6">
			<h2 class="fw-bold">{{ $product->name }}</h2>
			<div class="mb-2 text-muted">{{ $product->category->name ?? '' }}</div>
			<div class="fs-4 fw-semibold mb-3" style="color:var(--brand);">Rp {{ number_format($product->price,0,',','.') }}</div>

			<p class="text-muted">{{ $product->description }}</p>

			<form action="{{ route('cart.add', $product) }}" method="POST" class="d-flex gap-2 align-items-center mb-3">
				@csrf
				<input type="number" name="quantity" value="1" min="1" class="form-control" style="width:100px;">
				<button class="btn btn-primary">Tambah ke Keranjang</button>
			</form>

			<hr>

			@php
				$avg = $reviews->avg('rating') ?? null;
				$count = $reviews->count();
			@endphp

			<div class="mb-2">
				@includeIf('customer.partials.rating', ['avg' => $avg, 'count' => $count])
			</div>

		</div>
	</div>

	@if($reviews->count())
	<div class="mt-4">
		<h5 class="fw-bold">Ulasan Pembeli</h5>
		@foreach($reviews as $r)
		<div class="card mb-3">
			<div class="card-body">
				<div class="d-flex justify-content-between align-items-start flex-wrap">
					<div style="min-width:0;">
						<div class="fw-semibold">{{ $r->order->shipping_name ?? 'Pembeli' }}</div>
						<div class="text-muted small">{{ $r->order->created_at->format('d M Y') }}</div>
					</div>
					<div class="mt-2 mt-sm-0">
						@includeIf('customer.partials.rating', ['rating' => $r->rating])
					</div>
				</div>

				@if($r->rating_review)
					<p class="mt-2 mb-0 text-muted">{{ $r->rating_review }}</p>
				@endif
			</div>
		</div>
		@endforeach
	</div>
	@endif
</div>
@endsection

@section('styles')
    <style>
    /* responsive rating in product detail / reviews */
    .stars-wrapper { display:flex; gap:.25rem; align-items:center; flex-wrap:wrap; }
    .star { color:#E5E7EB; font-size:1.05rem; line-height:1; }
    .star-selected { color:#F59E0B !important; }

    @media (max-width:991.98px){
        .star { font-size:0.95rem; }
    }
    @media (max-width:576px){
        .star { font-size:0.9rem; }
        .stars-wrapper { margin-top:6px; }
    }
    </style>
@endsection
