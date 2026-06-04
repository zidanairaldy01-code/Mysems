@extends('layouts.admin')

@section('title', 'Scan QR Kehadiran - Admin')

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-lg-8">
        <!-- Header Section -->
        <div class="text-center mb-4">
            <h2 class="fw-bold text-gradient">Scan QR Kehadiran</h2>
            <p class="text-muted">Gunakan kamera untuk melakukan absensi peserta secara real-time.</p>
        </div>

        <div class="card border-0 shadow-lg rounded-5 overflow-hidden glass-card">
            <div class="card-body p-0">
                <div class="row g-0">
                    <!-- Scanner Area -->
                    <div class="col-md-12 p-4">
                        <div id="scanner-wrapper" class="position-relative rounded-4 overflow-hidden border border-2 border-primary border-opacity-25 shadow-sm mx-auto" style="max-width: 450px;">
                            <div id="reader"></div>
                            <div class="scanner-overlay">
                                <div class="scanner-line"></div>
                            </div>
                        </div>
                        <div class="text-center mt-3">
                            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                                <i class="bi bi-camera me-2"></i> Kamera Aktif
                            </span>
                        </div>
                    </div>

                    <!-- Result Area (Populated via JS) -->
                    <div id="result-container" class="col-md-12 p-4 pt-0 d-none animate__animated animate__fadeInUp">
                        <div class="divider mb-4"></div>
                        
                        <div id="scan-status" class="alert border-0 rounded-4 p-4 mb-4 shadow-sm">
                            <div class="d-flex align-items-center gap-4">
                                <div class="status-icon-wrapper rounded-circle p-3 shadow-sm">
                                    <i id="status-icon" class="bi fs-1"></i>
                                </div>
                                <div>
                                    <h4 id="status-title" class="fw-bold mb-1"></h4>
                                    <p id="status-message" class="mb-0 opacity-75"></p>
                                </div>
                            </div>
                        </div>
                        
                        <div id="participant-details" class="details-card rounded-4 p-4 d-none border">
                            <h5 class="fw-bold mb-4 border-bottom pb-2">Informasi Peserta</h5>
                            <div class="row g-4">
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="icon-box bg-light rounded-3 p-2 text-primary">
                                            <i class="bi bi-person-fill fs-5"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Nama Peserta</small>
                                            <span id="p-name" class="fw-bold fs-5"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="icon-box bg-light rounded-3 p-2 text-primary">
                                            <i class="bi bi-calendar-event fs-5"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Event</small>
                                            <span id="p-event" class="fw-bold fs-5"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="icon-box bg-light rounded-3 p-2 text-success">
                                            <i class="bi bi-credit-card-fill fs-5"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Status Pembayaran</small>
                                            <span id="p-payment" class="badge rounded-pill fw-bold"></span>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="icon-box bg-light rounded-3 p-2 text-warning">
                                            <i class="bi bi-clock-history fs-5"></i>
                                        </div>
                                        <div>
                                            <small class="text-muted d-block">Waktu Absen</small>
                                            <span id="p-time" class="fw-bold fs-5"></span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button onclick="resetScanner()" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow">
                                <i class="bi bi-camera me-2"></i> Lanjut Scan Peserta Lain
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<style>
    .text-gradient {
        background: linear-gradient(45deg, #0d6efd, #0dcaf0);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
    }

    .glass-card {
        background: rgba(255, 255, 255, 0.9);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }

    [data-theme='dark'] .glass-card {
        background: rgba(30, 41, 59, 0.8);
        border: 1px solid rgba(255, 255, 255, 0.05);
    }

    #reader {
        border: none !important;
    }

    #reader video {
        border-radius: 12px;
        object-fit: cover;
    }

    .scanner-overlay {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        pointer-events: none;
        z-index: 10;
        background: rgba(0,0,0,0.1);
    }

    .scanner-line {
        position: absolute;
        width: 100%;
        height: 2px;
        background: rgba(13, 110, 253, 0.8);
        box-shadow: 0 0 15px rgba(13, 110, 253, 0.8);
        animation: scan 2s linear infinite;
    }

    @keyframes scan {
        0% { top: 0; }
        50% { top: 100%; }
        100% { top: 0; }
    }

    [data-theme='dark'] .alert-success {
        background: rgba(21, 128, 61, 0.2) !important;
        color: #4ade80 !important;
        border: 1px solid rgba(74, 222, 128, 0.2) !important;
    }
    [data-theme='dark'] .alert-success #status-icon { color: #4ade80 !important; }

    [data-theme='dark'] .alert-warning {
        background: rgba(161, 98, 7, 0.2) !important;
        color: #facc15 !important;
        border: 1px solid rgba(250, 204, 21, 0.2) !important;
    }
    [data-theme='dark'] .alert-warning #status-icon { color: #facc15 !important; }

    [data-theme='dark'] .alert-danger {
        background: rgba(153, 27, 27, 0.2) !important;
        color: #f87171 !important;
        border: 1px solid rgba(248, 113, 113, 0.2) !important;
    }
    [data-theme='dark'] .alert-danger #status-icon { color: #f87171 !important; }

    .status-icon-wrapper {
        background: rgba(255, 255, 255, 0.9);
    }
    [data-theme='dark'] .status-icon-wrapper {
        background: rgba(255, 255, 255, 0.1);
    }

    .details-card {
        background: rgba(248, 249, 250, 0.5);
    }

    [data-theme='dark'] .details-card {
        background: rgba(15, 23, 42, 0.3);
        border-color: rgba(255, 255, 255, 0.05) !important;
    }

    .icon-box {
        width: 45px;
        height: 45px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .divider {
        height: 1px;
        background: linear-gradient(to right, transparent, rgba(0,0,0,0.1), transparent);
    }

    [data-theme='dark'] .divider {
        background: linear-gradient(to right, transparent, rgba(255,255,255,0.1), transparent);
    }

    /* Override html5-qrcode styles */
    #reader__dashboard_section_csr button {
        background-color: #0d6efd !important;
        border: none !important;
        border-radius: 50px !important;
        padding: 8px 20px !important;
        color: white !important;
        font-weight: bold !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://unpkg.com/html5-qrcode"></script>
<script>
    let html5QrcodeScanner = null;
    let isScanning = true;

    function onScanSuccess(decodedText, decodedResult) {
        if (!isScanning) return;
        
        isScanning = false;
        html5QrcodeScanner.clear();
        
        fetch("{{ route('admin.registration.scan.process') }}", {
            method: "POST",
            headers: {
                "Content-Type": "application/json",
                "X-CSRF-TOKEN": "{{ csrf_token() }}"
            },
            body: JSON.stringify({ qr_code: decodedText })
        })
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('result-container');
            const statusDiv = document.getElementById('scan-status');
            const icon = document.getElementById('status-icon');
            const title = document.getElementById('status-title');
            const message = document.getElementById('status-message');
            const details = document.getElementById('participant-details');

            container.classList.remove('d-none');
            
            if (data.success || data.already_attended) {
                statusDiv.className = data.success 
                    ? 'alert alert-success border-0 rounded-4 p-4 mb-4 shadow-sm animate__animated animate__pulse'
                    : 'alert alert-warning border-0 rounded-4 p-4 mb-4 shadow-sm animate__animated animate__headShake';
                
                icon.className = data.success ? 'bi bi-check-circle-fill text-success' : 'bi bi-exclamation-circle-fill text-warning';
                title.innerText = data.success ? 'Berhasil!' : 'Sudah Absen!';
                message.innerText = data.message;
                
                details.classList.remove('d-none');
                document.getElementById('p-name').innerText = data.data.nama_tim;
                document.getElementById('p-event').innerText = data.data.event.nama_event;
                
                const paymentBadge = document.getElementById('p-payment');
                paymentBadge.innerText = data.data.payment_status.toUpperCase();
                paymentBadge.className = 'badge rounded-pill fw-bold ' + (data.data.payment_status === 'settlement' ? 'bg-success' : 'bg-warning');
                
                document.getElementById('p-time').innerText = data.success ? new Date().toLocaleTimeString('id-ID') : (data.data.attended_at ? new Date(data.data.attended_at).toLocaleTimeString('id-ID') : '-');

                // Audio feedback
                const sound = data.success ? 'https://assets.mixkit.co/active_storage/sfx/2568/2568-preview.mp3' : 'https://assets.mixkit.co/active_storage/sfx/2573/2573-preview.mp3';
                new Audio(sound).play().catch(e => {});
            } else {
                statusDiv.className = 'alert alert-danger border-0 rounded-4 p-4 mb-4 shadow-sm animate__animated animate__headShake';
                icon.className = 'bi bi-exclamation-triangle-fill text-danger';
                title.innerText = 'Gagal!';
                message.innerText = data.message;
                details.classList.add('d-none');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            resetScanner();
        });
    }

    function resetScanner() {
        document.getElementById('result-container').classList.add('d-none');
        isScanning = true;
        startScanner();
    }

    function startScanner() {
        // Cek apakah koneksi aman (HTTPS atau localhost)
        if (!window.isSecureContext) {
            Swal.fire({
                icon: 'warning',
                title: 'Akses Kamera Terblokir',
                text: 'Kamera hanya dapat diakses melalui koneksi aman (HTTPS) atau localhost. Pastikan Anda mengakses website menggunakan protokol HTTPS atau menggunakan tunnel/jaringan yang terenkripsi.',
                confirmButtonColor: '#0d6efd'
            });
            
            const readerDiv = document.getElementById('reader');
            if (readerDiv) {
                readerDiv.innerHTML = `<div class="p-5 text-center text-dark">
                    <i class="bi bi-shield-slash-fill text-warning display-4 mb-3 d-block"></i>
                    <h5 class="fw-bold">Akses Kamera Membutuhkan HTTPS</h5>
                    <p class="small text-muted mb-0">Browser Anda memblokir sensor kamera karena halaman ini diakses menggunakan HTTP biasa. Gunakan HTTPS atau jalankan di localhost.</p>
                </div>`;
            }
            return;
        }

        html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { 
                fps: 20, 
                qrbox: function(viewfinderWidth, viewfinderHeight) {
                    let minEdgePercentage = 0.7; // 70% dari sisi terkecil
                    let minEdgeSize = Math.min(viewfinderWidth, viewfinderHeight);
                    let qrboxSize = Math.floor(minEdgeSize * minEdgePercentage);
                    return {
                        width: qrboxSize,
                        height: qrboxSize
                    };
                },
                aspectRatio: 1.0
            }
        );
        html5QrcodeScanner.render(onScanSuccess);
    }

    window.onload = startScanner;
</script>
@endpush
@endsection
