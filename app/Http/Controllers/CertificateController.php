<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\TournamentMatch;
use Illuminate\Http\Request;

class CertificateController extends Controller
{
    /**
     * Menghasilkan sertifikat untuk event tertentu.
     */
    public function generate($eventId)
    {
        $event = Event::with('user')->findOrFail($eventId);
        
        // Tentukan Babak Final berdasarkan babak tertinggi di database
        $finalRound = TournamentMatch::where('event_id', $eventId)->max('round');
        if (!$finalRound) {
            $slotTim = $event->slot_tim ?? 8;
            $finalRound = log($slotTim, 2);
        }
        
        // Ambil Pertandingan Final
        $finalMatch = TournamentMatch::where('event_id', $eventId)
            ->where('round', $finalRound)
            ->where('match_number', 1)
            ->first();
            
        $winners = [
            'juara1' => 'TBD',
            'juara2' => 'TBD',
            'juara3' => 'TBD'
        ];

        if ($finalMatch && $finalMatch->team1_score !== null && $finalMatch->team2_score !== null) {
            if ($finalMatch->team1_score > $finalMatch->team2_score) {
                $winners['juara1'] = $finalMatch->team1_name;
                $winners['juara2'] = $finalMatch->team2_name;
            } else {
                $winners['juara1'] = $finalMatch->team2_name;
                $winners['juara2'] = $finalMatch->team1_name;
            }
        }

        // Ambil Semifinalists untuk Juara 3 (Opsional: Ambil salah satu yang skornya lebih tinggi di semifinal)
        $semiFinalRound = $finalRound - 1;
        if ($semiFinalRound > 0) {
            $semiMatches = TournamentMatch::where('event_id', $eventId)
                ->where('round', $semiFinalRound)
                ->get();
                
            $losers = [];
            foreach ($semiMatches as $match) {
                if ($match->team1_score !== null && $match->team2_score !== null) {
                    if ($match->team1_score > $match->team2_score) {
                        $losers[] = $match->team2_name;
                    } else {
                        $losers[] = $match->team1_name;
                    }
                }
            }
            
            if (count($losers) > 0) {
                $winners['juara3'] = implode(' & ', $losers);
            }
        }

        return view('admin.certificate.template', compact('event', 'winners'));
    }
}
