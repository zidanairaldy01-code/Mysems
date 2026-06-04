<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = User::findOrFail(Auth::id());

        // Logic 1: Update Data Dasar & Foto
        if ($request->has('nama')) {
            $request->validate([
                'nama' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'hp' => 'nullable|string|max:15',
                'alamat' => 'nullable|string',
                'foto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            ]);

            $user->nama = $request->nama;
            $user->email = $request->email;
            $user->hp = $request->hp;
            $user->alamat = $request->alamat;

            // Handle Upload Foto
            if ($request->hasFile('foto')) {
                // Hapus foto lama jika ada
                if ($user->foto && file_exists(storage_path('app/' . $user->foto))) {
                    unlink(storage_path('app/' . $user->foto));
                }

                $file = $request->file('foto');
                $filename = time() . '_' . str_replace(' ', '_', $user->nama) . '.' . $file->getClientOriginalExtension();
                $file->move(storage_path('app/uploads/profile'), $filename);
                $user->foto = 'uploads/profile/' . $filename;
            }

            $user->save();
            return redirect()->back()->with('success', 'Profil berhasil diperbarui!');
        }

        // Logic 2: Update Password
        if ($request->has('new_password')) {
            $request->validate([
                'current_password' => 'required',
                'new_password' => 'required|min:8|confirmed',
            ]);

            if (!Hash::check($request->current_password, $user->password)) {
                return redirect()->back()->with('error', 'Password saat ini salah!');
            }

            $user->password = Hash::make($request->new_password);
            $user->save();
            return redirect()->back()->with('success', 'Password berhasil diubah!');
        }

        return redirect()->back();
    }

    public function deleteFoto()
    {
        $user = User::findOrFail(Auth::id());
        if ($user->foto && file_exists(storage_path('app/' . $user->foto))) {
            unlink(storage_path('app/' . $user->foto));
        }
        $user->foto = null;
        $user->save();

        return redirect()->back()->with('success', 'Foto profil berhasil dihapus!');
    }
}
