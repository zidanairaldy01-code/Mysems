@extends('layouts.app')

@section('title', $event->nama_event . ' - MySMES')

@section('content')
<div class="container py-4">
    <nav aria-label="breadcrumb" class="mb-4">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('public.event.index') }}" class="text-decoration-none">Event Sekolah</a></li>
            <li class="breadcrumb-item active" aria-current="page">Detail Event</li>
        </ol>
    </nav>

    <div class="row g-4">
        {{-- Sisi Kiri: Foto & Deskripsi --}}
        <div class="col-lg-8">
            <div class="card border-0 shadow-sm rounded-4 overflow-hidden mb-4">
                @if($event->foto_event)
                     <img src="{{ route('public.image', ['directory' => 'events', 'filename' => basename($event->foto_event)]) }}" 
                 class="img-fluid w-100 object-fit-cover" style="max-height: 450px;" 
                 alt="{{ $event->nama_event }}"
                 oncontextmenu="return false;" 
                 draggable="false">
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
                            <small class="text-muted d-block">Waktu</small>
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
                            <span class="text-muted small">{{ $event->type_normalized === 'umum' ? 'Kuota Terisi' : 'Kuota Tim Terisi' }}</span>
                            <span class="fw-bold small">{{ $pendaftarLunas }} / {{ $event->slot_tim }} {{ $event->type_normalized === 'umum' ? 'Orang' : 'Tim' }}</span>
                        </div>
                        <div class="progress rounded-pill" style="height: 10px;">
                            <div class="progress-bar progress-bar-striped progress-bar-animated bg-primary" role="progressbar" style="width: {{ ($pendaftarLunas / $event->slot_tim) * 100 }}%"></div>
                        </div>
                    </div>

                    @if($event->status == 2 || \Carbon\Carbon::parse($event->tanggal_event)->isPast())
                        <div class="alert alert-secondary border-0 rounded-4 text-center py-3 mb-0">
                            <i class="bi bi-info-circle me-2"></i> Event ini telah berakhir.
                        </div>
                        @if($event->type_normalized !== 'umum')
                        <a href="{{ route('public.klasemen.index', ['event_id' => $event->id, 'is_archive' => 1]) }}" class="btn btn-outline-secondary w-100 rounded-pill mt-3 fw-bold py-2">
                            <i class="bi bi-trophy me-2"></i> Lihat Hasil Akhir
                        </a>
                        @endif
                    @elseif($isFull)
                        <div class="alert alert-danger border-0 rounded-4 text-center py-3 mb-0">
                            <i class="bi bi-exclamation-triangle me-2"></i> Kuota pendaftaran penuh!
                        </div>
                    @else
                        <a href="{{ route('public.event.register', $event->uuid) }}" class="btn btn-primary w-100 rounded-pill py-3 fw-black shadow-sm">
                            DAFTAR SEKARANG
                        </a>
                        <p class="text-center small text-muted mt-3 mb-0">Pendaftaran ditutup 1 hari sebelum event dimulai.</p>
                    @endif
                </div>
            </div>

            {{-- Card Info Tambahan --}}
            <div class="card border-0 shadow-sm rounded-4 bg-light">
                <div class="card-body p-4 text-center">
                    <h6 class="fw-bold mb-2">Butuh Bantuan?</h6>
                    <p class="small text-muted mb-3">Hubungi panitia melalui email atau media sosial sekolah.</p>
                    <div class="d-flex justify-content-center gap-2">
                        <a href="#" class="btn btn-sm btn-white border rounded-circle"><i class="bi bi-envelope"></i></a>
                        <a href="#" class="btn btn-sm btn-white border rounded-circle"><i class="bi bi-whatsapp"></i></a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    .fw-black { font-weight: 900; }
    .btn-white { background-color: #fff; }
    .breadcrumb-item + .breadcrumb-item::before { content: "›"; font-size: 1.2rem; line-height: 1; vertical-align: middle; }
</style>
@endsection
