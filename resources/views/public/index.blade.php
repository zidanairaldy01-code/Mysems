@extends('layouts.app')

@section('title', 'Portal Peserta - MySMES')

@section('content')
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-primary text-white border-0 shadow-sm rounded-4 overflow-hidden position-relative" id="tour-public-hero">
            <!-- Background Decoration -->
            <div class="position-absolute top-0 end-0 h-100 w-50 d-none d-lg-block" style="background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1)); transform: skewX(-20deg) translateX(20%);"></div>
            
            <div class="card-body p-4 p-md-5 position-relative z-1">
                <div class="row align-items-center">
                    <div class="col-12 col-md-8">
                        <h2 class="fw-bold mb-2">Selamat Datang di MySEMS!</h2>
                        <p class="mb-4 opacity-75 fs-5">Platform resmi manajemen event sekolah. Temukan dan ikuti event sekolah dan seminar terbaik untuk mengembangkan bakatmu!</p>
                        <a href="{{ route('public.event.index') }}" class="btn btn-light text-primary fw-bold px-4 py-2 rounded-pill shadow-sm" id="tour-public-hero-btn">Lihat Event Sekolah</a>
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
    <div class="col-md-6" id="tour-public-event-card">
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
    <div class="col-md-6" id="tour-public-klasemen-card">
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
<div class="row g-3 mb-5 text-center" id="tour-public-stats">
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

@push('styles')
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.css"/>
<style>
    /* Premium Theming for Driver.js Popover matching MySEMS design tokens */
    .driver-popover {
        background-color: var(--bg-card) !important;
        color: var(--text-main) !important;
        border: 1px solid var(--border-color) !important;
        border-radius: 16px !important;
        box-shadow: 0 15px 30px rgba(0, 0, 0, 0.15) !important;
        font-family: 'Inter', sans-serif !important;
        padding: 20px !important;
        max-width: 350px !important;
    }
    .driver-popover-title {
        font-weight: 800 !important;
        color: var(--text-main) !important;
        font-size: 1.15rem !important;
        margin-bottom: 8px !important;
    }
    .driver-popover-description {
        color: var(--text-muted) !important;
        font-size: 0.9rem !important;
        line-height: 1.6 !important;
    }
    .driver-popover-navigation-btns {
        margin-top: 15px !important;
        gap: 8px !important;
        display: flex !important;
        justify-content: flex-end !important;
    }
    .driver-popover-btn {
        font-family: 'Inter', sans-serif !important;
        font-size: 0.85rem !important;
        font-weight: 700 !important;
        padding: 8px 16px !important;
        border-radius: 30px !important;
        text-shadow: none !important;
        border: 1px solid var(--border-color) !important;
        background-color: var(--bg-archive) !important;
        color: var(--text-main) !important;
        transition: all 0.25s ease !important;
    }
    .driver-popover-btn:hover {
        background-color: var(--primary-color) !important;
        color: #ffffff !important;
        border-color: var(--primary-color) !important;
    }
    .driver-popover-close-btn {
        color: var(--text-muted) !important;
    }
    .driver-popover-progress-text {
        color: var(--text-muted) !important;
        font-size: 0.8rem !important;
        font-weight: 500 !important;
    }
    
    /* Dynamic tour backdrop overlay based on theme */
    .driver-overlay {
        fill: rgba(15, 23, 42, 0.65) !important;
        transition: fill 0.3s ease !important;
    }
    [data-theme='dark'] .driver-overlay {
        fill: rgba(3, 7, 18, 0.85) !important;
    }

    /* Target specific arrow borders to avoid arrow-box shape bug */
    .driver-popover-arrow-side-top {
        border-bottom-color: var(--bg-card) !important;
    }
    .driver-popover-arrow-side-bottom {
        border-top-color: var(--bg-card) !important;
    }
    .driver-popover-arrow-side-left {
        border-right-color: var(--bg-card) !important;
    }
    .driver-popover-arrow-side-right {
        border-left-color: var(--bg-card) !important;
    }
</style>
@endpush

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/driver.js@1.3.1/dist/driver.js.iife.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    // Check if the user has already completed the public tour
    if (!localStorage.getItem('mysems_public_tour_seen')) {
        const driver = window.driver.js.driver;
        
        const driverObj = driver({
            showProgress: true,
            allowClose: true,
            overlayColor: 'rgba(15, 23, 42, 0.65)', // Sleek dark overlay
            steps: [
                {
                    element: '#tour-public-hero',
                    popover: {
                        title: 'Selamat Datang di MySEMS! 👋',
                        description: 'Ini adalah portal utama manajemen event sekolah. Kami siap membantu Anda memantau dan berpartisipasi dalam berbagai kegiatan seru!',
                        side: 'bottom',
                        align: 'start'
                    }
                },
                {
                    element: '#nav-public-event',
                    popover: {
                        title: 'Daftar Event Sekolah 📅',
                        description: 'Klik menu ini untuk melihat seluruh kompetisi, turnamen, dan acara sekolah yang sedang aktif atau mendatang.',
                        side: 'bottom',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-public-event-card',
                    popover: {
                        title: 'Jelajahi Aktivitas Aktif 🔍',
                        description: 'Di bagian ini, temukan info lengkap event aktif sekolah. Anda bisa mendaftar secara online dengan mudah.',
                        side: 'top',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-public-klasemen-card',
                    popover: {
                        title: 'Klasemen & Bagan Live 🏆',
                        description: 'Lihat perolehan skor terkini, bagan kompetisi, dan klasemen tim jagoan Anda tanpa harus login.',
                        side: 'top',
                        align: 'center'
                    }
                },
                {
                    element: '#tour-public-stats',
                    popover: {
                        title: 'Statistik Ringkas 📊',
                        description: 'Informasi cepat mengenai jumlah event aktif, total partisipan terdaftar, serta event yang telah selesai.',
                        side: 'top',
                        align: 'center'
                    }
                }
            ],
            onDestroyed: () => {
                // Set flag so it does not run automatically again
                localStorage.setItem('mysems_public_tour_seen', 'true');
            }
        });

        // Delay execution slightly to ensure all styles and assets are fully loaded and rendered
        setTimeout(() => {
            driverObj.drive();
        }, 800);
    }
});
</script>
@endpush
@endsection