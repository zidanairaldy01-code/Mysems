@extends('layouts.peserta')

@section('title', 'Profil Saya - MySMES')

@section('content')
<div class="row">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 text-center p-4 mb-4">
            @if(Auth::user()->foto)
                <img src="{{ asset(Auth::user()->foto) }}" class="rounded-circle mx-auto mb-3 object-fit-cover shadow-sm" width="120" height="120">
            @else
                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama) }}&size=128&background=0D6EFD&color=fff" class="rounded-circle mx-auto mb-3" width="120" height="120">
            @endif
            
            <h4 class="fw-bold mb-1">{{ Auth::user()->nama }}</h4>
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">Peserta MySMES</span>
            
            <div class="mt-3">
                <form action="{{ route('peserta.profile.update') }}" method="POST" enctype="multipart/form-data" id="form-foto-peserta">
                    @csrf
                    <input type="hidden" name="nama" value="{{ Auth::user()->nama }}">
                    <input type="hidden" name="email" value="{{ Auth::user()->email }}">
                    <input type="file" name="foto" id="input-foto-peserta" class="d-none" onchange="document.getElementById('form-foto-peserta').submit()">
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 me-1" onclick="document.getElementById('input-foto-peserta').click()">Ganti Foto</button>
                    @if(Auth::user()->foto)
                        <button type="button" class="btn btn-sm btn-link text-danger text-decoration-none" onclick="document.getElementById('delete-foto-peserta').submit()">Hapus</button>
                    @endif
                </form>
                <form action="{{ route('peserta.profile.delete-foto') }}" method="POST" id="delete-foto-peserta" class="d-none">
                    @csrf
                </form>
            </div>

            <hr class="my-4 opacity-25">
            <div class="text-start">
                <p class="mb-2 text-muted small text-uppercase fw-bold">Kontak</p>
                <p class="mb-3"><i class="bi bi-envelope me-2 text-primary"></i>{{ Auth::user()->email }}</p>
                <p class="mb-0"><i class="bi bi-telephone me-2 text-primary"></i>{{ Auth::user()->hp ?? 'Belum diisi' }}</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold mb-4">Pengaturan Profil</h5>
            
            <form action="{{ route('peserta.profile.update') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Nama Lengkap</label>
                        <input type="text" class="form-control" name="nama" value="{{ Auth::user()->nama }}" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Email</label>
                        <input type="email" class="form-control bg-light" value="{{ Auth::user()->email }}" disabled>
                        <div class="form-text small">Email tidak dapat diubah.</div>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Nomor HP / WhatsApp</label>
                        <input type="text" class="form-control" name="hp" value="{{ Auth::user()->hp }}">
                    </div>
                    <div class="col-md-6">
                        <label class="form-label small fw-semibold text-muted">Username</label>
                        <input type="text" class="form-control" name="username" value="{{ Auth::user()->username }}">
                    </div>
                    <div class="col-12">
                        <label class="form-label small fw-semibold text-muted">Alamat Lengkap</label>
                        <textarea class="form-control" name="alamat" rows="3">{{ Auth::user()->alamat }}</textarea>
                    </div>
                    
                    <div class="col-12 mt-4 pt-3 border-top text-end">
                        <button type="submit" class="btn btn-primary px-4 rounded-pill">Simpan Perubahan</button>
                    </div>
                </div>
            </form>
        </div>

        {{-- Bagian Keamanan (Ganti Password) untuk Peserta --}}
        <div class="card border-0 shadow-sm rounded-4 p-4 mt-4">
            <h5 class="fw-bold mb-4">Keamanan Akun</h5>
            <form action="{{ route('peserta.profile.update') }}" method="POST">
                @csrf
                <div class="row g-3">
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted">Password Saat Ini</label>
                        <input type="password" name="current_password" class="form-control" placeholder="********">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted">Password Baru</label>
                        <input type="password" name="new_password" class="form-control" placeholder="Minimal 8 karakter">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label small fw-semibold text-muted">Konfirmasi Password Baru</label>
                        <input type="password" name="new_password_confirmation" class="form-control" placeholder="Ulangi password">
                    </div>
                    <div class="col-12 mt-4 text-end">
                        <button type="submit" class="btn btn-outline-primary px-4 rounded-pill">Update Password</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
