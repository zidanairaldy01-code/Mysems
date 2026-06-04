<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Notifications\DatabaseNotification;

class NotificationController extends Controller
{
    /**
     * Menampilkan semua notifikasi untuk user yang login.
     */
    public function index()
    {
        $allNotifications = Auth::user()->notifications()->latest()->paginate(10);
        
        // Tandai semua sebagai dibaca saat membuka halaman ini
        Auth::user()->unreadNotifications->markAsRead();

        $layout = $this->getLayout();
        $rolePrefix = $this->getRolePrefix();

        return view('notifications.index', compact('allNotifications', 'layout', 'rolePrefix'));
    }

    /**
     * Menandai satu notifikasi sebagai dibaca dan redirect ke URL-nya.
     */
    public function read($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect($notification->data['url'] ?? route('public.index'));
    }

    /**
     * Menghapus semua notifikasi milik user.
     */
    public function deleteAll()
    {
        $userId = Auth::id();
        $userType = get_class(Auth::user());
        
        $deleted = DB::table('notifications')
            ->where('notifiable_id', $userId)
            ->where('notifiable_type', $userType)
            ->delete();
            
        if ($deleted) {
            return redirect()->back()->with('success', 'Semua notifikasi (' . $deleted . ') berhasil dihapus.');
        }

        return redirect()->back()->with('info', 'Tidak ada notifikasi yang perlu dihapus.');
    }
    /**
     * Menghapus satu notifikasi.
     */
    public function destroy($id)
    {
        $userId = Auth::id();
        
        $deleted = DB::table('notifications')
            ->where('id', $id)
            ->where('notifiable_id', $userId)
            ->delete();
        
        if ($deleted) {
            return redirect()->back()->with('success', 'Notifikasi berhasil dihapus.');
        }

        return redirect()->back()->with('error', 'Notifikasi tidak ditemukan.');
    }


    /**
     * Mendapatkan layout berdasarkan role user.
     */
    private function getLayout()
    {
        $role = Auth::user()->role;
        if ($role == '1') return 'layouts.admin';
        if ($role == '0') return 'layouts.panitia';
        return 'layouts.peserta';
    }

    /**
     * Mendapatkan prefix rute berdasarkan role user.
     */
    private function getRolePrefix()
    {
        $role = Auth::user()->role;
        if ($role == '1') return 'admin';
        if ($role == '0') return 'panitia';
        return 'peserta';
    }
}
