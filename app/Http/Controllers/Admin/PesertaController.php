<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;

class PesertaController extends Controller
{
    /**
     * Menampilkan daftar peserta (Role 2).
     */
    public function index()
    {
        $pesertas = User::where('role', '2')->latest()->get();
        $view = auth()->user()->role == '1' ? 'admin.peserta.index' : 'panitia.peserta.index';
        return view($view, compact('pesertas'));
    }

    /**
     * Menampilkan detail peserta.
     */
    public function show($id)
    {
        $peserta = User::where('role', '2')->findOrFail($id);
        $rolePrefix = auth()->user()->role == '1' ? 'admin' : 'panitia';
        $layout = 'layouts.' . $rolePrefix;
        
        return view('admin.peserta.show', compact('peserta', 'layout', 'rolePrefix'));
    }

    /**
     * Menghapus data peserta.
     */
    public function destroy($id)
    {
        $peserta = User::where('role', '2')->findOrFail($id);
        $peserta->delete();

        return redirect()->route('admin.peserta.index')->with('success', 'Peserta berhasil dihapus!');
    }
}
