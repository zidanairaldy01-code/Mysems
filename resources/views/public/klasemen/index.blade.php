@extends('layouts.app')

@section('title', 'Klasemen & Bracket Event - MySMES')

@section('content')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/klasemen.css') }}?v={{ time() }}">
    <style>
        .winner-highlight {
            background-color: rgba(25, 135, 84, 0.08) !important;
            border-left: 4px solid #198754 !important;
        }
        .bracket-team.winner-highlight .team-name { color: #198754 !important; }
    </style>
@endpush
<div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
    <div>
        <h2 class="fw-bold">Klasemen & Bracket</h2>
        <p class="text-muted mb-0">Pantau hasil pertandingan secara real-time.</p>
    </div>
    
    <!-- Dropdown Filter Event (Hanya muncul jika bukan dari Arsip) -->
    @if(!request('is_archive'))
    <div>
        <form action="{{ route('public.klasemen.index') }}" method="GET" class="d-flex align-items-center">
            <label for="event_id" class="me-2 fw-medium text-muted text-nowrap">Pilih Event:</label>
            <select name="event_id" id="event_id" class="form-select rounded-pill shadow-sm border-0" onchange="this.form.submit()" style="min-width: 200px;">
                @forelse($events as $event)
                    <option value="{{ $event->id }}" {{ $selected_event_id == $event->id ? 'selected' : '' }}>
                        {{ $event->nama_event }}
                    </option>
                @empty
                    <option value="">Belum ada event tersedia</option>
                @endforelse
            </select>
        </form>
    </div>
    @else
    <div>
        <a href="{{ route('public.event.index') }}" class="btn btn-light rounded-pill px-4 fw-bold shadow-sm">
            <i class="bi bi-arrow-left me-2"></i>Kembali ke Daftar Event
        </a>
    </div>
    @endif
</div>

@if($events->count() > 0 && $selected_event_id && $selectedEvent)
@php 
    // MENGGUNAKAN $bracketSize HASIL KALKULASI DARI CONTROLLER
    $slotTim = isset($bracketSize) ? $bracketSize : 2;
    
    // Tentukan Struktur Babak secara Dinamis
    $rounds = [];
    $matchCount = $slotTim / 2;
    $roundLevel = 1;
    
    while ($matchCount >= 1) {
        $title = 'Finals';
        if ($matchCount == 2) $title = 'Semifinals';
        if ($matchCount == 4) $title = 'Quarterfinals';
        if ($matchCount == 8) $title = 'Round of 16';
        if ($matchCount > 8) $title = 'Round of ' . ($matchCount * 2);
        
        $rounds[$roundLevel] = [
            'title' => $title,
            'count' => $matchCount
        ];
        
        $matchCount /= 2;
        $roundLevel++;
    }
    $totalRounds = count($rounds);
@endphp

<!-- Container Utama Bagan Pertandingan -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Bagan Pertandingan ({{ $selectedEvent->nama_event ?? 'Event' }})</h5>
        <div class="d-flex align-items-center gap-3">
            <div class="d-flex align-items-center small text-muted fw-bold">
                <div class="spinner-grow spinner-grow-sm text-success me-2" role="status" style="width: 8px; height: 8px;"></div>
                Update Otomatis (30s)
            </div>
            <span class="badge bg-light text-muted border rounded-pill">{{ $slotTim }} Tim</span>
        </div>
    </div>
    <div class="card-body p-0 overflow-auto" style="background: var(--bg-main);">
        <div class="bracket-wrapper" style="min-width: {{ $totalRounds * 350 }}px;">
            
            {{-- Looping untuk setiap babak (Round) --}}
            @foreach($rounds as $roundNum => $roundData)
            <div class="bracket-round">
                <div class="round-title">{{ $roundData['title'] }}</div>
                
                {{-- Looping untuk setiap kotak pertandingan dalam babak tersebut --}}
                @for ($m = 1; $m <= $roundData['count']; $m++)
                @php
                    $match = $matches->get($roundNum, collect())->where('match_number', $m)->first() ?? null;
                @endphp

                <!-- Kotak Pertandingan -->
                <div class="bracket-match">
                    <div class="bracket-team {{ ($match && $match->team1_score !== null && $match->team2_score !== null && $match->team1_score > $match->team2_score) ? 'winner-highlight' : '' }}">
                        <span class="team-name text-truncate {{ $match && $match->team1_name ? 'fw-bold text-dark' : 'text-muted fst-italic small' }}">
                            {{ $match->team1_name ?? 'Menunggu Lawan' }}
                        </span>
                        <span class="team-score {{ ($match && $match->team1_score !== null && $match->team2_score !== null && $match->team1_score > $match->team2_score) ? 'bg-success text-white' : 'bg-card-custom text-main-custom border-custom' }}">
                            {{ $match->team1_score ?? '-' }}
                        </span>
                    </div>

                    <div class="bracket-team {{ ($match && $match->team1_score !== null && $match->team2_score !== null && $match->team2_score > $match->team1_score) ? 'winner-highlight' : '' }}">
                        <span class="team-name text-truncate {{ $match && $match->team2_name ? 'fw-bold text-dark' : 'text-muted fst-italic small' }}">
                            {{ $match->team2_name ?? 'Menunggu Lawan' }}
                        </span>
                        <span class="team-score {{ ($match && $match->team1_score !== null && $match->team2_score !== null && $match->team2_score > $match->team1_score) ? 'bg-success text-white' : 'bg-card-custom text-main-custom border-custom' }}">
                            {{ $match->team2_score ?? '-' }}
                        </span>
                    </div>
                </div>
                @endfor
            </div>
            @endforeach

            <div class="bracket-round">
                <div class="round-title">Champion</div>
                <div class="text-center px-2 py-5">
                    <div class="champion-card mx-auto" style="max-width: 320px;">
                        <i class="bi bi-trophy-fill mb-3 d-block" style="font-size: 3.5rem; color: #fbbf24;"></i>
                        <div class="text-white small fw-bold text-uppercase mb-2" style="letter-spacing: 2px; opacity: 0.8;">Tournament Winner</div>
                        <div class="winner-name">
                            @php
                                $final = $matches->get($totalRounds, collect())->where('match_number', 1)->first() ?? null;
                                $winner = 'TBD';
                                if($final && $final->team1_score !== null && $final->team2_score !== null) {
                                    if($final->team1_score > $final->team2_score) $winner = $final->team1_name;
                                    elseif($final->team2_score > $final->team1_score) $winner = $final->team2_name;
                                    else $winner = 'Tie';
                                }
                            @endphp
                            {{ $winner }}
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@else
<div class="card border-0 shadow-sm rounded-4 text-center py-5 mt-4">
    <div class="card-body">
        <i class="bi bi-calendar-x fs-1 text-muted mb-3 d-block"></i>
        <h5 class="fw-bold text-dark">Tidak Ada Event</h5>
        <p class="text-muted mb-0">Saat ini belum ada event turnamen yang diselenggarakan.</p>
    </div>
</div>
@endif

@push('scripts')
{{-- Confetti hanya dijalankan jika ada juara --}}
@php
    $finalMatch = isset($totalRounds) ? ($matches->get($totalRounds, collect())->where('match_number', 1)->first() ?? null) : null;
    $winnerForJS = 'TBD';
    if ($finalMatch && $finalMatch->team1_score !== null && $finalMatch->team2_score !== null) {
        if ($finalMatch->team1_score > $finalMatch->team2_score) $winnerForJS = $finalMatch->team1_name;
        elseif ($finalMatch->team2_score > $finalMatch->team1_score) $winnerForJS = $finalMatch->team2_name;
        else $winnerForJS = 'Tie';
    }
@endphp
<script src="https://cdn.jsdelivr.net/npm/canvas-confetti@1.6.0/dist/confetti.browser.min.js"></script>
<script>
    // Refresh otomatis setiap 30 detik jika bukan halaman arsip
    @if(!request('is_archive'))
        setInterval(function() {
            location.reload();
        }, 30000); // 30.000 ms = 30 detik
    @endif

    document.addEventListener('DOMContentLoaded', function() {
        const winnerName = @json($winnerForJS);
        if (winnerName !== 'TBD' && winnerName !== 'Tie' && winnerName !== '') {
            const duration = 5 * 1000;
            const animationEnd = Date.now() + duration;
            const defaults = { startVelocity: 30, spread: 360, ticks: 60, zIndex: 9999 };
            function randomInRange(min, max) { return Math.random() * (max - min) + min; }
            const interval = setInterval(function() {
                const timeLeft = animationEnd - Date.now();
                if (timeLeft <= 0) return clearInterval(interval);
                const particleCount = 50 * (timeLeft / duration);
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.1, 0.3), y: Math.random() - 0.2 } }));
                confetti(Object.assign({}, defaults, { particleCount, origin: { x: randomInRange(0.7, 0.9), y: Math.random() - 0.2 } }));
            }, 250);
        }
    });
</script>
@endpush
@endsection
