@extends('layouts.app')

@section('title', 'Pembayaran Pendaftaran - ' . $event->nama_event)

@section('content')
    <div class="row justify-content-center">
        <div class="col-lg-5">
            <div class="card border-0 shadow-sm rounded-4 text-center p-4">
                <div class="mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle d-inline-flex align-items-center justify-content-center mb-3"
                        style="width: 80px; height: 80px;">
                        <i class="bi bi-wallet2 fs-1"></i>
                    </div>
                    <h4 class="fw-bold">Satu Langkah Lagi!</h4>
                    <p class="text-muted">Silakan selesaikan pembayaran untuk mengaktifkan tim Anda di klasemen.</p>
                </div>

                <div class="bg-light rounded-4 p-4 mb-4 text-start">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Event</span>
                        <span class="fw-bold">{{ $event->nama_event }}</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted">Nama Tim</span>
                        <span class="fw-bold">{{ $registration->nama_tim }}</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between mt-2">
                        <span class="text-muted">Total Bayar</span>
                        <h5 class="fw-bold text-primary mb-0">Rp {{ number_format($event->harga_pendaftaran, 0, ',', '.') }}
                        </h5>
                    </div>
                </div>

                <a href="{{ $redirectUrl }}"
                    onclick="event.preventDefault(); let url = this.href; Swal.fire({ title: 'Lanjutkan Pembayaran?', text: 'Halaman pembayaran akan dibuka di tab baru.', icon: 'question', showCancelButton: true, confirmButtonColor: '#0d6efd', confirmButtonText: 'Ya, Lanjutkan', cancelButtonText: 'Batal' }).then((result) => { if (result.isConfirmed) { window.open(url, '_blank'); } })"
                    class="btn btn-primary w-100 rounded-pill py-3 fw-bold shadow-sm mb-3">
                    <i class="bi bi-box-arrow-up-right me-2"></i>Bayar Sekarang
                </a>

                <div class="mt-2 mb-4">
                    <p class="text-muted small mb-3">Setelah menyelesaikan pembayaran di tab baru, silakan klik tombol di
                        bawah untuk verifikasi status pendaftaran Anda.</p>
                    <a href="{{ route('public.event.check', $registration->id) }}"
                        class="btn btn-outline-success w-100 rounded-pill py-2 fw-bold">
                        <i class="bi bi-arrow-repeat me-2"></i>Sudah Bayar? Cek Status
                    </a>
                </div>

                <p class="small text-muted mb-0">Pembayaran aman didukung oleh <strong>Midtrans</strong></p>
            </div>
        </div>
    </div>

    {{-- Skrip Midtrans Snap (Opsional jika ingin tetap ada backup popup) --}}
    {{--
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ env('MIDTRANS_CLIENT_KEY') }}"></script>
    --}}
@endsection