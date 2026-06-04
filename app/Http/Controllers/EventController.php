<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

/**
 * Controller untuk mengelola siklus hidup Event
 * Menangani pembuatan, daftar, dan proses persetujuan (approval)
 */
class EventController extends Controller
{
    /**
     * Menampilkan daftar event.
     * Logic: Admin melihat semua, Panitia hanya melihat miliknya sendiri.
     */
    public function index(Request $request)
    {
        $user = Auth::user();
        $type = $request->get('type'); // 'tournament' or 'umum'
        $status = $request->get('status'); // pending, success, fail

        // Mapping parameter string dari URL ke integer status di database
        $statusMap = [
            'pending' => 0,
            'success' => 1,
            'fail'    => 3,
        ];

        if ($user->role == '1') {
            // Admin: Ambil semua data event (KECUALI yang status 4 / dihapus)
            $query = Event::with('user')->where('status', '!=', 4);
            
            if ($type) {
                $query->where('type', $type);
            }

            if (isset($statusMap[$status])) {
                $query->where('status', $statusMap[$status]);
            }
            
            $events = $query->latest()->get();
            return view('admin.event.index', compact('events', 'type', 'status'));
            
        } elseif ($user->role == '0') {
            // Panitia: Ambil data event
            $query = Event::with('user')
                ->where(function($q) use ($user, $statusMap, $status) {
                    // Logika 1: Event miliknya sendiri
                    $q->where(function($myQuery) use ($user, $statusMap, $status) {
                        $myQuery->where('user_id', $user->id);
                        // Terapkan filter status HANYA jika parameter status tersedia
                        if (isset($statusMap[$status])) {
                            $myQuery->where('status', $statusMap[$status]);
                        }
                    });

                    // Logika 2: Ditambah event milik Admin (jika tidak sedang memfilter status spesifik 
                    // atau jika filter statusnya cocok dengan status event admin [1 atau 2])
                    if (!isset($statusMap[$status]) || $statusMap[$status] == 1) {
                        $q->orWhere(function($subQuery) {
                            $subQuery->whereHas('user', function($sq) {
                                $sq->where('role', '1');
                            })->whereIn('status', [1, 2]);
                        });
                    }
                })
                ->where('status', '!=', 4);

            if ($type) {
                $query->where('type', $type);
            }

            $events = $query->latest()->get();
            return view('panitia.event.index', compact('events', 'type', 'status'));
        }
    }

    /**
     * Menampilkan daftar event untuk publik (Peserta tanpa login).
     */
    public function publicIndex(Request $request)
    {
        $today = date('Y-m-d');
        $schoolId = $request->get('school_id');
        
        // Query dasar untuk Event Aktif (Harus status 1 [Disetujui] DAN Tanggal belum lewat)
        $activeQuery = Event::where('status', 1)
            ->where('tanggal_event', '>=', $today);

        // Query dasar untuk Event Arsip (Status 2 [Selesai Manual] ATAU Status 1 tapi Tanggal sudah lewat)
        $pastQuery = Event::where(function($query) use ($today) {
                $query->where('status', 2) // Selesai manual
                      ->orWhere(function($q) use ($today) {
                          $q->where('status', 1)->where('tanggal_event', '<', $today);
                      });
            });

        // Filter berdasarkan school_id
        if ($schoolId) {
            // Jika ada pencarian ID Sekolah, tampilkan event umum (school_id is null) DAN event sekolah tersebut
            $activeQuery->where(function($q) use ($schoolId) {
                $q->where('school_id', $schoolId)
                  ->orWhereNull('school_id');
            });
            $pastQuery->where(function($q) use ($schoolId) {
                $q->where('school_id', $schoolId)
                  ->orWhereNull('school_id');
            });
        } else {
            // Jika tidak ada pencarian ID Sekolah, HANYA tampilkan event umum
            $activeQuery->whereNull('school_id');
            $pastQuery->whereNull('school_id');
        }

        $activeEvents = $activeQuery->with('user')->latest()->get();
        $pastEvents = $pastQuery->with('user')->latest()->get();

        // Jika diakses dari Dashboard Peserta
        if (request()->routeIs('peserta.event.index')) {
            $tournamentEvents = $activeEvents->filter(function ($event) {
                return $event->type_normalized === 'tournament';
            });
            $umumEvents = $activeEvents->filter(function ($event) {
                return $event->type_normalized === 'umum';
            });
            return view('peserta.event.index', compact('tournamentEvents', 'umumEvents'));
        }

        return view('public.event.index', compact('activeEvents', 'pastEvents'));
    }

