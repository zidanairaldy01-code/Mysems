<?php
namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Registration;
use App\Models\TournamentMatch;
use App\Models\User;
use App\Notifications\SystemNotification;
use Illuminate\Http\Request;

class RegistrationController extends Controller
{
    /**
     * Menampilkan form pendaftaran untuk event tertentu
     */
    public function create($event_uuid)
    {
        $event = Event::where('uuid', $event_uuid)->firstOrFail();
        
        // Cek apakah slot masih tersedia
        $count = Registration::where('event_id', $event->id)->count();
        if ($count >= $event->slot_tim) {
            return redirect()->back()->with('error', 'Maaf, kuota pendaftaran untuk event ini sudah penuh!');
        }

        return view('public.event.register', compact('event'));
    }

    /**
     * Menyimpan pendaftaran dan menangani pembayaran Midtrans
     */
    public function store(Request $request)
    {
        $event = Event::findOrFail($request->event_id);

        $rules = [
            'event_id' => 'required|exists:events,id',
            'nama_kapten' => 'required|string|max:255',
            'nomor_wa' => 'required|string|max:20',
            'email' => 'required|email|max:255',
        ];

        // Hanya turnamen yang butuh nama tim dan anggota tim
        if ($event->type != 'umum') {
            $rules['nama_tim'] = 'required|string|max:255';
            $rules['anggota_tim'] = 'required|string';
        }

        // Tambahkan validasi school_id jika event bersifat terbatas sekolah
        if ($event->school_id) {
            $rules['school_id'] = [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($event) {
                    if ($value !== $event->school_id) {
                        $fail('ID Sekolah yang Anda masukkan tidak cocok dengan event ini!');
                    }
                },
            ];
        }

        $request->validate($rules);

        // Pengecekan Duplikat & Resume Pembayaran
        $namaTim = $event->type != 'umum' ? trim($request->nama_tim) : null;
        $email = strtolower(trim($request->email));
        $nomorWa = trim($request->nomor_wa);

        $existingRegistration = Registration::where('event_id', $event->id)
            ->where(function($query) use ($namaTim, $email, $nomorWa) {
                if ($namaTim) {
                    $query->where('nama_tim', 'LIKE', $namaTim);
                }
                $query->orWhere('email', $email)
                      ->orWhere('nomor_wa', $nomorWa);
            })->first();

        if ($existingRegistration) {
            if ($existingRegistration->payment_status == 'settlement') {
                return redirect()->back()->with('error', 'Data pendaftaran (Tim/Email/No WA) sudah terdaftar dan berstatus Lunas untuk event ini!');
            }
            
            if ($existingRegistration->payment_status == 'pending') {
                // Update dengan data terbaru dari form
                $existingRegistration->update([
                    'nama_tim' => $namaTim ?? $request->nama_kapten,
                    'nama_kapten' => $request->nama_kapten,
                    'anggota_tim' => $event->type == 'umum' ? '-' : $request->anggota_tim,
                    'nomor_wa' => $nomorWa,
                    'email' => $email,
                ]);
                
                $registration = $existingRegistration;
                
                if ($event->harga_pendaftaran > 0 && $registration->redirect_url) {
                    $redirectUrl = $registration->redirect_url;
                    session()->flash('info', 'Anda memiliki pendaftaran yang tertunda. Silakan lanjutkan pembayaran Anda.');
                    return view('public.event.checkout', compact('registration', 'event', 'redirectUrl'));
                }
            }
        }

        // 2. Cek Kuota
        $count = Registration::where('event_id', $event->id)->where('payment_status', 'settlement')->count();
        if ($count >= $event->slot_tim) {
            return redirect()->back()->with('error', 'Kuota penuh!');
        }

        // 3. Simpan Data Pendaftaran
        $registration = Registration::create([
            'event_id' => $event->id,
            'user_id' => auth()->id(),
            'nama_tim' => $event->type == 'umum' ? $request->nama_kapten : $request->nama_tim,
            'nama_kapten' => $request->nama_kapten,
            'anggota_tim' => $event->type == 'umum' ? '-' : $request->anggota_tim,
            'nomor_wa' => $request->nomor_wa,
            'email' => $request->email,
            'qr_code' => 'QR-' . strtoupper(bin2hex(random_bytes(4))) . '-' . time(),
            'status' => 0, // 0 = Belum aktif (nunggu bayar)
            'payment_status' => 'pending'
        ]);

        /**
         * TRIGGER NOTIFIKASI
         */
        $notifData = [
            'title' => 'Pendaftaran Baru',
            'message' => ($event->type == 'umum' ? 'Peserta ' : 'Tim ') . $registration->nama_tim . ' mendaftar di ' . $event->nama_event,
            'icon' => 'bi-person-plus-fill',
            'type' => 'primary',
        ];

        $admin = User::where('role', '1')->first();
        if ($admin) {
            $admin->notify(new SystemNotification(array_merge($notifData, ['url' => route('admin.registration.index')])));
        }

        $panitia = User::find($event->user_id);
        if ($panitia && (!$admin || $panitia->id != $admin->id)) {
            $panitia->notify(new SystemNotification(array_merge($notifData, ['url' => route('panitia.registration.index')])));
        }

        // 4. LOGIKA PEMBAYARAN (Jika Berbayar)
        if ($event->harga_pendaftaran > 0) {
            // Konfigurasi Midtrans
            \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
            \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => 'REG-' . $registration->id . '-' . time(),
                    'gross_amount' => (int) $event->harga_pendaftaran,
                ],
                'customer_details' => [
                    'first_name' => $request->nama_kapten,
                    'email' => $request->email,
                    'phone' => $request->nomor_wa,
                ],
                'item_details' => [
                    [
                        'id' => $event->id,
                        'price' => (int) $event->harga_pendaftaran,
                        'quantity' => 1,
                        'name' => 'Pendaftaran: ' . $event->nama_event,
                    ]
                ]
            ];

