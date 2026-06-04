<?php
namespace App\Http\Controllers;

use App\Models\Registration;
use App\Models\TournamentMatch;
use Illuminate\Http\Request;
use Midtrans\Config;
use Midtrans\Notification;

class PaymentCallbackController extends Controller
{
    public function handleNotification(Request $request)
    {
        // 1. Konfigurasi Midtrans
        Config::$serverKey = env('MIDTRANS_SERVER_KEY');
        Config::$isProduction = env('MIDTRANS_IS_PRODUCTION', false);

        try {
            $notification = new Notification();
        } catch (\Exception $e) {
            return response()->json(['message' => 'Invalid notification'], 400);
        }

        $transactionStatus = $notification->transaction_status;
        $orderId = $notification->order_id;
        $fraudStatus = $notification->fraud_status;

        // Ambil ID Registrasi dari Order ID (Format: REG-{id}-{time})
        $registrationId = explode('-', $orderId)[1];
        $registration = Registration::find($registrationId);

        if (!$registration) {
            return response()->json(['message' => 'Registration not found'], 404);
        }

        // 2. Logika Status Transaksi
        if ($transactionStatus == 'capture') {
            if ($fraudStatus == 'challenge') {
                $registration->update(['payment_status' => 'challenge']);
            } else {
                $this->successProcess($registration);
            }
        } else if ($transactionStatus == 'settlement') {
            $this->successProcess($registration);
        } else if ($transactionStatus == 'cancel' || $transactionStatus == 'deny' || $transactionStatus == 'expire') {
            $registration->update(['payment_status' => $transactionStatus]);
        } else if ($transactionStatus == 'pending') {
            $registration->update(['payment_status' => 'pending']);
        }

        return response()->json(['message' => 'Notification handled']);
    }

    /**
     * Proses ketika pembayaran berhasil
     */
    private function successProcess($registration)
    {
        $registration->update([
            'payment_status' => 'settlement',
            'status' => 1 // Aktifkan pendaftaran
        ]);

        // TRIGGER NOTIFIKASI PEMBAYARAN LUNAS
        $notifData = [
            'title' => 'Pembayaran Lunas',
            'message' => 'Pembayaran Tim ' . $registration->nama_tim . ' telah diterima (Lunas).',
            'icon' => 'bi-cash-stack',
            'type' => 'success',
            'url' => route('admin.registration.index')
        ];

        // Notif Admin
        $admin = \App\Models\User::where('role', '1')->first();
        if ($admin) $admin->notify(new \App\Notifications\SystemNotification($notifData));

        // Notif Panitia
        $panitia = \App\Models\User::find($registration->event->user_id);
        if ($panitia && (!$admin || $panitia->id != $admin->id)) {
            $panitia->notify(new \App\Notifications\SystemNotification(array_merge($notifData, ['url' => route('panitia.registration.index')])));
        }

        // Masukkan ke bracket (Hanya jika belum ada di bracket)
        $this->autoPlaceToBracket($registration);
    }

    /**
     * Logika Penempatan ke Bracket (Sama seperti di RegistrationController)
     */
    private function autoPlaceToBracket($registration)
    {
        // Cek apakah tim sudah ada di bracket (untuk menghindari duplikasi saat callback dipanggil berulang)
        $exists = TournamentMatch::where('event_id', $registration->event_id)
            ->where(function($query) use ($registration) {
                $query->where('team1_name', $registration->nama_tim)
                      ->orWhere('team2_name', $registration->nama_tim);
            })->exists();

        if ($exists) return;

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
