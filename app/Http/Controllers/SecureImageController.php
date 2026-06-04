<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SecureImageController extends Controller
{
    /**
     * Menyajikan file secara aman dari folder storage hanya untuk pengguna yang login
     * atau ketika diakses secara sah dari dalam website (cek Referer).
     */
    public function serveUploads($directory, $filename)
    {
        // 1. Jika pengguna sudah login, izinkan akses
        if (Auth::check()) {
            return $this->responseFile($directory, $filename);
        }

        // 2. Jika tidak login, periksa Referer Header (hanya boleh jika dipanggil dari website kita sendiri)
        $referer = request()->headers->get('referer');
        $host = request()->getHost();

        if ($referer && parse_url($referer, PHP_URL_HOST) === $host) {
            return $this->responseFile($directory, $filename);
        }

        // 3. Jika diakses langsung tanpa referer (misal copypaste link ke tab/browser baru) dan tidak login
        return redirect()->route('login')->with('error', 'Anda harus login untuk mengakses file ini secara langsung.');
    }

    /**
     * KHUSUS PUBLIK: Menyajikan gambar hanya jika dipanggil dari tag <img> website kita.
     * Jika URL dicopy-paste ke browser (direct access), blokir aksesnya.
     */
    public function servePublicImage($directory, $filename)
    {
        $referer = request()->headers->get('referer');
        $host = request()->getHost();

        // Jika referer ada dan berasal dari host (domain) kita sendiri, izinkan.
        if ($referer && parse_url($referer, PHP_URL_HOST) === $host) {
            return $this->responseFile($directory, $filename);
        }

        // Jika diakses langsung (copypaste), referer akan kosong. Kita blokir dengan error 403.
        abort(403, 'Akses gambar secara langsung tidak diizinkan. Silakan lihat melalui halaman web.');
    }

    /**
     * Menyajikan file fisik dari storage setelah validasi aman.
     */
    private function responseFile($directory, $filename)
    {
        // Filter folder untuk mencegah directory traversal attacks
        if (!in_array($directory, ['events', 'profile'])) {
            abort(403, 'Akses ditolak.');
        }

        // Sanitasi nama file (Aman meskipun di database path-nya mengandung folder)
        $filename = basename($filename);
        $path = storage_path('app/uploads/' . $directory . '/' . $filename);

        if (!file_exists($path)) {
            abort(404, 'File tidak ditemukan.');
        }

        // Header ini membantu mencegah browser menyimpan cache gambar terlalu agresif
        return response()->file($path, [
            'Cache-Control' => 'no-cache, no-store, must-revalidate',
            'Pragma' => 'no-cache',
            'Expires' => '0'
        ]);
    }
}