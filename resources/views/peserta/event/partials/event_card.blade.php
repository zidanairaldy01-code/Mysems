<div class="card border-0 shadow-sm rounded-4 h-100">
    <div class="card-body p-4 d-flex flex-column">
        <div class="d-flex justify-content-between align-items-start mb-3">
            <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3">
                <i class="bi {{ $event->type == 'umum' ? 'bi-calendar-check' : 'bi-trophy' }} fs-3"></i>
            </div>
            <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3 py-2">
                <i class="bi bi-check-circle me-1"></i> {{ $event->type == 'umum' ? 'Umum' : 'Turnamen' }}
            </span>
        </div>
        
        <h5 class="fw-bold text-dark mb-2">{{ $event->nama_event }}</h5>
        <p class="text-muted small mb-3 flex-grow-1">{{ Str::limit($event->deskripsi ?? 'Tidak ada deskripsi yang tersedia untuk event ini.', 100) }}</p>
        
        <div class="border-top pt-3 mt-auto">
            <div class="d-flex align-items-center mb-2">
                <i class="bi bi-geo-alt text-primary me-2"></i>
                <span class="small fw-medium">{{ $event->lokasi ?? 'Lokasi belum ditentukan' }}</span>
            </div>
            <div class="d-flex align-items-center mb-3">
                <i class="bi bi-clock text-primary me-2"></i>
                <span class="small fw-bold">{{ \Carbon\Carbon::parse($event->tanggal_event)->format('d F Y') }}</span>
            </div>
            <a href="{{ route('public.event.show', $event->uuid) }}" class="btn btn-primary w-100 rounded-pill shadow-sm">
                Informasi Detail
            </a>
        </div>
    </div>
</div>
