<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\User;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $today = date('Y-m-d');

        if ($user->role == '1') {
            // Data untuk Admin
            $totalEvent = Event::count();
            $totalPanitia = User::where('role', '0')->count();
            $totalPeserta = User::where('role', '2')->count();
            $eventSelesai = Event::where('tanggal_event', '<', $today)->count();
            
            $upcomingEvents = Event::where('tanggal_event', '>=', $today)
                ->where('status', 1)
                ->latest()
                ->take(5)
                ->get();
            
            $recentRegistrations = Registration::with('event')
                ->latest()
                ->take(5)
                ->get();

            // Tambahan default value agar compact() tidak error saat login sebagai Admin
            $eventSuccess = 0;
            $eventRejected = 0;
            $totalTeams = 0;

        } else {
            // Data untuk Panitia (Disesuaikan agar sinkron dengan daftar event)
            $visibleEventQuery = function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere(function ($subQuery) {
                          $subQuery->whereHas('user', function ($q) {
                              $q->where('role', '1');
                          })->whereIn('status', [1, 2]);
                      });
            };

            $totalEvent = Event::where($visibleEventQuery)->count();

            $totalPeserta = Registration::whereHas('event', $visibleEventQuery)
                ->where('payment_status', 'settlement')
                ->count();

            $totalTeams = Registration::whereHas('event', $visibleEventQuery)
                ->where('payment_status', 'settlement')
                ->select('nama_tim')
                ->distinct()
                ->count();

            $eventSuccess = Event::where($visibleEventQuery)
                ->whereIn('status', [1, 2])
                ->count();

            $eventRejected = Event::where('user_id', $user->id)
                ->where('status', 3)
                ->count();

            $eventSelesai = Event::where($visibleEventQuery)
                ->where('tanggal_event', '<', $today)
                ->count();

            $upcomingEvents = Event::with('user')
                ->where($visibleEventQuery)
                ->where('tanggal_event', '>=', $today)
                ->latest()
                ->take(5)
                ->get();

            $recentRegistrations = Registration::with('event')
                ->whereHas('event', $visibleEventQuery)
                ->latest()
                ->take(5)
                ->get();

            $totalPanitia = $totalTeams;
        }

        return view($user->role == '1' ? 'admin.index' : 'panitia.index', compact(
            'totalEvent', 'totalPanitia', 'totalPeserta', 'eventSelesai', 'upcomingEvents', 'recentRegistrations',
            'eventSuccess', 'eventRejected', 'totalTeams'
        ));
    }
}