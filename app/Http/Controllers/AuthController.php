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
        $num1 = rand(1, 9);
        $num2 = rand(1, 9);
        session(['captcha_answer' => $num1 + $num2]);

        return view('auth.login', compact('num1', 'num2'));
    }

    public function login(Request $request)
    {
        // 1. Validasi input wajib diisi
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
            'captcha'  => 'required|integer',
        ], [
            'email.required'    => 'Email wajib diisi.',
            'email.email'       => 'Format email tidak valid.',
            'password.required' => 'Password wajib diisi.',
            'captcha.required'  => 'Jawaban CAPTCHA wajib diisi.',
            'captcha.integer'   => 'Jawaban CAPTCHA harus berupa angka.',
        ]);

        // 2. Validasi jawaban CAPTCHA matematika
        if ((int) $request->captcha !== (int) session('captcha_answer')) {
            // Regenerasi angka baru agar tidak bisa ditebak ulang
            $num1 = rand(1, 9);
            $num2 = rand(1, 9);
            session(['captcha_answer' => $num1 + $num2]);

            return back()
                ->withInput($request->only('email'))
                ->withErrors(['captcha' => 'Jawaban CAPTCHA matematika salah. Silakan coba lagi.'])
                ->with(['captcha_num1' => $num1, 'captcha_num2' => $num2]);
        }

        // 3. Cek apakah email ada di database
        $user = User::where('email', $request->email)->first();

        if (!$user) {
            return back()->withInput($request->only('email'))->withErrors([
                'email' => 'Email salah'
            ]);
        }

        // 4. Cek apakah password sesuai dengan email yang ditemukan
        if (!Hash::check($request->password, $user->password)) {
            return back()->withInput($request->only('email'))->withErrors([
                'password' => 'Password salah'
            ]);
        }

        // 5. Jika berhasil, buat session login
        Auth::login($user, $request->has('remember'));
        $request->session()->regenerate();

        // 6. Redirect berdasarkan role
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