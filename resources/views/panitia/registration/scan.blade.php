@extends('layouts.panitia')

@section('title', 'Scan Kehadiran - Panitia')

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-lg-8">
        <div class="text-center mb-4">
            <h2 class="fw-bold text-primary">Scan QR Kehadiran</h2>
            <p class="text-muted">Pastikan peserta menunjukkan QR Code resmi dari MySMES.</p>
        </div>

        <div class="card border-0 shadow-lg rounded-5 overflow-hidden scanner-card">
            <div class="card-body p-0">
                <div class="row g-0">
                    <div class="col-md-12 p-3 p-md-4">
                        <div id="scanner-wrapper" class="rounded-4 shadow-inner border mx-auto p-2 p-md-3" style="max-width: 560px;">
                            
                            <div id="reader" class="rounded-4 overflow-hidden" style="background: #090b12;"></div>

                            <div class="text-center mt-3">
                                <p class="small text-muted mb-0">Arahkan kamera ke QR Code peserta, letakkan tepat di dalam bingkai.</p>
                            </div>
                        </div>
                    </div>

                    <div id="result-container" class="col-md-12 p-4 pt-0 d-none animate__animated animate__fadeInUp">
                        <hr class="opacity-10 mb-4">
                        
                        <div id="scan-status" class="alert border-0 rounded-4 p-4 mb-4 text-center">
                            <div class="mb-3">
                                <i id="status-icon" class="bi display-4"></i>
                            </div>
                            <h4 id="status-title" class="fw-bold mb-1"></h4>
                            <p id="status-message" class="mb-0 small"></p>
                        </div>
                        
                        <div id="participant-details" class="details-box rounded-4 p-4 d-none border bg-light bg-opacity-50">
                            <div class="row g-4">
                                <div class="col-sm-6">
                                    <label class="small text-muted d-block mb-1">Nama Peserta</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-person text-primary"></i>
                                        <span id="p-name" class="fw-bold"></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="small text-muted d-block mb-1">Event</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-tag text-primary"></i>
                                        <span id="p-event" class="fw-bold"></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="small text-muted d-block mb-1">WhatsApp</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-whatsapp text-success"></i>
                                        <span id="p-wa" class="fw-bold"></span>
                                    </div>
                                </div>
                                <div class="col-sm-6">
                                    <label class="small text-muted d-block mb-1">Waktu Kehadiran</label>
                                    <div class="d-flex align-items-center gap-2">
                                        <i class="bi bi-clock text-warning"></i>
                                        <span id="p-time" class="fw-bold"></span>
                                    </div>
                                </div>
                                <div class="col-12 border-top pt-3">
                                    <div class="d-flex justify-content-between align-items-center">
                                        <span class="small text-muted">Status Pembayaran:</span>
                                        <span id="p-payment" class="badge rounded-pill px-3 py-2 fw-bold text-uppercase"></span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="text-center mt-4">
                            <button onclick="resetScanner()" class="btn btn-primary btn-lg rounded-pill px-5 fw-bold shadow-sm">
                                <i class="bi bi-camera me-2"></i> Scan Lanjut
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
    .scanner-card {
        background: rgba(255, 255, 255, 0.95);
        border: 1px solid rgba(13, 110, 253, 0.12);
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.08);
        backdrop-filter: blur(18px);
    }
    [data-theme='dark'] .scanner-card {
        background: rgba(15, 23, 42, 0.9);
        border-color: rgba(71, 85, 105, 0.3);
        box-shadow: 0 24px 60px rgba(15, 23, 42, 0.25);
    }

    /* Container utama reader */
    #reader {
        border: none !important;
        width: 100% !important;
    }

    /* Feed video agar presisi memotong sudut melengkung */
    #reader video {
        border-radius: 16px;
        object-fit: cover;
    }

    /* Area sebelum izin kamera diberikan */
    #reader__dashboard_section {
        padding: 2rem 1rem !important;
        width: 100% !important;
    }

    #reader__dashboard_section_csr {
        display: flex !important;
        flex-direction: column !important;
        justify-content: center !important;
        align-items: center !important;
        padding: 2rem 0 !important;
    }

    /* Tombol Izinkan Kamera */
    #reader__dashboard_section_csr button {
        min-width: 200px !important;
        background-color: #0d6efd !important;
        border: none !important;
        border-radius: 50px !important;
        padding: 12px 28px !important;
        color: white !important;
        font-weight: 600 !important;
        font-size: 1rem !important;
        box-shadow: 0 8px 20px rgba(13, 110, 253, 0.3) !important;
        cursor: pointer !important;
        transition: all 0.3s ease !important;
    }

    #reader__dashboard_section_csr button:hover {
        background-color: #0b5ed7 !important;
        transform: translateY(-2px);
    }

    /* Sembunyikan teks standar library yang kurang rapi */
    #reader__dashboard_section_csr span {
        display: none !important; 
    }

    #reader__dashboard_section_swaplink {
        color: #3b82f6 !important;
        text-decoration: none !important;
        margin-top: 15px !important;
        display: inline-block !important;
    }

    /* INJEKSI EFEK LASER LANGSUNG KE DALAM REGION SCAN BISA LEBIH PRESISI */
    #reader__scan_region {
        position: relative !important;
    }

    /* Kita buat garis laser virtual di dalam area scan bawaan library */
    #reader__scan_region::after {
        content: "";
        position: absolute;
        top: 50%;
        left: 15%;
        width: 70%;
        height: 2.5px;
        background: #3b82f6;
        box-shadow: 0 0 15px 2px rgba(59, 130, 246, 0.9);
        animation: precise-scanning 2s ease-in-out infinite;
        z-index: 999;
        pointer-events: none;
    }

    @keyframes precise-scanning {
        0%, 100% { transform: translateY(-70px); }
        50% { transform: translateY(70px); }
    }

    /* Penyesuaian jarak vertikal laser khusus layar HP yang framenya mengecil */
    @media (max-width: 575.98px) {
        @keyframes precise-scanning {
            0%, 100% { transform: translateY(-50px); }
            50% { transform: translateY(50px); }
        }
    }

    /* Alert & Details Styling */
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

    .details-box { background: rgba(0, 0, 0, 0.03); }
    [data-theme='dark'] .details-box {
        background: rgba(255, 255, 255, 0.03);
        border-color: rgba(255, 255, 255, 0.1) !important;
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
        
        fetch("{{ route('panitia.registration.scan.process') }}", {
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
                    ? 'alert alert-success border-0 rounded-4 p-4 mb-4 animate__animated animate__bounceIn'
                    : 'alert alert-warning border-0 rounded-4 p-4 mb-4 animate__animated animate__pulse';
                
                icon.className = data.success ? 'bi bi-check-circle-fill' : 'bi bi-exclamation-circle-fill';
                title.innerText = data.success ? 'BERHASIL!' : 'SUDAH ABSEN!';
                message.innerText = data.message;
                
                details.classList.remove('d-none');
                document.getElementById('p-name').innerText = data.data.nama_tim;
                document.getElementById('p-event').innerText = data.data.event.nama_event;
                document.getElementById('p-wa').innerText = '+62 ' + data.data.nomor_wa;
                document.getElementById('p-time').innerText = data.success ? new Date().toLocaleTimeString('id-ID') : (data.data.attended_at ? new Date(data.data.attended_at).toLocaleTimeString('id-ID') : '-');

                const paymentBadge = document.getElementById('p-payment');
                paymentBadge.innerText = data.data.payment_status;
                paymentBadge.className = 'badge rounded-pill px-3 py-2 fw-bold text-uppercase ' + (data.data.payment_status === 'settlement' ? 'bg-success' : 'bg-warning text-dark');
                
                const sound = data.success ? 'https://assets.mixkit.co/active_storage/sfx/2568/2568-preview.mp3' : 'https://assets.mixkit.co/active_storage/sfx/2573/2573-preview.mp3';
                new Audio(sound).play().catch(e => {});
            } else {
                statusDiv.className = 'alert alert-danger border-0 rounded-4 p-4 mb-4 animate__animated animate__shakeX';
                icon.className = 'bi bi-x-circle-fill';
                title.innerText = 'GAGAL!';
                message.innerText = data.message;
                details.classList.add('d-none');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            Swal.fire('Error', 'Kesalahan koneksi server.', 'error');
            resetScanner();
        });
    }

    function resetScanner() {
        document.getElementById('result-container').classList.add('d-none');
        isScanning = true;
        startScanner();
    }

    function updatePermissionButtonText() {
        const permissionButton = document.querySelector('#reader__dashboard_section_csr button');
        if (permissionButton) {
            permissionButton.innerText = 'Izinkan Kamera';
            permissionButton.setAttribute('aria-label', 'Izinkan Kamera');
            return true;
        }
        return false;
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
                readerDiv.innerHTML = `<div class="p-5 text-center text-white">
                    <i class="bi bi-shield-slash-fill text-warning display-4 mb-3 d-block"></i>
                    <h5 class="fw-bold">Akses Kamera Membutuhkan HTTPS</h5>
                    <p class="small text-muted mb-0">Browser Anda memblokir sensor kamera karena halaman ini diakses menggunakan HTTP biasa. Gunakan HTTPS atau jalankan di localhost.</p>
                </div>`;
            }
            return;
        }

        html5QrcodeScanner = new Html5QrcodeScanner(
            "reader", { 
                fps: 15, 
                qrbox: function(viewfinderWidth, viewfinderHeight) {
                    let minEdgePercentage = 0.7; // 70% dari sisi terkecil
                    let minEdgeSize = Math.min(viewfinderWidth, viewfinderHeight);
                    let qrboxSize = Math.floor(minEdgeSize * minEdgePercentage);
                    return {
                        width: qrboxSize,
                        height: qrboxSize
                    };
                },
                aspectRatio: 1.0 // Membantu menjaga rasio container di mobile
            }
        );
        
        html5QrcodeScanner.render(onScanSuccess);

        const permissionButtonInterval = setInterval(() => {
            if (updatePermissionButtonText()) {
                clearInterval(permissionButtonInterval);
            }
        }, 300);
    }

    window.onload = startScanner;
</script>
@endpush
@endsection