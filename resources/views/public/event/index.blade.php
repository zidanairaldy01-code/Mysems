@extends('layouts.app')

@section('title', 'Event Sekolah - MySMES')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold text-main-custom">Event Sekolah</h2>
    <p class="text-muted-custom">Jelajahi turnamen dan event umum aktif atau lihat kembali arsip yang sudah selesai.</p>
</div>

{{-- Saringan ID Sekolah untuk Event Khusus --}}
<div class="card border-0 shadow-sm rounded-4 mb-4" style="background-color: var(--bg-card); border: 1px solid var(--border-color) !important;">
    <div class="card-body p-4">
        <form action="{{ route('public.event.index') }}" method="GET">
            <h5 class="fw-bold text-main-custom mb-3"><i class="bi bi-shield-lock-fill me-2 text-primary"></i>Akses Event Khusus Sekolah</h5>
            <div class="row g-2">
                <div class="col-md-9">
                    <div class="input-group">
                        <span class="input-group-text border-0 ps-3 text-muted-custom" style="background-color: var(--bg-archive); border-top-left-radius: 50px; border-bottom-left-radius: 50px;"><i class="bi bi-search"></i></span>
                        <input type="text" name="school_id" class="form-control border-0 py-3 text-main-custom" style="background-color: var(--bg-archive); border-top-right-radius: 50px; border-bottom-right-radius: 50px;" placeholder="Masukkan ID / NPSN Sekolah Anda..." value="{{ request('school_id') }}">
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary w-100 py-3 rounded-pill fw-bold">
                        <i class="bi bi-funnel-fill me-1"></i> Saring Event
                    </button>
                </div>
            </div>
            @if(request('school_id'))
                <div class="mt-3 d-flex justify-content-between align-items-center">
                    <span class="text-success small fw-semibold"><i class="bi bi-check-circle-fill me-1"></i> Menampilkan event umum & event sekolah ID: <strong>{{ request('school_id') }}</strong></span>
                    <a href="{{ route('public.event.index') }}" class="btn btn-sm btn-link text-decoration-none text-muted-custom p-0"><i class="bi bi-x-circle me-1"></i> Bersihkan Filter</a>
                </div>
            @else
                <div class="form-text text-muted-custom mt-2 small">
                    <i class="bi bi-info-circle me-1"></i> Event khusus sekolah disembunyikan sampai Anda memasukkan ID Sekolah yang valid.
                </div>
            @endif
        </form>
    </div>
</div>

<div class="nav-tabs-wrapper mb-4 overflow-x-auto">
    <ul class="nav nav-pills gap-2 p-1 rounded-pill shadow-sm d-inline-flex custom-nav-pills" id="eventTab" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active rounded-pill px-3 px-md-4 fw-bold text-nowrap" id="tournament-tab" data-bs-toggle="pill" data-bs-target="#tournament-events" type="button" role="tab">
                <i class="bi bi-trophy me-1 me-md-2"></i>Turnamen
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-3 px-md-4 fw-bold text-nowrap" id="umum-tab" data-bs-toggle="pill" data-bs-target="#umum-events" type="button" role="tab">
                <i class="bi bi-calendar-check me-1 me-md-2"></i>Event Umum
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link rounded-pill px-3 px-md-4 fw-bold text-nowrap" id="past-tab" data-bs-toggle="pill" data-bs-target="#past-events" type="button" role="tab">
                <i class="bi bi-archive me-1 me-md-2"></i>Arsip Selesai
            </button>
        </li>
    </ul>
</div>

