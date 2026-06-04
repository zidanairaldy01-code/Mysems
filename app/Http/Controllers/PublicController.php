<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use App\Models\User;
use Illuminate\Http\Request;

class PublicController extends Controller
{
    public function index()
    {
        $today = date('Y-m-d');
        
        // Statistik Dinamis untuk Landing Page
        $stats = [
            'active_events' => Event::where('status', 1)->where('tanggal_event', '>=', $today)->count(),
            'total_participants' => Registration::where('payment_status', 'settlement')->count(),
            'finished_events' => Event::where(function($q) use ($today) {
                $q->where('status', 2)
                  ->orWhere(function($inner) use ($today) {
                      $inner->where('status', 1)->where('tanggal_event', '<', $today);
                  });
            })->count(),
            'total_agencies' => User::where('role', '0')->count() + 1, // Panitia + Admin
        ];

        return view('public.index', compact('stats'));
    }
}
