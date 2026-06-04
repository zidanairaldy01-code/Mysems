<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        // 1. Validasi input wajib diisi
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ], [
            'email.required' => 'Email wajib diisi.',
            'email.email' => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.'
        ]);

        // 2. Cek apakah email ada di database
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            // Jika email tidak ditemukan
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'Email salah'
            ]);
        }

        // 3. Cek apakah password sesuai dengan email yang ditemukan
        if (!Hash::check($request->password, $user->password)) {
            // Jika password salah
            return back()->withInput($request->only('email'))->withErrors([
                'password' => 'Password salah'
            ]);
        }

        // 4. Jika berhasil, buat session login
        Auth::login($user, $request->has('remember'));
        $request->session()->regenerate();

        // 5. Redirect berdasarkan role
        if ($user->role === '1') {
            return redirect()->intended('admin/index');
        } elseif ($user->role === '0') {
            return redirect()->intended('panitia/index');
        } elseif ($user->role === '2') {
            return redirect()->intended('peserta/index');
        }

        // Fallback jika tidak punya role (opsional)
        return redirect('/');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('login');
    }
}