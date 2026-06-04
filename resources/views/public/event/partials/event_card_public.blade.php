<div class="card border-0 shadow-sm rounded-4 h-100 overflow-hidden position-relative event-card d-flex flex-column">
    {{-- Header / Gambar Event --}}
    <div class="event-img-wrapper position-relative">
        @if($event->foto_event)
            {{-- Mengubah asset() menjadi pemanggilan rute aman --}}
            <img src="{{ route('public.image', ['directory' => 'events', 'filename' => basename($event->foto_event)]) }}" 
                 class="card-img-top event-card-img" 
                 alt="{{ $event->nama_event }}"
                 oncontextmenu="return false;" 
                 draggable="false">
        @else
            <div class="card-img-placeholder d-flex align-items-center justify-content-center">
                <i class="bi bi-image fs-1 opacity-50"></i>
            </div>
        @endif

        <div class="position-absolute top-0 start-0 m-3 z-2 d-flex gap-2">
            <span class="badge {{ $event->type_badge_class ?? 'bg-primary' }} rounded-pill px-3 py-2 shadow-sm fw-semibold">
                {{ $event->type_label ?? 'Event' }}
            </span>
            @if($event->school_id)
                <span class="badge bg-danger rounded-pill px-3 py-2 shadow-sm fw-semibold">
                    <i class="bi bi-shield-lock-fill me-1"></i> Internal
                </span>
            @else
                <span class="badge bg-success rounded-pill px-3 py-2 shadow-sm fw-semibold">
                    <i class="bi bi-globe me-1"></i> Eksternal
                </span>
            @endif
        </div>
    </div>

    {{-- Body Event --}}
    <div class="card-body p-4 d-flex flex-column flex-grow-1 bg-card-custom">
        <div class="d-flex justify-content-between align-items-start mb-3 gap-2">
            <span class="badge bg-secondary-subtle text-secondary-emphasis rounded-pill px-3 py-1 fw-medium text-line-limit-1">
                {{ $event->nama_panitia }}
            </span>
            <span class="fw-bold {{ $event->harga_pendaftaran == 0 ? 'text-success' : 'text-primary-custom' }} text-nowrap">
                {{ $event->harga_pendaftaran == 0 ? 'GRATIS' : 'Rp ' . number_format($event->harga_pendaftaran, 0, ',', '.') }}
            </span>
        </div>

        <h5 class="fw-bold text-main-custom mb-2 text-line-limit-2">{{ $event->nama_event }}</h5>
        <p class="text-muted-custom small mb-4 flex-grow-1 text-line-limit-3">
            {{ Str::limit($event->deskripsi ?? 'Tidak ada deskripsi yang tersedia untuk event ini.', 120) }}
        </p>

        {{-- Meta Data Lokasi & Waktu --}}
        <div class="event-meta-grid mb-4 small text-muted-custom">
            <div class="meta-item full-width mb-2 d-flex align-items-center">
                <i class="bi bi-geo-alt text-primary-custom me-2 fs-6 flex-shrink-0"></i>
                <span class="text-truncate">{{ $event->lokasi ?? 'Online / Belum Ditentukan' }}</span>
            </div>
            <div class="d-flex justify-content-between align-items-center pt-1 border-top custom-border">
                <div class="meta-item d-flex align-items-center">
                    <i class="bi bi-calendar3 text-primary-custom me-2 fs-6 flex-shrink-0"></i>
                    <span class="fw-bold">{{ \Carbon\Carbon::parse($event->tanggal_event)->format('d/m/Y') }}</span>
                </div>
                <div class="meta-item d-flex align-items-center">
                    <i class="bi bi-clock text-primary-custom me-2 fs-6 flex-shrink-0"></i>
                    <span class="fw-bold">{{ \Carbon\Carbon::parse($event->jam_event)->format('H:i') }} WIB</span>
                </div>
            </div>
        </div>

        @php
            $pendaftarLunas = \App\Models\Registration::where('event_id', $event->id)
                ->where('payment_status', 'settlement')
                ->count();
            $sisaSlot = $event->slot_tim - $pendaftarLunas;
            $isFull = $sisaSlot <= 0;
            $isUmum = ($event->type_normalized ?? '') === 'umum';
        @endphp

        {{-- Kuota Bar --}}
        <div class="d-flex justify-content-between align-items-center mb-4 small">
            <span class="text-muted-custom">{{ $isUmum ? 'Kuota Peserta:' : 'Kuota Tim:' }}</span>
            <span class="badge {{ $isFull ? 'bg-danger' : 'bg-info-subtle text-info-emphasis' }} rounded-pill px-3 py-1">
                {{ $pendaftarLunas }} / {{ $event->slot_tim }} {{ $isUmum ? 'Orang' : 'Tim' }}
            </span>
        </div>

        {{-- Action Buttons --}}
        <div class="mt-auto">
            <div class="d-flex flex-column flex-sm-row gap-2">
                <a href="{{ route('public.event.show', $event->uuid) }}" class="btn btn-outline-primary w-100 rounded-pill py-2 fw-semibold small">
                    <i class="bi bi-info-circle me-1"></i> Detail
                </a>
                @if($isFull)
                    <button class="btn btn-secondary w-100 rounded-pill py-2 fw-semibold small disabled" disabled>
                        Penuh
                    </button>
                @else
                    <a href="{{ route('public.event.register', $event->uuid) }}" class="btn btn-primary w-100 rounded-pill py-2 fw-semibold small shadow-sm">
                        Ikuti Event
                    </a>
                @endif
            </div>
        </div>
    </div>
</div>