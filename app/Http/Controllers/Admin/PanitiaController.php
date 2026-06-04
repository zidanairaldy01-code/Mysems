<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;

class PanitiaController extends Controller
{
    /**
     * Menampilkan daftar panitia.
     */
    public function index()
    {
        // Mengambil semua user dengan role '0' (Panitia)
        $panitias = User::where('role', '0')->get();
        return view('admin.panitia.index', compact('panitias'));
    }

    /**
     * Menampilkan form buat akun panitia.
     */
    public function create()
    {
        return view('admin.panitia.create');
    }

    /**
     * Menyimpan akun panitia baru ke database.
     */
    public function store(Request $request)
    {
        // Validasi input
        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'hp' => 'required|string|max:15',
            'password' => 'required|string|min:8',
        ], [
            'email.unique' => 'Email ini sudah terdaftar sebagai akun lain.',
            'password.min' => 'Password minimal harus 8 karakter.',
        ]);

        try {
            // Simpan data ke tabel users
            $user = User::create([
                'nama' => $request->nama,
                'email' => $request->email,
                'hp' => $request->hp,
                'alamat' => $request->alamat,
                'password' => Hash::make($request->password),
                'role' => '0', // Set sebagai Panitia
                'status' => 1,
            ]);

            if ($user) {
                return redirect()->route('admin.panitia.index')->with('success', 'Akun panitia berhasil dibuat!');
            }
        } catch (\Exception $e) {
            Log::error('Gagal membuat akun panitia: ' . $e->getMessage());
            return redirect()->back()->withInput()->withErrors(['error' => 'Gagal menyimpan ke database: ' . $e->getMessage()]);
        }

        return redirect()->back()->withInput()->withErrors(['error' => 'Gagal membuat akun panitia. Silakan coba lagi.']);
    }

    /**
     * Menampilkan form edit akun panitia.
     */
    public function edit($id)
    {
        $panitia = User::findOrFail($id);
        return view('admin.panitia.edit', compact('panitia'));
    }

    /**
     * Memperbarui data akun panitia.
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $request->validate([
            'nama' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
            'hp' => 'required|string|max:15',
        ], [
            'email.unique' => 'Email ini sudah terdaftar sebagai akun lain.',
        ]);

        $data = [
            'nama' => $request->nama,
            'email' => $request->email,
            'hp' => $request->hp,
            'alamat' => $request->alamat,
        ];

        // Update password jika diisi
        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:8']);
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('admin.panitia.index')->with('success', 'Data panitia berhasil diperbarui!');
    }

    /**
     * Menghapus akun panitia.
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        
        return redirect()->route('admin.panitia.index')->with('success', 'Akun panitia berhasil dihapus!');
    }
}
