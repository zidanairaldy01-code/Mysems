<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     */
    public function handle(Request $request, Closure $next, string $role): Response
    {
        // 1. Cek apakah user sudah login
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        $user = Auth::user();

        // 2. Cek apakah role user sesuai dengan yang diizinkan route
        if ($user->role !== $role) {
            // Jika tidak sesuai, kembalikan ke habitat aslinya
            if ($user->role === '1') {
                return redirect('admin/index');
            } elseif ($user->role === '0') {
                return redirect('panitia/index');
            }
            
            // Jika role tidak dikenali
            return redirect('/');
        }

        return $next($request);
    }
}