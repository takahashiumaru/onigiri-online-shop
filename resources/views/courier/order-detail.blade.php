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
    #cameraStream {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: none;
        border-radius: 26px;
    }
    #capturedPhoto {
        width: 100%;
        height: 100%;
        object-fit: cover;
        display: none;
        border-radius: 26px;
    }
    #hiddenCanvas { display: none; }
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
    /* Tombol snap saat live preview */
    #btnSnapPhoto {
        width: 80px;
        height: 80px;
        background: #fff;
        border-radius: 50%;
        border: 6px solid #dc3545;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 0 20px rgba(220, 53, 69, 0.4);
        cursor: pointer;
        transition: all 0.2s ease;
    }
    #btnSnapPhoto:active { transform: scale(0.9); }

    /* Fallback label button */
    .btn-fallback-file {
        background: rgba(255,255,255,0.15);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3);
        color: #fff;
        padding: 8px 20px;
        border-radius: 50px;
        font-size: 0.8rem;
        cursor: pointer;
        margin-top: 8px;
        display: inline-block;
        transition: background 0.2s;
    }
    .btn-fallback-file:hover { background: rgba(255,255,255,0.25); }

    /* ── Clock Widget ─────────────────────────────────────── */
    .clock-widget {
        background: linear-gradient(135deg, #fff1f2 0%, #ffe4e6 50%, #fecdd3 100%);
        border-radius: var(--radius-xl, 20px);
        padding: 16px 20px;
        margin-bottom: 16px;
        display: flex;
        align-items: center;
        gap: 14px;
        box-shadow: 0 4px 16px rgba(239,68,68,0.12), 0 1px 4px rgba(0,0,0,0.06);
        border: 1px solid rgba(239,68,68,0.15);
        position: relative;
        overflow: hidden;
    }
    .clock-widget::before {
        content: '';
        position: absolute;
        top: -30px; right: -30px;
        width: 100px; height: 100px;
        background: radial-gradient(circle, rgba(239,68,68,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }
    .clock-icon-wrap {
        width: 44px; height: 44px;
        background: rgba(239,68,68,0.12);
        border-radius: 12px;
        display: flex; align-items: center; justify-content: center;
        flex-shrink: 0;
        border: 1px solid rgba(239,68,68,0.2);
    }
    .clock-icon-wrap i { font-size: 1.3rem; color: #ef4444; }
    .clock-body { flex: 1; min-width: 0; }
    .clock-time {
        font-size: 1.6rem;
        font-weight: 700;
        color: #7f1d1d;
        letter-spacing: 1.5px;
        line-height: 1;
        font-variant-numeric: tabular-nums;
        font-family: 'Courier New', monospace;
    }
    .clock-time .clock-seconds {
        font-size: 1.05rem;
        color: rgba(185,28,28,0.5);
        font-weight: 400;
    }
    .clock-date {
        font-size: 0.74rem;
        color: #b91c1c;
        margin-top: 5px;
        letter-spacing: 0.2px;
        opacity: 0.8;
    }
    .clock-badge {
        background: #ef4444;
        color: #fff;
        font-size: 0.68rem;
        font-weight: 700;
        padding: 5px 12px;
        border-radius: 50px;
        letter-spacing: 1px;
        flex-shrink: 0;
        box-shadow: 0 2px 8px rgba(239,68,68,0.3);
    }
    .clock-live-dot {
        width: 7px; height: 7px;
        background: #ef4444;
        border-radius: 50%;
        display: inline-block;
        margin-right: 5px;
        animation: pulse-dot 1.4s ease-in-out infinite;
    }
    @keyframes pulse-dot {
        0%, 100% { opacity: 1; transform: scale(1); }
        50%       { opacity: 0.35; transform: scale(0.65); }
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

            <!-- Clock Widget -->
            <div class="clock-widget">
                <div class="clock-icon-wrap">
                    <i class="bi bi-clock-fill"></i>
                </div>
                <div class="clock-body">
                    <div class="clock-time">
                        <span id="clockHMS">--:--</span><span class="clock-seconds" id="clockSec">:--</span>
                    </div>
                    <div class="clock-date">
                        <span class="clock-live-dot"></span>
                        <span id="clockDate">-- --- ----</span>
                    </div>
                </div>
                <div class="clock-badge">WIB</div>
            </div>

            <!-- Camera Box -->
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

                    <!-- Live camera stream (WebRTC) -->
                    <video id="cameraStream" autoplay playsinline muted></video>

                    <!-- Canvas untuk capture (tersembunyi) -->
                    <canvas id="hiddenCanvas"></canvas>

                    <!-- Hasil foto -->
                    <img id="capturedPhoto" src="" alt="Bukti foto">

                    <!-- Placeholder awal -->
                    <div id="cameraPlaceholder" class="camera-placeholder">
                        <div class="mb-3">
                            <i class="bi bi-camera-fill" style="font-size: 4rem;"></i>
                        </div>
                        <p class="px-5">Ketuk tombol di bawah untuk membuka kamera.</p>
                    </div>

                    <div class="camera-ui-container">
                        <form id="deliveryForm" method="POST" action="{{ route('courier.orders.update-delivery', $order) }}" enctype="multipart/form-data">
                            @csrf
                            <!-- Input file tersembunyi sebagai fallback & untuk submit -->
                            <input type="file" name="proof_of_delivery" id="mainCameraInput" class="d-none" accept="image/*">
                            <!-- Input hidden untuk data base64 (opsional jika backend support) -->

                            <!-- Tombol buka kamera (tampil awal) -->
                            <div id="btnGoCamera" class="shutter-btn">
                                <i class="bi bi-camera-fill fs-2 text-primary"></i>
                            </div>

                            <!-- Tombol snap (tampil saat live stream aktif) -->
                            <div id="btnSnapPhoto" class="d-none">
                                <i class="bi bi-record-circle fs-2 text-danger"></i>
                            </div>

                            <!-- Fallback: pilih dari galeri / file -->
                            <label for="mainCameraInput" id="btnFallback" class="btn-fallback-file d-none">
                                <i class="bi bi-image me-1"></i> Pilih dari galeri
                            </label>

                            <!-- Action setelah foto diambil -->
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
                        <i class="bi bi-info-circle me-1"></i> Aplikasi menggunakan kamera secara langsung. Izinkan akses kamera jika diminta.
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
document.addEventListener("DOMContentLoaded", function () {
    // ─── WIB Live Clock ──────────────────────────────────────────────────────
    const elHMS  = document.getElementById('clockHMS');
    const elSec  = document.getElementById('clockSec');
    const elDate = document.getElementById('clockDate');
    const DAYS   = ['Minggu','Senin','Selasa','Rabu','Kamis','Jumat','Sabtu'];
    const MONTHS = ['Jan','Feb','Mar','Apr','Mei','Jun','Jul','Agu','Sep','Okt','Nov','Des'];

    function tickClock() {
        const now = new Date();
        const pad = n => String(n).padStart(2, '0');
        const wib = new Date(now.getTime() + 7 * 60 * 60 * 1000); // UTC+7

        elHMS.textContent  = `${pad(wib.getUTCHours())}:${pad(wib.getUTCMinutes())}`;
        elSec.textContent  = `:${pad(wib.getUTCSeconds())}`;
        elDate.textContent = `${DAYS[wib.getUTCDay()]}, ${wib.getUTCDate()} ${MONTHS[wib.getUTCMonth()]} ${wib.getUTCFullYear()}`;
    }
    tickClock();
    setInterval(tickClock, 1000);


    // Elemen yang ada hanya saat status bukan 'delivered'
    const btnGo        = document.getElementById("btnGoCamera");
    if (!btnGo) return; // sudah delivered, tidak perlu logic kamera

    const btnSnap      = document.getElementById("btnSnapPhoto");
    const btnRetry     = document.getElementById("btnRetry");
    const btnFallback  = document.getElementById("btnFallback");
    const videoEl      = document.getElementById("cameraStream");
    const canvas       = document.getElementById("hiddenCanvas");
    const resultImg    = document.getElementById("capturedPhoto");
    const placeholder  = document.getElementById("cameraPlaceholder");
    const postAction   = document.getElementById("postSnapAction");
    const fileInput    = document.getElementById("mainCameraInput");
    const form         = document.getElementById("deliveryForm");

    let activeStream   = null; // menyimpan MediaStream aktif

    // ─── Fungsi: Hentikan stream kamera ─────────────────────────────────────
    function stopStream() {
        if (activeStream) {
            activeStream.getTracks().forEach(t => t.stop());
            activeStream = null;
        }
        videoEl.srcObject = null;
        videoEl.style.display = 'none';
    }

    // ─── Fungsi: Buka kamera via WebRTC ─────────────────────────────────────
    async function openCamera() {
        try {
            // Coba kamera belakang di mobile, kamera mana saja di desktop
            const constraints = {
                video: {
                    facingMode: { ideal: 'environment' },
                    width:  { ideal: 1280 },
                    height: { ideal: 720 }
                },
                audio: false
            };

            const stream = await navigator.mediaDevices.getUserMedia(constraints);
            activeStream = stream;
            videoEl.srcObject = stream;
            videoEl.style.display = 'block';

            // Sembunyikan placeholder & tombol buka, tampilkan tombol snap
            placeholder.style.display = 'none';
            btnGo.style.display       = 'none';
            btnFallback.classList.add('d-none');
            btnSnap.classList.remove('d-none');

        } catch (err) {
            console.warn("WebRTC gagal:", err.name, err.message);

            // Fallback: gunakan native file input
            useFallbackFileInput(err);
        }
    }

    // ─── Fungsi: Panduan reset izin per browser ──────────────────────────────
    function getBrowserResetGuide() {
        const ua = navigator.userAgent;
        if (/Chrome/.test(ua) && !/Edg/.test(ua)) {
            return `<div style="text-align:left;font-size:0.85rem;margin-top:8px;">
                <b>Chrome:</b> Klik ikon 🔒 di address bar → <b>Izin Situs</b> → Kamera → Ubah ke <b>Izinkan</b>, lalu tekan <b>Coba Lagi</b>.
            </div>`;
        } else if (/Safari/.test(ua)) {
            return `<div style="text-align:left;font-size:0.85rem;margin-top:8px;">
                <b>Safari:</b> Buka <b>Preferensi → Situs Web → Kamera</b> → Ubah izin untuk situs ini ke <b>Izinkan</b>, lalu tekan <b>Coba Lagi</b>.
            </div>`;
        } else if (/Edg/.test(ua)) {
            return `<div style="text-align:left;font-size:0.85rem;margin-top:8px;">
                <b>Edge:</b> Klik ikon 🔒 di address bar → <b>Izin</b> → Kamera → <b>Izinkan</b>, lalu tekan <b>Coba Lagi</b>.
            </div>`;
        } else if (/Firefox/.test(ua)) {
            return `<div style="text-align:left;font-size:0.85rem;margin-top:8px;">
                <b>Firefox:</b> Klik ikon kamera di address bar → <b>Hapus blokir</b> → Muat ulang halaman.
            </div>`;
        }
        return `<div style="text-align:left;font-size:0.85rem;margin-top:8px;">
            Buka <b>Pengaturan Browser → Izin Situs → Kamera</b> → Aktifkan untuk halaman ini, lalu tekan <b>Coba Lagi</b>.
        </div>`;
    }

    // ─── Fungsi: Tampilkan dialog izin ditolak ────────────────────────────────
    function showPermissionDeniedDialog() {
        Swal.fire({
            icon: 'warning',
            title: 'Izin Kamera Diperlukan',
            html: `
                <p style="margin-bottom:4px;">Browser memblokir akses kamera untuk halaman ini.</p>
                ${getBrowserResetGuide()}
            `,
            confirmButtonText: '<i class="bi bi-camera-fill me-1"></i> Coba Lagi',
            showDenyButton: true,
            denyButtonText: '<i class="bi bi-image me-1"></i> Pilih dari Galeri',
            showCancelButton: true,
            cancelButtonText: 'Batal',
            confirmButtonColor: '#0d6efd',
            denyButtonColor: '#6c757d',
        }).then(result => {
            if (result.isConfirmed) {
                // Coba minta ulang izin kamera
                openCamera();
            } else if (result.isDenied) {
                // Fallback ke galeri
                fileInput.removeAttribute('capture');
                fileInput.click();
            }
        });

        btnFallback.classList.remove('d-none');
    }

    // ─── Fungsi: Fallback ke file input (error selain ditolak) ───────────────
    function useFallbackFileInput(err) {
        let title = 'Kamera Tidak Tersedia';
        let html  = '<p>Kamera tidak dapat diakses.</p>';

        if (!err) {
            // Browser tidak support WebRTC sama sekali
            html = '<p>Browser ini tidak mendukung akses kamera langsung. Silakan pilih foto dari galeri.</p>';
        } else if (err.name === 'NotFoundError') {
            html = '<p>Tidak ditemukan kamera di perangkat ini. Silakan pilih foto dari galeri.</p>';
        } else if (err.name === 'NotReadableError') {
            html = '<p>Kamera sedang digunakan oleh aplikasi lain. Tutup aplikasi tersebut, lalu coba lagi.</p>';
        } else {
            html = `<p>${err.message || 'Terjadi kesalahan tidak dikenal.'}</p>`;
        }

        Swal.fire({
            icon: 'error',
            title: title,
            html: html,
            confirmButtonText: '<i class="bi bi-image me-1"></i> Pilih dari Galeri',
            showCancelButton: true,
            cancelButtonText: 'Batal',
        }).then(result => {
            if (result.isConfirmed) {
                fileInput.removeAttribute('capture');
                fileInput.click();
            }
        });

        btnFallback.classList.remove('d-none');
    }

    // ─── Tombol: Buka Kamera ─────────────────────────────────────────────────
    btnGo.addEventListener("click", async function () {
        if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
            useFallbackFileInput(null);
            return;
        }

        // Cek status izin dulu jika browser support Permissions API
        if (navigator.permissions) {
            try {
                const perm = await navigator.permissions.query({ name: 'camera' });
                if (perm.state === 'denied') {
                    // Sudah ditolak & browser tidak akan tanya ulang → tampilkan panduan
                    showPermissionDeniedDialog();
                    return;
                }
                // 'granted' atau 'prompt' → lanjut request lewat getUserMedia
            } catch (_) {
                // Permissions API tidak support query 'camera', lanjut saja
            }
        }

        openCamera();
    });

    // ─── Tombol: Ambil Foto (Snap) ──────────────────────────────────────────
    btnSnap.addEventListener("click", function () {
        if (!activeStream) return;

        // Siapkan canvas sesuai ukuran video aktual
        const vw = videoEl.videoWidth  || videoEl.clientWidth;
        const vh = videoEl.videoHeight || videoEl.clientHeight;
        canvas.width  = vw;
        canvas.height = vh;

        const ctx = canvas.getContext('2d');
        ctx.drawImage(videoEl, 0, 0, vw, vh);

        // Ambil blob dan masukkan ke file input agar bisa di-submit sebagai multipart
        canvas.toBlob(function (blob) {
            const file = new File([blob], 'bukti_pengiriman.jpg', { type: 'image/jpeg' });
            const dt   = new DataTransfer();
            dt.items.add(file);
            fileInput.files = dt.files;

            // Tampilkan preview
            resultImg.src = canvas.toDataURL('image/jpeg', 0.9);
            resultImg.style.display = 'block';
        }, 'image/jpeg', 0.9);

        // Hentikan stream dan update UI
        stopStream();
        placeholder.style.display = 'none';
        btnSnap.classList.add('d-none');
        postAction.classList.remove('d-none');
    });

    // ─── Tombol: Ulangi Foto ────────────────────────────────────────────────
    btnRetry.addEventListener("click", function () {
        // Reset UI
        resultImg.style.display   = 'none';
        resultImg.src             = '';
        postAction.classList.add('d-none');
        placeholder.style.display = 'block';
        btnGo.style.display       = 'flex';
        fileInput.value           = '';
        stopStream();
    });

    // ─── Fallback: Dari file picker / galeri ───────────────────────────────
    fileInput.addEventListener("change", function () {
        if (this.files && this.files[0]) {
            const reader = new FileReader();
            reader.onload = function (e) {
                resultImg.src             = e.target.result;
                resultImg.style.display   = 'block';
                placeholder.style.display = 'none';
                btnGo.style.display       = 'none';
                btnFallback.classList.add('d-none');
                postAction.classList.remove('d-none');
            };
            reader.readAsDataURL(this.files[0]);
        }
    });

    // ─── Submit: Tampilkan loading ──────────────────────────────────────────
    form.addEventListener('submit', function (e) {
        if (!fileInput.files || !fileInput.files[0]) {
            e.preventDefault();
            Swal.fire({ icon: 'warning', title: 'Belum ada foto!', text: 'Ambil foto bukti pengiriman terlebih dahulu.' });
            return;
        }
        stopStream(); // pastikan kamera dimatikan
        Swal.fire({
            title: 'Sedang Mengirim...',
            allowOutsideClick: false,
            didOpen: () => Swal.showLoading()
        });
    });
});
</script>
@endsection
