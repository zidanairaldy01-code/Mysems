@extends('layouts.app')

@section('title', 'Tiket Berhasil - ' . $registration->event->nama_event)

@section('content')
<div class="row justify-content-center py-5">
    <div class="col-lg-5">
        <div class="ticket-wrapper animate__animated animate__zoomIn">
            <!-- Ticket Header -->
            <div class="ticket-header bg-primary text-white p-4 rounded-top-5 text-center position-relative overflow-hidden">
                <div class="bg-pattern"></div>
                <div class="bg-success bg-opacity-25 rounded-circle p-3 d-inline-block mb-3 shadow-sm border border-white border-opacity-25">
                    <i class="bi bi-check-lg fs-1"></i>
                </div>
                <h3 class="fw-bold mb-1">Pendaftaran Berhasil</h3>
                <p class="mb-0 opacity-75 small">ID Registrasi: #REG-{{ $registration->id }}</p>
                
                <!-- Ticket Notches -->
                <div class="notch notch-left"></div>
                <div class="notch notch-right"></div>
            </div>

            <!-- Ticket Body (QR Code) -->
            <div class="ticket-body bg-white p-4 text-center border-start border-end">
                <p class="text-muted small fw-bold mb-4 tracking-widest text-uppercase">QR Code Kehadiran</p>
                <div class="qr-wrapper p-3 bg-light rounded-4 d-inline-block shadow-sm mb-4 border">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=220x220&data={{ $registration->qr_code }}" 
                         alt="QR Code" 
                         class="img-fluid rounded-3"
                         style="width: 220px; height: 220px;">
                </div>
                <h4 class="fw-black text-main-custom mb-0 letter-spacing-lg">{{ $registration->qr_code }}</h4>
                <p class="text-muted-custom x-small mt-2">Tunjukkan QR ini pada petugas di lokasi</p>
                
                <div class="dotted-line my-4"></div>

                <!-- Participant Info -->
                <div class="row g-3 text-start px-3 px-md-4">
                    <div class="col-6">
                        <small class="text-muted-custom d-block x-small uppercase fw-bold">Nama Peserta</small>
                        <span class="fw-bold text-main-custom">{{ $registration->nama_kapten }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted-custom d-block x-small uppercase fw-bold">Event</small>
                        <span class="fw-bold text-main-custom">{{ $registration->event->nama_event }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted-custom d-block x-small uppercase fw-bold">Tanggal</small>
                        <span class="fw-bold text-main-custom">{{ \Carbon\Carbon::parse($registration->event->tanggal_event)->format('d M Y') }}</span>
                    </div>
                    <div class="col-6">
                        <small class="text-muted-custom d-block x-small uppercase fw-bold">Status Bayar</small>
                        <span class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-2 py-1 x-small fw-bold">Lunas</span>
                    </div>
                </div>
            </div>

            <!-- Ticket Footer -->
            <div class="ticket-footer bg-light p-4 rounded-bottom-5 border border-top-0 text-center position-relative">
                <div class="notch notch-left-top"></div>
                <div class="notch notch-right-top"></div>
                
                <div class="alert alert-warning border-0 rounded-4 small mb-4 py-2 px-3 text-start d-flex align-items-center">
                    <i class="bi bi-exclamation-circle-fill me-2 fs-5"></i>
                    <div>Jangan bagikan QR Code ini kepada siapapun untuk keamanan tiket Anda.</div>
                </div>

                <div class="d-grid gap-2 no-print">
                    <button onclick="window.print()" class="btn btn-primary rounded-pill py-2 fw-bold shadow-sm">
                        <i class="bi bi-download me-2"></i> Simpan Tiket (PDF/Cetak)
                    </button>
                    <a href="{{ route('public.event.index') }}" class="btn btn-link text-decoration-none text-muted small">Kembali ke Beranda</a>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
<style>
    .ticket-wrapper {
        filter: drop-shadow(0 15px 30px rgba(0,0,0,0.1));
    }
    
    .bg-pattern {
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background-image: radial-gradient(rgba(255,255,255,0.1) 1px, transparent 1px);
        background-size: 20px 20px;
        opacity: 0.5;
    }

    .notch {
        width: 30px;
        height: 30px;
        background: #f8f9fa; /* Matches page background */
        border-radius: 50%;
        position: absolute;
        z-index: 5;
    }
    [data-theme='dark'] .notch { background: #0f172a; }

    .notch-left { left: -15px; bottom: -15px; }
    .notch-right { right: -15px; bottom: -15px; }
    .notch-left-top { left: -15px; top: -15px; }
    .notch-right-top { right: -15px; top: -15px; }

    .dotted-line {
        border-top: 2px dotted #dee2e6;
        height: 0;
    }

    .fw-black { font-weight: 900; }
    .tracking-widest { letter-spacing: 0.2em; }
    .x-small { font-size: 0.7rem; }
    .uppercase { text-transform: uppercase; }

    @media print {
        @page {
            size: A4;
            margin: 0;
        }
        .no-print, nav, footer, #top-loading-bar { display: none !important; }
        body { background: white !important; margin: 0; padding: 0; }
        .row { margin: 0; }
        .col-lg-5 { width: 100% !important; max-width: 100% !important; flex: 0 0 100% !important; }
        .ticket-wrapper { 
            filter: none; 
            margin: 20mm auto !important; 
            width: 140mm !important; /* Proper ticket width on A4 */
            box-shadow: none !important;
            border: 1px solid #eee !important;
        }
        .ticket-header { -webkit-print-color-adjust: exact; print-color-adjust: exact; background-color: #0d6efd !important; }
        .ticket-body, .ticket-footer { -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    }
</style>
@endpush
@endsection