<div class="tab-content" id="eventTabContent">
    <div class="tab-pane fade show active" id="tournament-events" role="tabpanel">
        <div class="row g-4">
            @php
                $activeTournaments = $activeEvents->filter(fn($event) => $event->type_normalized === 'tournament');
            @endphp
            @forelse($activeTournaments as $event)
                <div class="col-12 col-md-6 col-xl-4">
                    @include('public.event.partials.event_card_public', ['event' => $event])
                </div>
            @empty
                <div class="col-12 text-center py-5 empty-state">
                    <i class="bi bi-calendar-x fs-1 text-muted-custom mb-3 d-block"></i>
                    <h5 class="fw-bold text-main-custom">Belum Ada Turnamen Aktif</h5>
                    <p class="text-muted-custom mb-0">Nantikan turnamen seru lainnya segera!</p>
                </div>
            @endforelse
        </div>
    </div>

    <div class="tab-pane fade" id="umum-events" role="tabpanel">
        <div class="row g-4">
            @php
                $activeUmum = $activeEvents->filter(fn($event) => $event->type_normalized === 'umum');
            @endphp
            @forelse($activeUmum as $event)
                <div class="col-12 col-md-6 col-xl-4">
                    @include('public.event.partials.event_card_public', ['event' => $event])
                </div>
            @empty
                <div class="col-12 text-center py-5 empty-state">
                    <i class="bi bi-calendar-check fs-1 text-muted-custom mb-3 d-block opacity-50"></i>
                    <h5 class="text-main-custom">Belum ada event umum aktif.</h5>
                </div>
            @endforelse
        </div>
    </div>

    <div class="tab-pane fade" id="past-events" role="tabpanel">
        <div class="row g-4">
            @forelse($pastEvents as $event)
                <div class="col-12 col-md-6 col-xl-4">
                    <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden archive-card d-flex flex-column">
                        @if($event->foto_event)
                            <img src="{{ asset($event->foto_event) }}" class="card-img-top event-card-img grayscale-img" alt="{{ $event->nama_event }}">
                        @else
                            <div class="card-img-placeholder d-flex align-items-center justify-content-center">
                                <i class="bi bi-archive fs-1 opacity-50"></i>
                            </div>
                        @endif
                        
                        <div class="card-body p-4 d-flex flex-column flex-grow-1 bg-archive">
                            <div class="d-flex justify-content-between align-items-center mb-3 gap-2">
                                <div class="d-flex gap-1">
                                    <span class="badge bg-secondary rounded-pill px-3 py-1">Selesai</span>
                                    @if($event->school_id)
                                        <span class="badge bg-danger rounded-pill px-3 py-1"><i class="bi bi-shield-lock-fill me-1"></i>Internal</span>
                                    @else
                                        <span class="badge bg-success rounded-pill px-3 py-1"><i class="bi bi-globe me-1"></i>Eksternal</span>
                                    @endif
                                </div>
                                <span class="text-muted-custom small fw-bold">EVENT SELESAI</span>
                            </div>
                            
                            <h5 class="fw-bold text-main-custom mb-2 text-line-limit-2">{{ $event->nama_event }}</h5>
                            <p class="text-muted-custom small mb-4 flex-grow-1 text-decoration-line-through fw-bold">
                                Terlaksana pada {{ \Carbon\Carbon::parse($event->tanggal_event)->format('d M Y') }}
                            </p>
                            
                            <div class="mt-auto pt-3 border-top custom-border">
                                <div class="d-flex flex-column gap-2">
                                    <a href="{{ route('public.event.show', $event->uuid) }}" class="btn btn-light-custom w-100 rounded-pill py-2 fw-bold small">
                                        <i class="bi bi-info-circle me-1"></i> Detail Event
                                    </a>
                                    @if($event->type != 'umum')
                                        <a href="{{ route('public.klasemen.index', ['event_id' => $event->id, 'is_archive' => 1]) }}" class="btn btn-outline-secondary w-100 rounded-pill py-2 fw-bold small">
                                            <i class="bi bi-trophy me-1"></i> Klasemen Akhir
                                        </a>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            @empty
                <div class="col-12 text-center py-5 empty-state">
                    <i class="bi bi-archive fs-1 text-muted-custom mb-3 d-block opacity-50"></i>
                    <h5 class="text-main-custom">Belum ada arsip event.</h5>
                </div>
            @endforelse
        </div>
    </div>
</div>
@endsection