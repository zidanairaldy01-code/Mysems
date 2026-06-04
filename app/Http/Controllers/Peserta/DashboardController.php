<?php

namespace App\Http\Controllers\Peserta;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $userId = Auth::id();
        $today = date('Y-m-d');

        // Statistik Peserta
        $totalEventDiikuti = Registration::where('user_id', $userId)->count();
        $totalEventAktif = Event::where('status', 1)->where('tanggal_event', '>=', $today)->count();
        $totalLunas = Registration::where('user_id', $userId)->where('payment_status', 'settlement')->count();
        $totalPending = Registration::where('user_id', $userId)->where('payment_status', 'pending')->count();

        // Riwayat Pendaftaran Peserta
        $myRegistrations = Registration::with('event')
            ->where('user_id', $userId)
            ->latest()
            ->take(5)
            ->get();

        // Event yang Direkomendasikan (Mendatang)
        $recommendedEvents = Event::where('status', 1)
            ->where('tanggal_event', '>=', $today)
            ->latest()
            ->take(5)
            ->get();

        return view('peserta.index', compact(
            'totalEventDiikuti', 'totalEventAktif', 'totalLunas', 'totalPending', 
            'myRegistrations', 'recommendedEvents'
        ));
    }
}
