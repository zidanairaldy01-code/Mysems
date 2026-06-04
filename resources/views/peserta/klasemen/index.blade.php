@extends('layouts.peserta')

@section('title', 'Klasemen & Bracket Event - MySMES')

@section('content')
@push('styles')
    <link rel="stylesheet" href="{{ asset('css/klasemen.css') }}?v={{ time() }}">
@endpush
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">Klasemen & Bracket</h2>
        <p class="text-muted">Pantau hasil pertandingan secara real-time.</p>
    </div>
</div>

<!-- Container Utama Bagan Pertandingan -->
<div class="card border-0 shadow-sm rounded-4 mb-4 overflow-hidden">
    <div class="card-header bg-white border-0 py-3">
        <h5 class="fw-bold mb-0">Bagan Pertandingan Turnamen</h5>
    </div>
    <div class="card-body bg-light bg-opacity-50 overflow-auto">
        <div class="bracket-wrapper" style="width: 100%;">
            
            @php
                // Definisi struktur babak: nama babak dan jumlah pertandingan per babak
                $rounds = [
                    1 => ['title' => 'Quarterfinals', 'count' => 4],
                    2 => ['title' => 'Semifinals', 'count' => 2],
                    3 => ['title' => 'Finals', 'count' => 1],
                ];
            @endphp

            {{-- Looping untuk setiap babak (Round) --}}
            @foreach($rounds as $roundNum => $roundData)
            <div class="bracket-round">
                <div class="round-title">{{ $roundData['title'] }}</div>
                
                {{-- Looping untuk membuat kotak pertandingan (Match) di setiap babak --}}
                @for($matchNum = 1; $matchNum <= $roundData['count']; $matchNum++)
                    @php
                        // Cari data pertandingan di database berdasarkan babak dan nomor
                        $match = $matches->get($roundNum, collect())->where('match_number', $matchNum)->first();
                        
                        // Siapkan nama tim dan skor (jika ada data, gunakan itu; jika tidak, kosong)
                        $t1_name = $match->team1_name ?? 'TBD';
                        $t2_name = $match->team2_name ?? 'TBD';
                        $t1_score = $match->team1_score ?? '-';
                        $t2_score = $match->team2_score ?? '-';
                    @endphp

                    {{-- Form dan Kotak Pertandingan --}}
                    <div class="bracket-match">
                        
                        {{-- Slot Tim 1 --}}
                        <div class="bracket-team">
                            <span class="team-name">{{ $t1_name }}</span>
                            <span class="team-score">{{ $t1_score }}</span>
                        </div>
                        
                        {{-- Slot Tim 2 --}}
                        <div class="bracket-team">
                            <span class="team-name">{{ $t2_name }}</span>
                            <span class="team-score">{{ $t2_score }}</span>
                        </div>
                    </div>
                @endfor
            </div>
            @endforeach

            <!-- Bagian Pemenang Akhir (Juara) -->
            <div class="bracket-round">
                <div class="round-title">Champion</div>
                <div class="text-center px-4">
                    <div class="champion-card">
                        <i class="bi bi-trophy-fill mb-3 d-block" style="font-size: 4rem; color: #ffd700; filter: drop-shadow(0 0 10px rgba(255,215,0,0.5));"></i>
                        <div class="champion-title">Tournament Winner</div>
                        <h2 class="winner-name">
                            @php
                                // Ambil data babak Final (Round 3)
                                $final = $matches->get(3, collect())->where('match_number', 1)->first() ?? null;
                                $winner = 'TBD';

                                // Logika penentuan nama pemenang di babak final
                                if($final && $final->team1_score !== null && $final->team2_score !== null) {
                                    if($final->team1_score > $final->team2_score) $winner = $final->team1_name;
                                    elseif($final->team2_score > $final->team1_score) $winner = $final->team2_name;
                                    else $winner = 'Seri';
                                }
                            @endphp
                            {{ $winner }}
                        </h2>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
