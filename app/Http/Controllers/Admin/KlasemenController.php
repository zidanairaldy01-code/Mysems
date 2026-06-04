<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\TournamentMatch;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;

class KlasemenController extends Controller
{
    /**
     * Menampilkan halaman klasemen dan bagan pertandingan.
     */
    public function index(Request $request)
    {
        $user = auth()->user();
        
        // Ambil daftar event untuk dropdown (Hanya bertipe tournament)
        if ($user->role == '1') {
            $events = Event::where('status', '!=', 4)->where('type', 'tournament')->latest()->get();
        } else {
            $events = Event::where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere(function($subQuery) {
                          $subQuery->whereHas('user', function($q) {
                              $q->where('role', '1');
                          })->whereIn('status', [1, 2]);
                      });
            })
            ->where('status', '!=', 4)
            ->where('type', 'tournament')
            ->latest()->get();
        }

        $selected_event_id = $request->get('event_id');
        
        if ($selected_event_id && !$events->contains('id', $selected_event_id)) {
            $selected_event_id = null;
        }

        if (!$selected_event_id && $events->count() > 0) {
            $selected_event_id = $events->first()->id;
        }

        $matches = TournamentMatch::where('event_id', $selected_event_id)->get()->groupBy('round');
        
        $selectedEvent = $events->firstWhere('id', $selected_event_id);
        $isFinished = false;
        $bracketSize = 2; // Default ukuran bagan

        if ($selectedEvent) {
            $isFinished = ($selectedEvent->status == 2);

            // Hitung jumlah tim terdaftar yang valid
            $registeredCount = Registration::where('event_id', $selected_event_id)
                ->where('payment_status', 'settlement')
                ->count();
                
            // Tentukan ukuran bagan (pangkat 2 terdekat: 2, 4, 8, 16...)
            if ($registeredCount > 2) {
                while ($bracketSize < $registeredCount) {
                    $bracketSize *= 2;
                }
            }
        }

        $view = ($user->role == '1') ? 'admin.klasemen.index' : 'panitia.klasemen.index';
        
        return view($view, compact('matches', 'events', 'selected_event_id', 'selectedEvent', 'isFinished', 'bracketSize'));
    }

    /**
     * Menampilkan klasemen untuk publik (Peserta tanpa login).
     */
    public function publicIndex(Request $request)
    {
        $selected_event_id = $request->get('event_id');
        $is_archive = $request->get('is_archive');

        if ($is_archive && $selected_event_id) {
            $events = Event::where('id', $selected_event_id)->where('type', 'tournament')->get();
        } else {
            $events = Event::where('status', 1)->where('type', 'tournament')->latest()->get();
            if (!$selected_event_id && $events->count() > 0) {
                $selected_event_id = $events->first()->id;
            }
        }

        $selectedEvent = $events->firstWhere('id', $selected_event_id);
        $isFinished = false;
        $bracketSize = 2;

        if ($selectedEvent) {
            $isFinished = ($selectedEvent->status == 2);

            $registeredCount = Registration::where('event_id', $selected_event_id)
                ->where('payment_status', 'settlement')
                ->count();
                
            if ($registeredCount > 2) {
                while ($bracketSize < $registeredCount) {
                    $bracketSize *= 2;
                }
            }
        }

        $matches = TournamentMatch::where('event_id', $selected_event_id)->get()->groupBy('round');
        return view('public.klasemen.index', compact('matches', 'events', 'selected_event_id', 'selectedEvent', 'isFinished', 'bracketSize'));
    }

    /**
     * Memproses pembaruan data pertandingan (nama tim dan skor).
     */
    public function update(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id',
            'round' => 'required|integer',
            'match_number' => 'required|integer',
            'team1_score' => 'nullable|numeric',
            'team2_score' => 'nullable|numeric',
        ]);

        $match = TournamentMatch::firstOrNew([
            'event_id' => $request->event_id,
            'round'    => $request->round,
            'match_number' => $request->match_number,
        ]);

        if (!empty($request->team1_name)) {
            $match->team1_name = $request->team1_name;
        }
        if (!empty($request->team2_name)) {
            $match->team2_name = $request->team2_name;
        }

        $match->team1_score = $request->filled('team1_score') ? (int)$request->team1_score : null;
        $match->team2_score = $request->filled('team2_score') ? (int)$request->team2_score : null;

        $match->save();

        $this->advanceWinner($match);

        return redirect()->route(
            auth()->user()->role == '1' ? 'admin.klasemen.index' : 'panitia.klasemen.index',
            ['event_id' => $request->event_id]
        )->with('success', 'Skor berhasil diperbarui!');
    }

    /**
     * Logika untuk menentukan pemenang dan mengirimkannya ke babak selanjutnya secara otomatis.
     */
    private function advanceWinner($match)
    {
        if ($match->team1_score === null || $match->team2_score === null) return;

        $winnerName = null;
        if ($match->team1_score > $match->team2_score) {
            $winnerName = $match->team1_name;
        } elseif ($match->team2_score > $match->team1_score) {
            $winnerName = $match->team2_name;
        }

        if (!$winnerName) return;

        $nextRound = $match->round + 1;
        
        // Hitung batas maksimal babak (mengikuti logika pendaftar nyata)
        $registeredCount = Registration::where('event_id', $match->event_id)
            ->where('payment_status', 'settlement')
            ->count();
            
        $bracketSize = 2;
        if ($registeredCount > 2) {
            while ($bracketSize < $registeredCount) {
                $bracketSize *= 2;
            }
        }

        $maxRounds = log($bracketSize, 2);

        if ($nextRound > $maxRounds) return; 

        $nextMatchNumber = ceil($match->match_number / 2);
        $isTeam1 = ($match->match_number % 2 != 0);

        $nextMatch = TournamentMatch::firstOrNew([
            'event_id' => $match->event_id,
            'round' => $nextRound,
            'match_number' => $nextMatchNumber
        ]);

        if ($isTeam1) {
            $nextMatch->team1_name = $winnerName;
        } else {
            $nextMatch->team2_name = $winnerName;
        }

        $nextMatch->save();
    }

    /**
     * Reset dan acak bagan turnamen.
     */
    public function reset(Request $request)
    {
        $request->validate([
            'event_id' => 'required|exists:events,id'
        ]);

        $event_id = $request->event_id;

        $hasScores = TournamentMatch::where('event_id', $event_id)
            ->where(function($query) {
                $query->whereNotNull('team1_score')
                      ->orWhereNotNull('team2_score');
            })->exists();

        if ($hasScores) {
            return redirect()->back()->with('error', 'Klasemen tidak bisa direset karena sudah ada skor pertandingan yang terisi!');
        }

        $registrations = Registration::where('event_id', $event_id)
            ->where('payment_status', 'settlement') 
            ->inRandomOrder()   
            ->get();
            
        // Validasi jika belum ada yang daftar sama sekali
        if ($registrations->count() == 0) {
            return redirect()->back()->with('error', 'Belum ada tim terdaftar yang valid/lunas!');
        }

        // Tentukan jumlah bagan
        $registeredCount = $registrations->count();
        $bracketSize = 2;
        if ($registeredCount > 2) {
            while ($bracketSize < $registeredCount) {
                $bracketSize *= 2;
            }
        }
        $maxMatchesInRound1 = $bracketSize / 2;

        TournamentMatch::where('event_id', $event_id)
            ->update([
                'team1_name' => null,
                'team2_name' => null,
                'team1_score' => null,
                'team2_score' => null
            ]);

        $matchNumber = 1;
        $isTeam1 = true;

        foreach ($registrations as $reg) {
            if ($matchNumber > $maxMatchesInRound1) break; 

            $match = TournamentMatch::firstOrNew([
                'event_id' => $event_id,
                'round' => 1,
                'match_number' => $matchNumber
            ]);

            if ($isTeam1) {
                $match->team1_name = $reg->nama_tim;
                $isTeam1 = false;
            } else {
                $match->team2_name = $reg->nama_tim;
                $isTeam1 = true;
                $matchNumber++;
            }
            $match->save();
        }

        return redirect()->back()->with('success', 'Bagan telah berhasil direset dan diacak ulang!');
    }
}