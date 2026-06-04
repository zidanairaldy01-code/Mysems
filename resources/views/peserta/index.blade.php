@extends('layouts.peserta')

@section('title', 'Dashboard Peserta - MySMES')

@section('content')
{{-- Header Dashboard --}}
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white border-0 shadow-sm rounded-4 overflow-hidden position-relative">
            <div class="position-absolute top-0 end-0 h-100 w-50" style="background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1)); transform: skewX(-20deg) translateX(20%);"></div>
            <div class="card-body p-4 p-md-5 position-relative z-1">
                <div class="row align-items-center">
                    <div class="col-md-8">
                        <h2 class="fw-bold mb-2">Halo, {{ Auth::user()->nama }}! 👋</h2>
                        <p class="mb-4 opacity-75 fs-5">Pantau status pendaftaranmu dan temukan event menarik lainnya untuk diikuti!</p>
                        <a href="{{ route('peserta.event.index') }}" class="btn btn-light text-primary fw-bold px-4 py-2 rounded-pill shadow-sm">Jelajahi Event</a>
                    </div>
                    <div class="col-md-4 text-center d-none d-md-block">
                        <i class="bi bi-person-bounding-box" style="font-size: 8rem; opacity: 0.8;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Statistik Ringkas --}}
<div class="row g-4 mb-4">
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 dashboard-card h-100">
            <div class="card-body p-4">
                <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 d-inline-block mb-3">
                    <i class="bi bi-calendar-event fs-4"></i>
                </div>
                <h6 class="text-muted mb-1 fw-semibold">Event Diikuti</h6>
                <h3 class="fw-bold mb-0">{{ $totalEventDiikuti }}</h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 dashboard-card h-100">
            <div class="card-body p-4">
                <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 d-inline-block mb-3">
                    <i class="bi bi-patch-check fs-4"></i>
                </div>
                <h6 class="text-muted mb-1 fw-semibold">Sudah Lunas</h6>
                <h3 class="fw-bold mb-0">{{ $totalLunas }}</h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 dashboard-card h-100">
            <div class="card-body p-4">
                <div class="bg-warning bg-opacity-10 text-warning rounded-circle p-3 d-inline-block mb-3">
                    <i class="bi bi-hourglass-split fs-4"></i>
                </div>
                <h6 class="text-muted mb-1 fw-semibold">Menunggu</h6>
                <h3 class="fw-bold mb-0">{{ $totalPending }}</h3>
            </div>
        </div>
    </div>
    <div class="col-6 col-lg-3">
        <div class="card border-0 shadow-sm rounded-4 dashboard-card h-100">
            <div class="card-body p-4">
                <div class="bg-info bg-opacity-10 text-info rounded-circle p-3 d-inline-block mb-3">
                    <i class="bi bi-fire fs-4"></i>
                </div>
                <h6 class="text-muted mb-1 fw-semibold">Event Aktif</h6>
                <h3 class="fw-bold mb-0">{{ $totalEventAktif }}</h3>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    {{-- Riwayat Pendaftaran Terbaru --}}
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold mb-0">Riwayat Pendaftaranmu</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3 border-0">Event</th>
                                <th class="py-3 border-0 text-center">Status</th>
                                <th class="py-3 border-0 text-end pe-4">Tanggal Daftar</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($myRegistrations as $reg)
                            <tr>
                                <td class="ps-4">
                                    <div class="fw-bold text-dark">{{ $reg->event?->nama_event ?? 'Event Dihapus' }}</div>
                                    <div class="small text-muted">{{ $reg->nama_tim }}</div>
                                </td>
                                <td class="text-center">
                                    @if($reg->payment_status == 'settlement')
                                        <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Lunas</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">Pending</span>
                                    @endif
                                </td>
                                <td class="text-end pe-4 small text-muted fw-bold">
                                    {{ $reg->created_at->diffForHumans() }}
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center py-5 text-muted">
                                    Kamu belum mendaftar di event manapun.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    {{-- Rekomendasi Event --}}
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold mb-0">Event Mendatang</h5>
            </div>
            <div class="card-body p-3">
                @foreach($recommendedEvents as $event)
                <div class="d-flex align-items-center mb-3 p-2 rounded-3 hover-bg">
                    <div class="flex-shrink-0">
                        <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-2 text-center" style="width: 50px;">
                            <div class="fw-bold lh-1">{{ \Carbon\Carbon::parse($event->tanggal_event)->format('d') }}</div>
                            <div class="small text-uppercase fw-bold">{{ \Carbon\Carbon::parse($event->tanggal_event)->format('M') }}</div>
                        </div>
                    </div>
                    <div class="ms-3 flex-grow-1 overflow-hidden">
                        <h6 class="mb-0 fw-bold text-dark text-truncate">{{ $event->nama_event }}</h6>
                        <small class="text-muted d-block text-truncate">{{ $event->lokasi }}</small>
                    </div>
                    <a href="{{ route('peserta.event.index') }}" class="btn btn-sm btn-light rounded-circle">
                        <i class="bi bi-chevron-right"></i>
                    </a>
                </div>
                @endforeach
                <a href="{{ route('peserta.event.index') }}" class="btn btn-outline-primary btn-sm w-100 rounded-pill mt-2">Lihat Semua</a>
            </div>
        </div>
    </div>
</div>

<style>
    .dashboard-card {
        transition: transform 0.3s ease;
    }
    .dashboard-card:hover {
        transform: translateY(-5px);
    }
    .hover-bg:hover {
        background-color: #f8f9fa;
        cursor: pointer;
    }
    .fw-900 { font-weight: 900; }
</style>
@endsection
