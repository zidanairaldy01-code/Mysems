@extends('layouts.panitia')

@section('title', $event->nama_event . ' - Detail Event (Panitia)')

@section('content')
<div class="mb-4">
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('panitia.event.index') }}" class="text-decoration-none">Event Saya</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detail Event</li>
        </ol>
    </nav>
</div>

<div class="row g-4">
    {{-- Sisi Kiri: Foto & Deskripsi --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
            @if($event->foto_event)
                <img src="{{ asset($event->foto_event) }}" class="img-fluid w-100 object-fit-cover" style="max-height: 450px;" alt="{{ $event->nama_event }}">
            @else
                <div class="bg-primary bg-opacity-10 text-primary d-flex align-items-center justify-content-center" style="height: 300px;">
                    <i class="bi bi-image fs-1 opacity-50"></i>
                </div>
            @endif
            
            <div class="card-body p-4 p-md-5">
                <div class="d-flex align-items-center mb-3">
                    <span class="badge bg-primary rounded-pill px-3 me-2">{{ $event->nama_panitia }}</span>
                    <span class="text-muted small"><i class="bi bi-person-circle me-1"></i> Diunggah oleh {{ $event->user->nama }}</span>
                </div>
                
                <h1 class="fw-black mb-4">{{ $event->nama_event }}</h1>
                
                <h5 class="fw-bold mb-3">Deskripsi Event</h5>
                <div class="text-muted lh-lg" style="white-space: pre-line;">
                    {{ $event->deskripsi ?? 'Tidak ada deskripsi lengkap untuk event ini.' }}
                </div>
            </div>
        </div>
    </div>

    {{-- Sisi Kanan: Info Pendaftaran & Sidebar --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 mb-4 sticky-top" style="top: 2rem;">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4">Detail Pelaksanaan</h5>
                
                <div class="d-flex mb-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-3" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-calendar3 fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Tanggal</small>
                        <span class="fw-bold">{{ \Carbon\Carbon::parse($event->tanggal_event)->format('l, d F Y') }}</span>
                    </div>
                </div>

                <div class="d-flex mb-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-3" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-clock fs-5"></i>
                    </div>
                    <div>
                        <small class="text- বিদ্রোহীদের d-block">Waktu</small>
                        <span class="fw-bold">{{ \Carbon\Carbon::parse($event->jam_event)->format('H:i') }} WIB</span>
                    </div>
                </div>

                <div class="d-flex mb-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-3" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-geo-alt fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Lokasi</small>
                        <span class="fw-bold">{{ $event->lokasi }}</span>
                    </div>
                </div>

                <div class="d-flex mb-4">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 me-3" style="width: 45px; height: 45px; display: flex; align-items: center; justify-content: center;">
                        <i class="bi bi-cash-stack fs-5"></i>
                    </div>
                    <div>
                        <small class="text-muted d-block">Biaya Pendaftaran</small>
                        <span class="fw-bold {{ $event->harga_pendaftaran == 0 ? 'text-success' : 'text-primary' }}">
                            {{ $event->harga_pendaftaran == 0 ? 'GRATIS' : 'Rp ' . number_format($event->harga_pendaftaran, 0, ',', '.') }}
                        </span>
                    </div>
                </div>

                <hr class="my-4 opacity-50">

                <div class="mb-4">
                    <div class="d-flex justify-content-between mb-2">
                        <span class="text-muted small">Kuota Terisi</span>
                        <span class="fw-bold small">{{ $pendaftarLunas }} / {{ $event->slot_tim }} Tim</span>
                    </div>
                    <div class="progress rounded-pill" style="height: 10px;">
                        <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: {{ ($pendaftarLunas / $event->slot_tim) * 100 }}%"></div>
                    </div>
                </div>

                @if($event->status == 2 || \Carbon\Carbon::parse($event->tanggal_event)->isPast())
                    <div class="alert alert-secondary border-0 rounded-4 text-center py-3 mb-0">
                        <i class="bi bi-info-circle me-2"></i> Event ini telah berakhir (Arsip).
                    </div>
                    <a href="{{ route('panitia.klasemen.index', ['event_id' => $event->id]) }}" class="btn btn-outline-secondary w-100 rounded-pill mt-3 fw-bold py-2">
                        <i class="bi bi-trophy me-2"></i> Lihat Hasil Akhir
                    </a>
                @elseif($event->status == 0)
                    <div class="alert alert-warning border-0 rounded-4 text-center py-3 mb-0">
                        <i class="bi bi-hourglass-split me-2"></i> Menunggu persetujuan Admin
                    </div>
                @elseif($event->status == 3)
                    <div class="alert alert-danger border-0 rounded-4 py-3 mb-0">
                        <div class="text-center fw-bold">
                            <i class="bi bi-x-circle me-2"></i> Event ini ditolak oleh Admin
                        </div>
                        @if($event->alasan_ditolak)
                            <hr class="my-2 border-danger opacity-25">
                            <div class="small">
                                <strong class="text-danger">Alasan Penolakan:</strong>
                                <p class="mb-0 text-dark mt-1">{{ $event->alasan_ditolak }}</p>
                            </div>
                        @endif
                    </div>
                @else
                    <div class="alert alert-success border-0 rounded-4 text-center py-3 mb-0">
                        <i class="bi bi-check-circle me-2"></i> Event Sedang Berjalan
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .breadcrumb-item + .breadcrumb-item::before { content: "›"; font-size: 1.2rem; line-height: 1; vertical-align: middle; }
</style>
@endsection