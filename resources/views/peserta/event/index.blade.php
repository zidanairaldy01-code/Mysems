@extends('layouts.peserta')

@section('title', 'Event Sekolah - MySMES')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">Event Sekolah</h2>
        <p class="text-muted">Pilih kategori event yang ingin Anda ikuti.</p>
    </div>
</div>

<!-- Nav Pills untuk Kategori -->
<ul class="nav nav-pills mb-4 gap-2 p-1 bg-white rounded-pill shadow-sm d-inline-flex" id="eventTab" role="tablist" style="border: 1px solid #eee;">
    <li class="nav-item" role="presentation">
        <button class="nav-link active rounded-pill px-4 fw-bold" id="tournament-tab" data-bs-toggle="pill" data-bs-target="#tournament-events" type="button" role="tab">
            <i class="bi bi-trophy me-2"></i>Turnamen Sekolah
        </button>
    </li>
    <li class="nav-item" role="presentation">
        <button class="nav-link rounded-pill px-4 fw-bold" id="umum-tab" data-bs-toggle="pill" data-bs-target="#umum-events" type="button" role="tab">
            <i class="bi bi-calendar-check me-2"></i>Event Umum
        </button>
    </li>
</ul>

<div class="tab-content" id="eventTabContent">
    <!-- Tab Turnamen -->
    <div class="tab-pane fade show active" id="tournament-events" role="tabpanel">
        <div class="row g-4">
            @forelse($tournamentEvents as $event)
                <div class="col-md-6 col-lg-4">
                    @include('peserta.event.partials.event_card', ['event' => $event])
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 text-center py-5">
                        <div class="card-body">
                            <i class="bi bi-calendar-x fs-1 text-muted mb-3 d-block"></i>
                            <h5 class="fw-bold text-dark">Belum Ada Turnamen</h5>
                            <p class="text-muted mb-0">Saat ini belum ada turnamen sekolah yang tersedia.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <!-- Tab Event Umum -->
    <div class="tab-pane fade" id="umum-events" role="tabpanel">
        <div class="row g-4">
            @forelse($umumEvents as $event)
                <div class="col-md-6 col-lg-4">
                    @include('peserta.event.partials.event_card', ['event' => $event])
                </div>
            @empty
                <div class="col-12">
                    <div class="card border-0 shadow-sm rounded-4 text-center py-5">
                        <div class="card-body">
                            <i class="bi bi-calendar-x fs-1 text-muted mb-3 d-block"></i>
                            <h5 class="fw-bold text-dark">Belum Ada Event Umum</h5>
                            <p class="text-muted mb-0">Saat ini belum ada event umum yang tersedia.</p>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>
</div>

<style>
    .nav-pills .nav-link.active {
        background-color: #0d6efd !important;
        color: #ffffff !important;
        box-shadow: 0 4px 15px rgba(13, 110, 253, 0.3);
    }
    .nav-pills .nav-link {
        color: #64748b;
        transition: all 0.3s ease;
    }
</style>
@endsection