            try {
                $response = \Midtrans\Snap::createTransaction($params);
                $snapToken = $response->token;
                $redirectUrl = $response->redirect_url;
                
                $registration->update([
                    'snap_token' => $snapToken,
                    'redirect_url' => $redirectUrl,
                    'order_id' => $params['transaction_details']['order_id']
                ]);
                
                return view('public.event.checkout', compact('registration', 'event', 'redirectUrl'));
            } catch (\Exception $e) {
                return redirect()->back()->with('error', 'Gagal membuat transaksi: ' . $e->getMessage());
            }
        } else {
            // Jika Gratis, langsung aktifkan
            $registration->update(['payment_status' => 'settlement', 'status' => 1]);
            
            if ($event->type != 'umum') {
                $this->autoPlaceToBracket($registration);
            }
            
            if ($event->type == 'umum') {
                return redirect()->route('public.registration.success', $registration->id)->with('success', 'Pendaftaran berhasil!');
            }
            
            return redirect()->route('public.event.index')->with('success', 'Pendaftaran berhasil!');
        }
    }

    /**
     * Menampilkan halaman sukses pendaftaran dan QR Code
     */
    public function success($id)
    {
        $registration = Registration::with('event')->findOrFail($id);
        
        if ($registration->payment_status != 'settlement') {
            return redirect()->route('public.event.index')->with('error', 'Silakan selesaikan pembayaran terlebih dahulu.');
        }

        return view('public.event.success', compact('registration'));
    }

    /**
     * Cek status pembayaran manual (sebagai backup jika webhook terhambat)
     */
    public function check($id)
    {
        $registration = Registration::findOrFail($id);

        // Jika sudah lunas, tidak perlu cek lagi (tapi arahkan ke sukses jika umum)
        if ($registration->payment_status == 'settlement') {
            $event = Event::find($registration->event_id);
            if ($event && $event->type == 'umum') {
                return redirect()->route('public.registration.success', $registration->id);
            }
            return redirect()->route('public.event.index')->with('success', 'Pembayaran Anda sudah lunas!');
        }

        // Konfigurasi Midtrans
        \Midtrans\Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        \Midtrans\Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);

        try {
            // Jika order_id kosong (misal data lama), kita tidak bisa cek ke Midtrans
            if (empty($registration->order_id)) {
                return redirect()->route('public.event.index')->with('error', 'Transaksi tidak memiliki Order ID valid.');
            }

            // Ambil status transaksi dari Midtrans
            $status = \Midtrans\Transaction::status($registration->order_id);
            $transactionStatus = $status->transaction_status;

            if ($transactionStatus == 'settlement' || $transactionStatus == 'capture') {
                $registration->update([
                    'payment_status' => 'settlement',
                    'status' => 1
                ]);

                // Notifikasi Pembayaran Sukses
                $notifData = [
                    'title' => 'Pembayaran Sukses',
                    'message' => 'Pembayaran ' . ($registration->event->type == 'umum' ? 'Peserta ' : 'Tim ') . $registration->nama_tim . ' telah dikonfirmasi.',
                    'icon' => 'bi-cash-stack',
                    'type' => 'success',
                ];

                $admin = User::where('role', '1')->first();
                if ($admin) $admin->notify(new SystemNotification(array_merge($notifData, ['url' => route('admin.registration.index')])));
                
                $event = Event::find($registration->event_id);
                $panitia = User::find($event->user_id);
                if ($panitia && (!$admin || $panitia->id != $admin->id)) {
                    $panitia->notify(new SystemNotification(array_merge($notifData, ['url' => route('panitia.registration.index')])));
                }

                if ($event->type != 'umum') {
                    $this->autoPlaceToBracket($registration);
                    return redirect()->route('public.event.index')->with('success', 'Pembayaran berhasil! Tim Anda telah masuk ke klasemen.');
                } else {
                    return redirect()->route('public.registration.success', $registration->id)->with('success', 'Pembayaran berhasil! Silakan simpan QR Code Anda.');
                }
            } else {
                return redirect()->route('public.event.index')->with('error', 'Pembayaran Anda belum diselesaikan. Status saat ini: ' . strtoupper($transactionStatus));
            }
        } catch (\Exception $e) {
            return redirect()->route('public.event.index')->with('error', 'Gagal memverifikasi ke Midtrans: ' . $e->getMessage());
        }
    }

    /**
     * Logika Terpisah untuk Penempatan ke Bracket
     */
    private function autoPlaceToBracket($registration)
    {
        $match = TournamentMatch::where('event_id', $registration->event_id)
            ->where('round', 1)
            ->where(function($query) {
                $query->whereNull('team1_name')->orWhereNull('team2_name');
            })
            ->inRandomOrder()
            ->first();

        if ($match) {
            if (is_null($match->team1_name)) {
                $match->update(['team1_name' => $registration->nama_tim]);
            } else {
                $match->update(['team2_name' => $registration->nama_tim]);
            }
        }
    }
}