    /**
     * Menampilkan detail satu event untuk publik.
     */
    public function show($uuid)
    {
        // Cari berdasarkan UUID, bukan ID
        $event = Event::with('user')->where('uuid', $uuid)->firstOrFail();
        
        $pendaftarLunas = \App\Models\Registration::where('event_id', $event->id)
            ->where('payment_status', 'settlement')
            ->count();
            
        $sisaSlot = $event->slot_tim - $pendaftarLunas;
        $isFull = $sisaSlot <= 0;

        return view('public.event.show', compact('event', 'pendaftarLunas', 'sisaSlot', 'isFull'));
    }

    /**
     * Menampilkan detail event khusus untuk Admin.
     */
    public function showAdmin($uuid)
    {
        if (Auth::user()->role != '1') abort(403);
        $event = Event::with('user')->where('uuid', $uuid)->firstOrFail();
        
        $pendaftarLunas = \App\Models\Registration::where('event_id', $event->id)
            ->where('payment_status', 'settlement')
            ->count();
            
        $sisaSlot = $event->slot_tim - $pendaftarLunas;
        $isFull = $sisaSlot <= 0;

        return view('admin.event.show', compact('event', 'pendaftarLunas', 'sisaSlot', 'isFull'));
    }

    /**
     * Menampilkan detail event khusus untuk Panitia.
     */
    public function showPanitia($uuid)
    {
        if (Auth::user()->role != '0') abort(403);
        
        // Cari berdasarkan UUID
        $event = Event::with('user')->where('uuid', $uuid)->firstOrFail();
        
        $pendaftarLunas = \App\Models\Registration::where('event_id', $event->id)
            ->where('payment_status', 'settlement')
            ->count();
            
        $sisaSlot = $event->slot_tim - $pendaftarLunas;
        $isFull = $sisaSlot <= 0;

        return view('panitia.event.show', compact('event', 'pendaftarLunas', 'sisaSlot', 'isFull'));
    }

    /**
     * Menampilkan daftar event yang butuh persetujuan (Khusus Role Admin).
     */
    public function persetujuan()
    {
        if (Auth::user()->role != '1') abort(403);
        
        $events = Event::where('status', 0)->with('user')->latest()->get();
        return view('admin.event.persetujuan', compact('events'));
    }

    /**
     * Menampilkan form tambah event berdasarkan Role.
     */
    public function create(Request $request)
    {
        $type = $request->get('type', 'tournament');
        $view = Auth::user()->role == '1' ? 'admin.event.create' : 'panitia.event.create';
        return view($view, compact('type'));
    }

    /**
     * Menampilkan form tambah event umum.
     */
    public function createUmum(Request $request)
    {
        $type = 'umum';
        $view = Auth::user()->role == '1' ? 'admin.event.create' : 'panitia.event.create';
        return view($view, compact('type'));
    }

