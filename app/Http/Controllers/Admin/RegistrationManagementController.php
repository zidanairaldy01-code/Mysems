<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Registration;
use Illuminate\Http\Request;

class RegistrationManagementController extends Controller
{
    public function index(Request $request)
    {
        $user = auth()->user();
        $type = $request->get('type'); // 'tournament' or 'umum'
        
        // 1. Ambil daftar event untuk dropdown filter
        if ($user->role == '1') {
            $events = Event::latest();
            if ($type) $events->where('type', $type);
            $events = $events->get();
        } else {
            $events = Event::where(function($query) use ($user) {
                $query->where('user_id', $user->id)
                      ->orWhere(function($subQuery) {
                          $subQuery->whereHas('user', function($q) {
                              $q->where('role', '1');
                          })->whereIn('status', [1, 2]);
                      });
            })->latest();
            if ($type) $events->where('type', $type);
            $events = $events->get();
        }

        $selected_event_id = $request->get('event_id');

        // 2. Ambil data registrasi berdasarkan filter
        $query = Registration::with('event')->latest();

        if ($type) {
            $query->whereHas('event', function($q) use ($type) {
                $q->where('type', $type);
            });
        }

        if ($selected_event_id) {
            $query->where('event_id', $selected_event_id);
        } elseif ($user->role != '1') {
            // Jika panitia dan tidak pilih filter, batasi ke event miliknya saja
            $eventIds = $events->pluck('id');
            $query->whereIn('event_id', $eventIds);
        }

        $registrations = $query->get();

        $view = ($user->role == '1') ? 'admin.registration.index' : 'panitia.registration.index';
        
        return view($view, compact('registrations', 'events', 'selected_event_id', 'type'));
    }

    /**
     * Menghapus pendaftaran tim
     */
    public function destroy($id)
    {
        $registration = Registration::findOrFail($id);

        $event = $registration->event;
        if ($event && $event->status != 2 && $event->status != 4 && $event->tanggal_event >= date('Y-m-d')) {
            return redirect()->back()->with('error', 'Data pendaftaran tidak dapat dihapus sebelum event selesai.');
        }

        $registration->delete();
        
        return redirect()->back()->with('success', 'Data pendaftaran tim berhasil dihapus!');
    }

    /**
     * Menampilkan halaman scanner QR
     */
    public function scan()
    {
        $view = (auth()->user()->role == '1') ? 'admin.registration.scan' : 'panitia.registration.scan';
        return view($view);
    }

    /**
     * Memproses hasil scan QR Code
     */
    public function processScan(Request $request)
    {
        $qr_code = $request->qr_code;
        $registration = Registration::with('event')->where('qr_code', $qr_code)->first();

        if (!$registration) {
            return response()->json(['success' => false, 'message' => 'QR Code tidak valid atau tidak ditemukan!'], 404);
        }

        if ($registration->payment_status != 'settlement') {
            return response()->json(['success' => false, 'message' => 'Pendaftaran ini belum lunas!'], 400);
        }

        if ($registration->attended_at) {
            return response()->json([
                'success' => false, 
                'already_attended' => true,
                'message' => 'Peserta ' . $registration->nama_tim . ' sudah melakukan absen pada ' . $registration->attended_at->format('d/m/Y H:i'),
                'data' => $registration
            ], 200);
        }

        $registration->update(['attended_at' => now()]);

        return response()->json([
            'success' => true, 
            'message' => 'Berhasil! Kehadiran ' . $registration->nama_tim . ' telah dicatat.',
            'data' => $registration
        ]);
    }
}
