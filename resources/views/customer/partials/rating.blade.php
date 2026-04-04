@php
$avgValue = isset($avg) && $avg !== null ? (float) $avg : 0;
$countValue = isset($count) ? (int) $count : 0;
$isRated = $avgValue > 0;
@endphp

<div class="rating-component d-flex align-items-center" title="Rating: {{ number_format($avgValue,1) }} / 5">
    <i class="bi {{ $isRated ? 'bi-star-fill' : 'bi-star' }} star-single {{ $isRated ? 'star-selected' : '' }}" aria-hidden="true" style="font-size:0.9rem; line-height:1;"></i>
    <span class="ms-1 fw-semibold text-dark" style="font-size: 0.85rem;">{{ $isRated ? number_format($avgValue,1) : '0' }}</span>
    <span class="text-muted ms-1" style="font-size: 0.75rem;">({{ $countValue }})</span>
</div>

<style>
/* gaya ringkas agar konsisten dengan card */
.rating-component .star-single { color: #E5E7EB; display:inline-block; }
.rating-component .star-selected { color: #F59E0B !important; }
</style>
