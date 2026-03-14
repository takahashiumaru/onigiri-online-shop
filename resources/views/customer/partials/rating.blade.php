@php
$avgValue = isset($avg) && $avg !== null ? (float) $avg : 0;
$countValue = isset($count) ? (int) $count : 0;
$isRated = $avgValue > 0;
@endphp

<div class="rating-component d-flex align-items-center" title="Rating: {{ number_format($avgValue,1) }} / 5">
    {{-- hanya satu ikon bintang --}}
    <i class="bi {{ $isRated ? 'bi-star-fill' : 'bi-star' }} star-single {{ $isRated ? 'star-selected' : '' }}" aria-hidden="true" style="font-size:1rem;line-height:1;"></i>

    {{-- tampilkan angka rata-rata (atau 0 jika belum ada) dan jumlah ulasan --}}
    <small class="text-muted ms-2" style="font-size:.9rem;">
        {{ $isRated ? number_format($avgValue,1) : '0' }} ({{ $countValue }})
    </small>
</div>

<style>
/* gaya ringkas agar konsisten dengan card */
.rating-component .star-single { color: #E5E7EB; display:inline-block; }
.rating-component .star-selected { color: #F59E0B !important; }
</style>