    /**
     * Menyimpan data event baru ke database.
     */
    public function store(Request $request)
    {
        $request->validate([
            'nama_event' => 'required|string|max:255',
            'type' => 'nullable|string|in:tournament,umum',
            'school_id' => 'nullable|string|max:50',
            'foto_event' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'tanggal_event' => 'required|date',
            'jam_event' => 'required',
            'nama_panitia' => 'required|string|max:255',
            'harga_pendaftaran' => 'required|numeric|min:0',
            'slot_tim' => 'required|integer|min:1',
            'lokasi' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $user = Auth::user();
        
        $fotoPath = null;
        if ($request->hasFile('foto_event')) {
            $foto = $request->file('foto_event');
            $namaFoto = time() . '_' . $foto->getClientOriginalName();
            $foto->move(storage_path('app/uploads/events'), $namaFoto);
            $fotoPath = 'uploads/events/' . $namaFoto;
        }

        $status = ($user->role == '1') ? 1 : 0;

        $type = Str::of($request->type ?? 'tournament')->trim()->lower();
        $type = $type->contains('umum') ? 'umum' : 'tournament';

        Event::create([
            'nama_event' => $request->nama_event,
            'type' => $type,
            'school_id' => $request->school_id,
            'foto_event' => $fotoPath,
            'tanggal_event' => $request->tanggal_event,
            'jam_event' => $request->jam_event,
            'nama_panitia' => $request->nama_panitia,
            'harga_pendaftaran' => $request->harga_pendaftaran,
            'slot_tim' => $request->slot_tim,
            'lokasi' => $request->lokasi,
            'deskripsi' => $request->deskripsi,
            'user_id' => $user->id,
            'status' => $status,
        ]);

        if ($user->role == '0') {
            $admin = User::where('role', '1')->first();
            if ($admin) {
                $admin->notify(new SystemNotification([
                    'title' => 'Pengajuan Event Baru',
                    'message' => 'Panitia ' . $user->nama . ' mengajukan event: ' . $request->nama_event,
                    'icon' => 'bi-calendar-event-fill',
                    'type' => 'warning',
                    'url' => route('admin.event.persetujuan')
                ]));
            }
        }

        $route = ($user->role == '1') ? 'admin.event.index' : 'panitia.event.index';
        return redirect()->route($route)->with('success', 'Event berhasil disimpan!');
    }

    /**
     * Menampilkan form edit event.
     */
    public function edit($uuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role != '1' && $user->id != $event->user_id) {
            abort(403);
        }

        $view = ($user->role == '1') ? 'admin.event.edit' : 'panitia.event.edit';
        return view($view, compact('event'));
    }

    /**
     * Memperbarui data event di database.
     */
    public function update(Request $request, $uuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        if ($user->role != '1' && $user->id != $event->user_id) {
            abort(403);
        }

        $request->validate([
            'nama_event' => 'required|string|max:255',
            'school_id' => 'nullable|string|max:50',
            'foto_event' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'tanggal_event' => 'required|date',
            'jam_event' => 'required',
            'nama_panitia' => 'required|string|max:255',
            'harga_pendaftaran' => 'required|numeric|min:0',
            'slot_tim' => 'required|integer|min:1',
            'lokasi' => 'required|string|max:255',
            'deskripsi' => 'nullable|string',
        ]);

        $data = $request->except(['_token', '_method', 'foto_event']);

        if ($request->hasFile('foto_event')) {
            if ($event->foto_event && file_exists(storage_path('app/' . $event->foto_event))) {
                unlink(storage_path('app/' . $event->foto_event));
            }
            $foto = $request->file('foto_event');
            $namaFoto = time() . '_' . $foto->getClientOriginalName();
            $foto->move(storage_path('app/uploads/events'), $namaFoto);
            $data['foto_event'] = 'uploads/events/' . $namaFoto;
        }

        $event->update($data);

        $route = ($user->role == '1') ? 'admin.event.index' : 'panitia.event.index';
        return redirect()->route($route)->with('success', 'Event berhasil diperbarui!');
    }

    /**
     * Update status event menjadi Diterima (Status 1).
     */
    public function approve($uuid)
    {
        if (Auth::user()->role != '1') abort(403);
        
        $event = Event::where('uuid', $uuid)->firstOrFail();
        $event->update(['status' => 1]);

        $panitia = User::find($event->user_id);
        if ($panitia) {
            $panitia->notify(new SystemNotification([
                'title' => 'Event Disetujui',
                'message' => 'Event Anda "' . $event->nama_event . '" telah disetujui oleh Admin.',
                'icon' => 'bi-check-circle-fill',
                'type' => 'success',
                'url' => route('panitia.event.index')
            ]));
        }

        return redirect()->route('admin.event.index')->with('success', 'Event telah disetujui dan kini dipublikasikan!');
    }

    /**
     * Update status event menjadi Ditolak (Status 3).
     */
    public function reject(Request $request, $uuid)
    {
        if (Auth::user()->role != '1') abort(403);
        
        $event = Event::where('uuid', $uuid)->firstOrFail();
        
        $alasan = $request->input('alasan_ditolak') ?? 'Tidak ada alasan spesifik.';
        
        $event->update([
            'status' => 3,
            'alasan_ditolak' => $alasan
        ]);

        $panitia = User::find($event->user_id);
        if ($panitia) {
            $panitia->notify(new SystemNotification([
                'title' => 'Event Ditolak',
                'message' => 'Maaf, event Anda "' . $event->nama_event . '" ditolak oleh Admin. Alasan: ' . $alasan,
                'icon' => 'bi-x-circle-fill',
                'type' => 'danger',
                'url' => route('panitia.event.index')
            ]));
        }

        return redirect()->back()->with('success', 'Event telah ditolak.');
    }

    /**
     * Update status event menjadi Selesai (Status 2).
     */
    public function finish($uuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();

        if (Auth::user()->role != '1' && Auth::user()->id != $event->user_id) {
            abort(403);
        }

        $event->update(['status' => 2]);

        return redirect()->back()->with('success', 'Event telah diselesaikan dan masuk ke arsip publik.');
    }

    /**
     * Menghapus data event.
     */
    public function destroy($uuid)
    {
        $event = Event::where('uuid', $uuid)->firstOrFail();
        $event->update(['status' => 4]);

        $route = (Auth::user()->role == '1') ? 'admin.event.index' : 'panitia.event.index';
        return redirect()->route($route)->with('success', 'Event berhasil dihapus!');
    }

    public function getStatusCounts()
    {
        $user = auth()->user();
        $query = \App\Models\Event::query();
        if ($user->role != '1') {
            $query->where('user_id', $user->id);
        }

        $pending = (clone $query)->where('status', 0)->count();
        $success = (clone $query)->where('status', 1)->count();
        $fail = (clone $query)->where('status', 3)->count(); 

        return response()->json([
            'pending' => $pending,
            'success' => $success,
            'fail'    => $fail
        ]);
    }
}