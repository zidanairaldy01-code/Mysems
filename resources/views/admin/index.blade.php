@extends('layouts.admin')

@section('title', 'Dashboard Admin - MySEMS')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <h2 class="fw-bold text-dark">Dashboard Admin</h2>
        <p class="text-muted">Selamat datang kembali, <strong>{{ Auth::user()->nama }}</strong>! Berikut adalah ringkasan aktivitas sistem hari ini.</p>
    </div>
</div>

<!-- Stat Cards -->
<div class="row g-4 mb-4">
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 transition-hover">
            <div class="d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 p-3 rounded-4 me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bi bi-calendar-event text-primary fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small fw-bold text-uppercase">Total Event</h6>
                    <h4 class="fw-bold mb-0 text-primary">{{ number_format($totalEvent) }}</h4>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-12 col-sm-6 col-xl-3">
        <div class="card border-0 shadow-sm rounded-4 p-3 h-100 transition-hover">
            <div class="d-flex align-items-center">
                <div class="bg-success bg-opacity-10 p-3 rounded-4 me-3 d-flex align-items-center justify-content-center" style="width: 60px; height: 60px;">
                    <i class="bi bi-people text-success fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-1 small fw-bold text-uppercase">Total Panitia</h6>
                    <h4 class="fw-bold mb-0 text-success">{{ number_format($totalPanitia) }}</h4>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4">
    <!-- Tabel Event Mendatang -->
    <div class="col-12 col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
                <h5 class="fw-bold mb-0">Event Mendatang</h5>
                <a href="{{ route('admin.event.index') }}" class="btn btn-sm btn-light rounded-pill px-3 shadow-sm transition-hover">Lihat Semua</a>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 text-nowrap">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4 py-3">Nama Event</th>
                                <th class="py-3">Tanggal</th>
                                <th class="py-3">Penyelenggara</th>
                                <th class="text-center pe-4 py-3">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($upcomingEvents as $event)
                            <tr>
                                <td class="ps-4 py-3">
                                    <div class="fw-bold text-dark">{{ $event->nama_event }}</div>
                                    <small class="text-muted"><i class="bi bi-geo-alt me-1"></i>{{ $event->lokasi }}</small>
                                </td>
                                <td><span class="fw-bold">{{ \Carbon\Carbon::parse($event->tanggal_event)->format('d M Y') }}</span></td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3 py-2">
                                        {{ $event->nama_panitia }}
                                    </span>
                                </td>
                                <td class="text-center pe-4">
                                    <a href="{{ route('admin.event.index') }}" class="btn btn-sm btn-outline-primary rounded-pill px-3 shadow-sm transition-hover">
                                        <i class="bi bi-eye"></i> Detail
                                    </a>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-5 text-muted">
                                    <i class="bi bi-calendar-x fs-1 d-block mb-2 opacity-50"></i>
                                    Belum ada event yang dijadwalkan.
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Aktivitas Pendaftaran Terbaru -->
    <div class="col-12 col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold mb-0">Pendaftaran Terbaru</h5>
            </div>
            <div class="card-body">
                @forelse($recentRegistrations as $reg)
                <div class="d-flex mb-3 align-items-center p-3 border rounded-4 transition-hover bg-light bg-opacity-50">
                    <div class="p-3 rounded-circle me-3 flex-shrink-0 d-flex justify-content-center align-items-center {{ $reg->payment_status == 'settlement' ? 'bg-success bg-opacity-10' : 'bg-warning bg-opacity-10' }}" style="width: 50px; height: 50px;">
                        <i class="bi {{ $reg->payment_status == 'settlement' ? 'bi-check-lg text-success' : 'bi-hourglass-split text-warning' }} fs-5"></i>
                    </div>
                    <div class="flex-grow-1 min-w-0">
                        <h6 class="mb-1 fw-bold text-dark text-truncate">Tim {{ $reg->nama_tim }}</h6>
                        <p class="mb-0 text-muted small text-truncate">Event: <span class="fw-medium text-dark">{{ $reg->event?->nama_event ?? 'Event Dihapus' }}</span></p>
                        <small class="text-muted fw-bold" style="font-size: 0.75rem;"><i class="bi bi-clock me-1"></i>{{ $reg->created_at->diffForHumans() }}</small>
                    </div>
                </div>
                @empty
                <div class="text-center py-5">
                    <i class="bi bi-inbox text-muted opacity-25" style="font-size: 3.5rem;"></i>
                    <p class="text-muted mt-3">Belum ada pendaftaran baru.</p>
                </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<style>
    .transition-hover {
        transition: all 0.3s ease;
    }
    .transition-hover:hover {
        transform: translateY(-3px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.08) !important;
    }
    /* Mencegah teks terpotong di layar kecil */
    .text-truncate {
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }
</style>
@endsection