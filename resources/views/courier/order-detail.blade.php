@extends('layouts.app')

@section('styles')
<style>
    body { background: #f0f2f5; font-family: 'Inter', sans-serif; }
    .camera-card {
        background: #000;
        width: 100%;
        height: 480px;
        border-radius: 30px;
        position: relative;
        overflow: hidden;
        box-shadow: 0 20px 40px rgba(0,0,0,0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        border: 4px solid #fff;
    }
    #capturedPhoto {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: none;
    }
    .shutter-btn {
        width: 80px;
        height: 80px;
        background: #fff;
        border-radius: 50%;
        border: 6px solid #0d6efd;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 20px rgba(13, 110, 253, 0.4);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    .shutter-btn:active { transform: scale(0.9); }
    .camera-ui-container {
        position: absolute;
        bottom: 30px;
        left: 0;
        width: 100%;
        display: flex;
        flex-direction: column;
        align-items: center;
        z-index: 10;
    }
    .camera-placeholder {
        text-align: center;
        color: rgba(255,255,255,0.6);
    }
    .badge-camera {
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(10px);
        padding: 8px 20px;
        border-radius: 50px;
        color: #fff;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        margin-bottom: 20px;
        letter-spacing: 1px;
        border: 1px solid rgba(255,255,255,0.1);
    }
    .btn-finish {
        background: #198754;
        color: #fff;
        border: none;
        padding: 12px 35px;
        border-radius: 50px;
        font-weight: 700;
        box-shadow: 0 10px 20px rgba(25, 135, 84, 0.3);
    }
</style>
@endsection

@section('content')
<div class="container py-4">
    <div class="row justify-content-center">
        <div class="col-md-6 col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 mb-4" style="background: var(--surface);">
                <div class="card-body p-3 p-md-4">
                    <div class="d-flex align-items-center mb-3 pb-3 border-bottom border-light">
                        <a href="{{ route('courier.dashboard') }}" class="btn btn-light rounded-circle shadow-sm me-3 flex-shrink-0" style="width: 42px; height: 42px; display: flex; align-items: center; justify-content: center;">
                            <i class="bi bi-arrow-left text-dark fs-5"></i>
                        </a>
                        <div class="flex-grow-1">
                            <h5 class="fw-bold mb-1 text-dark">Bukti Pengiriman</h5>
                            <span class="badge bg-brand-light text-brand px-3 py-1 rounded-pill fw-semibold">{{ $order->order_number ?? 'Order #' . $order->id }}</span>
                        </div>
                    </div>
                    
                    <div class="bg-surface-secondary p-3 rounded-3">
                        <div class="d-flex align-items-start mb-2">
                            <i class="bi bi-person-fill text-muted me-2 mt-1"></i>
                            <div>
                                <small class="text-muted d-block lh-1">Penerima</small>
                                <span class="fw-semibold text-dark">{{ $order->shipping_name }}</span>
                                <div class="text-muted small mt-1"><i class="bi bi-telephone-fill me-1"></i>{{ $order->shipping_phone }}</div>
                            </div>
                        </div>
                        <hr class="border-light my-2">
                        <div class="d-flex align-items-start">
                            <i class="bi bi-geo-alt-fill text-brand me-2 mt-1"></i>
                            <div>
                                <small class="text-muted d-block lh-1">Alamat Tujuan</small>
                                <span class="small text-dark d-block mt-1">{{ $order->shipping_address }}</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Attendance Style Camera Box -->
            <div class="camera-card mb-4" style="{{ $order->status === 'delivered' ? 'background: transparent; border-color: var(--brand-light);' : '' }}">
                @if($order->status === 'delivered' && $order->proof_of_delivery)
                    <div class="badge-camera position-absolute top-0 mt-4 bg-brand text-white border-0 shadow">
                        <i class="bi bi-check-circle-fill me-1"></i> Pengiriman Selesai
                    </div>
                    <img id="finalPhoto" src="{{ asset('storage/' . $order->proof_of_delivery) }}" style="width: 100%; height: 100%; object-fit: cover; border-radius: 26px;">
                @else
                    <div class="badge-camera position-absolute top-0 mt-4">
                        <i class="bi bi-shield-fill-check me-1 text-success"></i> Verifikasi Kurir
                    </div>

                    <!-- Result Display -->
                    <img id="capturedPhoto" src="">

                    <!-- Placeholder when empty -->
                    <div id="cameraPlaceholder" class="camera-placeholder">
                        <div class="mb-3">
                            <i class="bi bi-camera-fill" style="font-size: 4rem;"></i>
                        </div>
                        <p class="px-5">Ketuk tombol di bawah untuk mengambil foto bukti secara langsung.</p>
                    </div>

                    <div class="camera-ui-container">
                        <form id="deliveryForm" method="POST" action="{{ route('courier.orders.update-delivery', $order) }}" enctype="multipart/form-data">
                            @csrf
                            <!-- The Master Key: Hidden Native Capture -->
                            <input type="file" name="proof_of_delivery" id="mainCameraInput" class="d-none" accept="image/*" capture="environment">
                            
                            <!-- Circular Shutter Button -->
                            <div id="btnGoCamera" class="shutter-btn">
                                <i class="bi bi-camera-fill fs-2 text-primary"></i>
                            </div>

                            <!-- Action Controls after snap -->
                            <div id="postSnapAction" class="d-none">
                                <div class="d-flex gap-3">
                                    <button type="button" id="btnRetry" class="btn btn-dark rounded-pill px-4 fw-bold">
                                        <i class="bi bi-arrow-clockwise me-1"></i>ULANG
                                    </button>
                                    <button type="submit" class="btn-finish">
                                        <i class="bi bi-check2-circle me-1"></i>SELESAI
                                    </button>
                                </div>
                            </div>
                        </form>
                    </div>
                @endif
            </div>

            <div class="text-center">
                @if($order->status === 'delivered')
                    <div class="bg-white p-3 rounded-4 shadow-sm border mb-3">
                        <div class="text-success fw-bold mb-1"><i class="bi bi-clock-history me-1"></i> Waktu Selesai</div>
                        <div class="text-muted small">{{ $order->updated_at->isoFormat('D MMMM YYYY, HH:mm') }} WIB</div>
                    </div>
                    <a href="{{ route('courier.dashboard', ['status' => 'delivered']) }}" class="btn btn-outline-dark rounded-pill px-4">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Dashboard
                    </a>
                @else
                    <p class="text-muted small px-4">
                        <i class="bi bi-info-circle me-1"></i> Aplikasi memanggil fitur kamera sistem untuk keamanan & kompatibilitas tinggi.
                    </p>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection

@section('scripts')
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const cameraInput = document.getElementById("mainCameraInput");
        const btnGo = document.getElementById("btnGoCamera");
        const btnRetry = document.getElementById("btnRetry");
        const resultImg = document.getElementById("capturedPhoto");
        const placeholder = document.getElementById("cameraPlaceholder");
        const postAction = document.getElementById("postSnapAction");

        // 1. Trigger Kamera Sistem (Chrome/Safari/Mac/HP)
        btnGo.addEventListener("click", function() {
            cameraInput.click();
        });

        // 2. Handle Hasil Foto
        cameraInput.addEventListener("change", function() {
            if (this.files && this.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    // Tampilkan Hasil
                    resultImg.src = e.target.result;
                    resultImg.style.display = 'block';
                    
                    // UI Transitions
                    placeholder.style.display = 'none';
                    btnGo.style.display = 'none';
                    postAction.classList.remove('d-none');
                }
                reader.readAsDataURL(this.files[0]);
            }
        });

        // 3. Reset / Retake
        btnRetry.addEventListener("click", function() {
            cameraInput.value = "";
            resultImg.style.display = 'none';
            placeholder.style.display = 'block';
            btnGo.style.display = 'flex';
            postAction.classList.add('d-none');
        });

        // 4. Loading on Submit
        document.getElementById("deliveryForm").addEventListener('submit', function() {
            Swal.fire({
                title: 'Sedang Mengirim...',
                allowOutsideClick: false,
                didOpen: () => Swal.showLoading()
            });
        });
    });
</script>
@endsection
