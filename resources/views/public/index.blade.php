@extends('layouts.app')

@section('title', 'Portal Peserta - MySMES')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white border-0 shadow-sm rounded-4 overflow-hidden position-relative">
            <!-- Background Decoration -->
            <div class="position-absolute top-0 end-0 h-100 w-50 d-none d-lg-block" style="background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1)); transform: skewX(-20deg) translateX(20%);"></div>
            
            <div class="card-body p-4 p-md-5 position-relative z-1">
                <div class="row align-items-center">
                    <div class="col-12 col-md-8">
                        <h2 class="fw-bold mb-2">Selamat Datang di MySEMS!</h2>
                        <p class="mb-4 opacity-75 fs-5">Platform resmi manajemen event sekolah. Temukan dan ikuti event sekolah dan seminar terbaik untuk mengembangkan bakatmu!</p>
                        <a href="{{ route('public.event.index') }}" class="btn btn-light text-primary fw-bold px-4 py-2 rounded-pill shadow-sm">Lihat Event Sekolah</a>
                    </div>
                    <div class="col-12 col-md-4 text-center mt-4 mt-md-0">
                        <i class="bi bi-calendar2-heart" style="font-size: 6rem;"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <!-- Card Info 1 -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-primary bg-opacity-10 text-primary rounded-circle p-3 me-3">
                        <i class="bi bi-calendar-check fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0 fw-semibold">Event Sekolah</h6>
                        <h3 class="fw-bold mb-0">Aktif</h3>
                    </div>
                </div>
                <p class="text-muted small mb-3">Lihat daftar kompetisi, webinar, dan acara seru lainnya yang sedang berlangsung.</p>
                <a href="{{ route('public.event.index') }}" class="text-primary fw-bold text-decoration-none small">Jelajahi Event <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>

    <!-- Card Info 2 -->
    <div class="col-md-6">
        <div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden">
            <div class="card-body p-4">
                <div class="d-flex align-items-center mb-3">
                    <div class="bg-success bg-opacity-10 text-success rounded-circle p-3 me-3">
                        <i class="bi bi-trophy fs-3"></i>
                    </div>
                    <div>
                        <h6 class="text-muted mb-0 fw-semibold">Klasemen & Bagan</h6>
                        <h3 class="fw-bold mb-0">Update</h3>
                    </div>
                </div>
                <p class="text-muted small mb-3">Pantau terus hasil pertandingan dan posisi tim jagoanmu di setiap cabang olahraga.</p>
                <a href="{{ route('public.klasemen.index') }}" class="text-success fw-bold text-decoration-none small">Lihat Klasemen <i class="bi bi-arrow-right ms-1"></i></a>
            </div>
        </div>
    </div>
</div>

<!-- Section: Info Statistik Ringkas -->
<div class="row g-3 mb-5 text-center">
    <div class="col-6 col-md-4">
        <div class="p-3 bg-white shadow-sm rounded-4 border-0">
            <h2 class="fw-black text-primary mb-0">{{ $stats['active_events'] ?? 0 }}</h2>
            <p class="text-muted small mb-0 fw-medium">Event Aktif</p>
        </div>
    </div>
    <div class="col-6 col-md-4">
        <div class="p-3 bg-white shadow-sm rounded-4 border-0">
            <h2 class="fw-black text-danger mb-0">{{ $stats['total_participants'] ?? 0 }}</h2>
            <p class="text-muted small mb-0 fw-medium">Partisipan</p>
        </div>
    </div>
    <div class="col-12 col-md-4">
        <div class="p-3 bg-white shadow-sm rounded-4 border-0">
            <h2 class="fw-black text-success mb-0">{{ $stats['finished_events'] ?? 0 }}</h2>
            <p class="text-muted small mb-0 fw-medium">Selesai</p>
        </div>
    </div>
</div>

<!-- Section: Galeri Olahraga -->

<!-- Section: FAQ Sederhana -->
<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold mb-4 text-center">Tanya Jawab (FAQ)</h5>
            <div class="accordion accordion-flush" id="faqAccordion">
                <div class="accordion-item border-bottom">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                            Bagaimana cara mendaftar event?
                        </button>
                    </h2>
                    <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted small">
                            Anda dapat melihat detail event pada menu "Event Sekolah", lalu hubungi panitia yang tertera atau ikuti instruksi pendaftaran di deskripsi event tersebut.
                        </div>
                    </div>
                </div>
                <div class="accordion-item">
                    <h2 class="accordion-header">
                        <button class="accordion-button collapsed fw-semibold" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                            Apakah saya perlu login untuk melihat klasemen?
                        </button>
                    </h2>
                    <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
                        <div class="accordion-body text-muted small">
                            Tidak perlu! Seluruh data event dan klasemen bisa diakses secara publik oleh seluruh siswa dan guru tanpa harus masuk ke sistem.
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection