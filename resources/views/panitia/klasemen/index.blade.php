@extends('layouts.panitia')

@section('title', 'Klasemen & Bracket Event - MySEMS')

@section('content')
@php
    $hasWinner = false;
    $winnerName = 'TBD';
    $totalRounds = 0;
    
    if ($selected_event_id && $selectedEvent) {
        $slotTim = isset($bracketSize) ? $bracketSize : 2;
        $matchCount = $slotTim / 2;
        $roundLevel = 1;
        while ($matchCount >= 1) {
            $matchCount /= 2;
            $roundLevel++;
        }
        $totalRounds = $roundLevel - 1;

        if ($totalRounds > 0 && isset($matches)) {
            $finalMatch = $matches->get($totalRounds, collect())->where('match_number', 1)->first() ?? null;
            if ($finalMatch && $finalMatch->team1_score !== null && $finalMatch->team2_score !== null) {
                if ($finalMatch->team1_score != $finalMatch->team2_score) {
                    $hasWinner = true;
                    $winnerName = $finalMatch->team1_score > $finalMatch->team2_score ? $finalMatch->team1_name : $finalMatch->team2_name;
                }
            }
        }
    }
@endphp
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/klasemen.css') }}?v={{ time() }}">
    <style>
        /* CSS Khusus untuk Modal Update Skor agar Selalu di Tengah & Estetik */
        /* CSS untuk Modal Update Skor agar Adaptif dengan Tema */
        #modalUpdatePertandingan .modal-content {
            background: var(--bg-card) !important;
            backdrop-filter: blur(15px) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-main) !important;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.25) !important;
        }

        [data-theme='dark'] #modalUpdatePertandingan .modal-content {
            background: rgba(30, 41, 59, 0.9) !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        #modalUpdatePertandingan .modal-header {
            border-bottom: 1px solid var(--border-color) !important;
            cursor: move;
        }

        #modalUpdatePertandingan .btn-close {
            filter: var(--theme-icon-filter);
        }

        #modalUpdatePertandingan .form-control {
            background: var(--bg-body) !important;
            border: 1px solid var(--border-color) !important;
            color: var(--text-main) !important;
        }

        #modalUpdatePertandingan .form-control:focus {
            border-color: var(--primary-color) !important;
            box-shadow: 0 0 0 4px rgba(59, 130, 246, 0.2) !important;
        }

        #modalUpdatePertandingan .input-group-text {
            background: var(--bg-main) !important;
            border-color: var(--border-color) !important;
            color: var(--text-muted) !important;
        }

        #modalUpdatePertandingan .badge {
            background: var(--bg-main) !important;
            color: var(--text-muted) !important;
            border: 1px solid var(--border-color) !important;
        }

        /* Styling untuk Select Event di Dark Mode */
        [data-theme='dark'] .form-select {
            background-color: rgba(15, 23, 42, 0.8) !important;
            color: #f8fafc !important;
            border-color: rgba(255, 255, 255, 0.1) !important;
        }

        [data-theme='dark'] .form-select:focus {
            background-color: rgba(15, 23, 42, 0.9) !important;
            border-color: #3b82f6 !important;
            box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25) !important;
        }

        [data-theme='dark'] .form-select option {
            background-color: #1e293b !important;
            color: #f8fafc !important;
        }
    </style>
@endpush

<div class="text-center text-md-start mb-4">
    <h2 class="fw-bold">Klasemen & Bracket</h2>
    <p class="text-muted mb-0">Kelola struktur pertandingan secara manual.</p>
</div>

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div class="w-100 w-md-auto">
        <form action="{{ route('panitia.klasemen.index') }}" method="GET" class="d-flex align-items-center w-100 w-md-auto">
            <label for="event_id" class="me-2 fw-medium text-muted text-nowrap d-none d-md-inline">Pilih Event:</label>
            <select name="event_id" id="event_id" class="form-select rounded-pill shadow-sm border-0 w-100 w-md-auto" onchange="this.form.submit()" style="min-width: 200px;">
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
    
    <div class="w-100 w-md-auto d-flex justify-content-center justify-content-md-end">
        @if($selected_event_id && $selectedEvent)
            @if(!$isFinished)
            <form action="{{ route('panitia.klasemen.reset') }}" method="POST" id="resetBaganForm" class="w-100 w-md-auto">
                @csrf
                <input type="hidden" name="event_id" value="{{ $selected_event_id }}">
                <button type="button" class="btn btn-outline-danger btn-sm rounded-pill px-3 py-2 w-100 w-md-auto" onclick="confirmResetBagan()">
                    <i class="bi bi-arrow-clockwise me-1"></i> Reset & Acak Bagan
                </button>
            </form>

            @push('scripts')
            <script>
                function confirmResetBagan() {
                    Swal.fire({
                        title: 'Reset Bagan?',
                        text: "Bagan akan dikosongkan dan posisi tim akan diacak ulang!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Reset & Acak!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('resetBaganForm').submit();
                        }
                    })
                }
            </script>
            @endpush
            @else
            <span class="badge bg-secondary rounded-pill px-3 py-2"><i class="bi bi-archive me-1"></i> Event Arsip (Read-Only)</span>
            @endif
        @endif
        @if($selected_event_id)
            @php
                $certRoute = Auth::user()->role == '1' ? route('admin.certificate.generate', $selected_event_id) : route('panitia.certificate.generate', $selected_event_id);
            @endphp
            @if($hasWinner)
                <a href="{{ $certRoute }}" class="btn btn-warning rounded-pill px-4 shadow-sm fw-bold text-dark ms-2 w-100 w-md-auto">
                    <i class="bi bi-award-fill me-2"></i>Cetak Sertifikat
                </a>
            @else
                <button type="button" class="btn btn-secondary rounded-pill px-4 shadow-sm fw-bold ms-2 w-100 w-md-auto opacity-50" onclick="showNoWinnerAlert()">
                    <i class="bi bi-award-fill me-2"></i>Cetak Sertifikat
                </button>
            @endif
        @endif
    </div>
</div>

@if($selected_event_id && $selectedEvent)
    <div class="d-none d-print-block text-center mb-5 mt-3">
        <h3 class="fw-bold text-uppercase">Bagan Pertandingan Turnamen</h3>
        <h2 class="text-primary fw-800">{{ $selectedEvent->nama_event }}</h2>
        <p class="text-muted">Dicetak pada: {{ date('d/m/Y H:i') }}</p>
        <hr class="border-2 opacity-50">
    </div>
@endif

@if($events->count() > 0 && $selected_event_id && $selectedEvent)
@php 
    $slotTim = isset($bracketSize) ? $bracketSize : 2;
    
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

<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
    <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
        <h5 class="fw-bold mb-0">Bagan Pertandingan ({{ $slotTim }} Tim)</h5>
        <span class="badge bg-secondary text-white border rounded-pill">Total {{ $totalRounds }} Babak</span>
    </div>
    <div class="card-body p-0 overflow-auto" style="background: var(--bg-main);">
        <div class="bracket-wrapper" style="min-width: {{ $totalRounds * 350 }}px;">
            
            @foreach($rounds as $roundNum => $roundData)
            <div class="bracket-round">
                <div class="round-title">{{ $roundData['title'] }}</div>
                
                @for ($m = 1; $m <= $roundData['count']; $m++)
                @php
                    $match = $matches->get($roundNum, collect())->where('match_number', $m)->first() ?? null;
                @endphp

                <div class="bracket-match {{ !$isFinished ? 'match-trigger' : '' }}" 
                     @if(!$isFinished) data-bs-toggle="modal" data-bs-target="#modalUpdatePertandingan" @endif
                     data-round="{{ $roundNum }}"
                     data-match-number="{{ $m }}"
                     data-team1="{{ $match->team1_name ?? '' }}"
                     data-team2="{{ $match->team2_name ?? '' }}"
                     data-score1="{{ $match->team1_score ?? '' }}"
                     data-score2="{{ $match->team2_score ?? '' }}">
                    
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

@push('modals')
<!-- Modal Update Skor -->
<div class="modal fade" id="modalUpdatePertandingan" tabindex="-1" aria-hidden="true" style="z-index: 9999;" data-bs-backdrop="false">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content border-0 rounded-4 shadow-lg">
            <div class="modal-header border-0 pb-0" id="draggableHeader" style="cursor: move; user-select: none;">
                <h5 class="modal-title fw-bold"><i class="bi bi-arrows-move me-2 small opacity-50"></i>Update Skor</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body p-4">
                <form action="{{ route('panitia.klasemen.update') }}" method="POST">
                    @csrf
                    <input type="hidden" name="event_id" value="{{ $selected_event_id }}">
                    <input type="hidden" name="round" id="input-round">
                    <input type="hidden" name="match_number" id="input-match-number">
                    
                    @if($errors->any())
                    <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">
                        <ul class="mb-0 small fw-medium">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    @endif

                    <div class="row g-3">
                        <div class="col-12">
                            <label id="label-team1" class="form-label fw-bold text-primary text-uppercase mb-2" style="letter-spacing: 1px;">Tim 1</label>
                            <div class="input-group">
                                <input type="text" name="team1_name" id="input-team1" class="form-control rounded-start-3 fw-semibold" placeholder="Ketik nama tim 1...">
                                <span class="input-group-text fw-bold">SKOR</span>
                                <input type="number" name="team1_score" id="input-score1" class="form-control text-center fw-bold text-primary" style="max-width: 80px; font-size: 1.2rem;" placeholder="0">
                            </div>
                        </div>

                        <div class="col-12 text-center my-1">
                            <span class="badge rounded-pill px-3 py-2 bg-secondary text-white">VS</span>
                        </div>

                        <div class="col-12">
                            <!-- Tambahkan id="label-team2" dan perbagus stylingnya -->
                            <label id="label-team2" class="form-label fw-bold text-danger text-uppercase mb-2" style="letter-spacing: 1px;">Tim 2</label>
                            <div class="input-group">
                                <!-- Hapus readonly dan ubah placeholder agar panitia bisa mengetik manual -->
                                <input type="text" name="team2_name" id="input-team2" class="form-control rounded-start-3 fw-semibold" placeholder="Ketik nama tim 2...">
                                <span class="input-group-text fw-bold">SKOR</span>
                                <input type="number" name="team2_score" id="input-score2" class="form-control text-center fw-bold text-primary" style="max-width: 80px; font-size: 1.2rem;" placeholder="0">
                            </div>
                        </div>
                    </div>
                    
                    <div class="d-grid mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill py-3 fw-bold shadow-sm">
                            Simpan Perubahan Skor <i class="bi bi-check-circle-fill ms-2"></i>
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const modal = document.getElementById('modalUpdatePertandingan');
        const modalDialog = modal.querySelector('.modal-dialog');
        const header = document.getElementById('draggableHeader');
        
        modal.addEventListener('show.bs.modal', function(event) {
            const trigger = event.relatedTarget;
            
            // Ambil data untuk label (Jika kosong, tampilkan "Menunggu Lawan")
            const team1Name = trigger.getAttribute('data-team1') || 'Menunggu Lawan';
            const team2Name = trigger.getAttribute('data-team2') || 'Menunggu Lawan';

            // Ubah label secara dinamis
            document.getElementById('label-team1').textContent = team1Name;
            document.getElementById('label-team2').textContent = team2Name;

            // Isi value input form (Jika kosong, biarkan benar-benar kosong '' agar bisa diketik manual)
            document.getElementById('input-round').value = trigger.getAttribute('data-round');
            document.getElementById('input-match-number').value = trigger.getAttribute('data-match-number');
            document.getElementById('input-team1').value = trigger.getAttribute('data-team1') || ''; 
            document.getElementById('input-team2').value = trigger.getAttribute('data-team2') || '';
            document.getElementById('input-score1').value = trigger.getAttribute('data-score1');
            document.getElementById('input-score2').value = trigger.getAttribute('data-score2');

            // Reset posisi modal ke tengah setiap kali dibuka
            modalDialog.style.top = "";
            modalDialog.style.left = "";
            modalDialog.style.position = "";
            modalDialog.style.margin = "";
        });

        // Logika Draggable Premium
        let isDragging = false;
        let startX, startY, initialX, initialY;

        header.addEventListener('mousedown', function(e) {
            isDragging = true;
            startX = e.clientX;
            startY = e.clientY;
            const rect = modalDialog.getBoundingClientRect();
            initialX = rect.left;
            initialY = rect.top;
            modalDialog.style.transition = 'none';
            modalDialog.style.position = 'fixed';
            modalDialog.style.margin = '0';
            modalDialog.style.left = initialX + 'px';
            modalDialog.style.top = initialY + 'px';
            modalDialog.style.width = rect.width + 'px';
        });

        document.addEventListener('mousemove', function(e) {
            if (!isDragging) return;
            const dx = e.clientX - startX;
            const dy = e.clientY - startY;
            modalDialog.style.left = (initialX + dx) + 'px';
            modalDialog.style.top = (initialY + dy) + 'px';
        });

        document.addEventListener('mouseup', function() {
            if (isDragging) {
                isDragging = false;
                modalDialog.style.transition = '';
            }
        });
    });
</script>

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
    function showNoWinnerAlert() {
        Swal.fire({
            title: 'Sertifikat Belum Tersedia',
            text: 'Pemenang turnamen belum ditentukan. Silakan selesaikan semua pertandingan hingga babak final dan isi skornya terlebih dahulu.',
            icon: 'info',
            confirmButtonColor: '#3b82f6',
            confirmButtonText: 'Mengerti'
        });
    }

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

@else
<div class="card border-0 shadow-sm rounded-4 text-center py-5 mt-4">
    <div class="card-body">
        <i class="bi bi-calendar-x fs-1 text-muted mb-3 d-block"></i>
        <h5 class="fw-bold text-dark">Tidak Ada Event</h5>
        <p class="text-muted mb-0">Silakan buat event terlebih dahulu sebelum mengelola klasemen.</p>
    </div>
</div>
@endif

<style>
    @media print {
        .sidebar, .topbar, form, .btn, .d-flex.justify-content-between, .card-header, hr.mx-3 {
            display: none !important;
        }
        .main-wrapper {
            margin-left: 0 !important;
            padding: 0 !important;
        }
        body { background: white !important; }
        .card { box-shadow: none !important; border: none !important; }
        .bracket-wrapper { transform: scale(0.9); transform-origin: top center; }
        .bracket-match { border: 1px solid #ddd !important; box-shadow: none !important; }
    }

    .winner-highlight {
        background-color: rgba(25, 135, 84, 0.08) !important;
        border-left: 4px solid #198754 !important;
    }
    .bracket-team.winner-highlight .team-name { color: #198754 !important; }
</style>
@endsection
