<?php

namespace App\Http\Controllers\Admin;

use App\Exports\LaporanEventExport;
use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LaporanController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();
        $selectedEventId = $request->event_id;

        // 1. Ambil List Event untuk Dropdown Filter
        if ($user->role == '1') {
            $events = Event::where('status', '!=', 4)->get();
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
            ->get();
        }

        $registrations = collect();
        $selectedEvent = null;

        // 2. Jika Event Dipilih, Ambil Detailnya
        if ($selectedEventId) {
            $selectedEvent = Event::findOrFail($selectedEventId);
            $registrations = Registration::where('event_id', $selectedEventId)
                ->where('payment_status', 'settlement')
                ->latest()
                ->get();
        }

        // 3. Hitung Total Pendapatan untuk Event Terpilih
        $totalPendapatan = $registrations->count() * ($selectedEvent->harga_pendaftaran ?? 0);

        // Logic Export (Sederhana via Header)
        if ($request->has('export')) {
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=Laporan_Event_" . ($selectedEvent->nama_event ?? 'Semua') . ".xls");
            return view('admin.laporan.excel', compact('registrations', 'selectedEvent', 'totalPendapatan'));
        }

        $view = $user->role == '1' ? 'admin.laporan.index' : 'panitia.laporan.index';
        return view($view, compact('events', 'registrations', 'selectedEvent', 'totalPendapatan', 'selectedEventId'));
    }

    public function exportData(Request $request)
    {
        $user = Auth::user();

        $validated = $request->validate([
            'event_type' => 'nullable|string|in:all,tournament,umum',
            'event_ids' => 'nullable|array',
            'event_ids.*' => 'nullable|integer|exists:events,id',
            'columns' => 'required|array|min:1',
            'columns.*' => 'required|string|in:nama_event,type,penyelenggara,nama_panitia,lokasi,tanggal_event,jam_event,harga_pendaftaran,slot_tim,status',
            'format' => 'required|string|in:pdf,excel',
        ]);

        $eventsQuery = Event::with('user')->where('status', '!=', 4);

        if ($user->role != '1') {
            $eventsQuery->where(function ($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere(function ($subQuery) {
                          $subQuery->whereHas('user', function ($q) {
                              $q->where('role', '1');
                          })->whereIn('status', [1, 2]);
                      });
            });
        }

        $eventType = $validated['event_type'] ?? 'all';
        if ($eventType !== 'all') {
            $eventsQuery->where('type', $eventType);
        }

        $eventIds = array_filter($validated['event_ids'] ?? [], fn ($id) => is_numeric($id));
        if (!empty($eventIds)) {
            $eventsQuery->whereIn('id', $eventIds);
        }

        $events = $eventsQuery->latest()->get();
        $rows = LaporanEventExport::formatRows($events, $validated['columns']);
        $headers = array_values(array_intersect_key(LaporanEventExport::columnLabels(), array_flip($validated['columns'])));

        return response()->json([
            'success' => true,
            'data' => $rows,
            'headers' => $headers,
            'format' => $validated['format'],
            'event_type' => $eventType,
            'count' => $events->count(),
        ]);
    }
}
