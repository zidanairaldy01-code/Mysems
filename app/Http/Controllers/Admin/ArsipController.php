<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use App\Models\TournamentMatch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ArsipController extends Controller
{
    public function index(Request $request)
    {
        $type = $request->query('type');
        $user = Auth::user();

        if ($user->role == '1') {
            $events = Event::when(in_array($type, ['tournament', 'umum']), function ($query) use ($type) {
                $query->where('type', $type);
            })->latest()->get();
        } else {
            $events = Event::where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere(function($subQuery) {
                          $subQuery->whereHas('user', function($q) {
                              $q->where('role', '1');
                          })->whereIn('status', [1, 2]);
                      });
            })->when(in_array($type, ['tournament', 'umum']), function ($query) use ($type) {
                $query->where('type', $type);
            })->latest()->get();
        }

        $title = 'Arsip Event';
        if ($type === 'tournament') {
            $title = 'History Event Turnamen';
        } elseif ($type === 'umum') {
            $title = 'History Event Umum';
        }

        $view = $user->role == '1' ? 'admin.arsip.index' : 'panitia.arsip.index';
        return view($view, compact('events', 'title', 'type'));
    }

    public function show($uuid) // Ubah parameter menjadi $uuid
    {
        $user = Auth::user();
        
        // PERBAIKAN: Cari berdasarkan kolom 'uuid', bukan 'id'
        $event = Event::with('user')->where('uuid', $uuid)->firstOrFail();
        
        // Gunakan $event->id untuk mencari relasinya
        $registrations = Registration::where('event_id', $event->id)
            ->where('payment_status', 'settlement')
            ->get();

        $matches = TournamentMatch::where('event_id', $event->id)
            ->get()
            ->groupBy('round');

        $final = TournamentMatch::where('event_id', $event->id)
            ->where('round', 3)
            ->where('match_number', 1)
            ->first();

        $winner = "Belum Ada";
        if($final && $final->team1_score !== null && $final->team2_score !== null) {
            if($final->team1_score > $final->team2_score) $winner = $final->team1_name;
            elseif($final->team2_score > $final->team1_score) $winner = $final->team2_name;
            else $winner = "Seri";
        }

        $view = $user->role == '1' ? 'admin.arsip.show' : 'panitia.arsip.show';
        return view($view, compact('event', 'registrations', 'matches', 'winner'));
    }

    public function delete($uuid) // Ubah parameter menjadi $uuid
    {
        // PERBAIKAN: Cari berdasarkan kolom 'uuid', bukan 'id'
        $event = Event::where('uuid', $uuid)->firstOrFail();
        
        $user = Auth::user();
        if ($user->role != '1' && $user->id != $event->user_id) {
            abort(403);
        }

        if ($event->foto_event && file_exists(storage_path('app/' . $event->foto_event))) {
            unlink(storage_path('app/' . $event->foto_event));
        }

        // Gunakan $event->id (ID asli dari database) untuk menghapus relasi
        \App\Models\Registration::where('event_id', $event->id)->delete();
        \App\Models\TournamentMatch::where('event_id', $event->id)->delete();

        $event->delete();

        $route = ($user->role == '1') ? 'admin.arsip.index' : 'panitia.arsip.index';
        return redirect()->route($route)->with('success', 'Arsip event berhasil dihapus secara permanen.');
    }
}