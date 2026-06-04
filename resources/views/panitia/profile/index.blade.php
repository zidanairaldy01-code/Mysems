@extends('layouts.panitia')

@section('title', 'Profil Saya - MySMES')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold">Profil Saya</h2>
    <p class="text-muted">Kelola informasi pribadi dan keamanan akun Anda.</p>
</div>

<div class="row g-4">
    <!-- Bagian 1: Informasi Pribadi (Nama, Email, HP, Alamat) -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold mb-0">Informasi Pribadi</h5>
            </div>
            <div class="card-body p-4">
                {{-- Form untuk mengupdate data profil dasar --}}
                <form action="{{ route('panitia.profile.update') }}" method="POST">
                    @csrf
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Lengkap</label>
                            <input type="text" name="nama" class="form-control rounded-3" value="{{ Auth::user()->nama }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Email</label>
                            <input type="email" name="email" class="form-control rounded-3" value="{{ Auth::user()->email }}">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">No. HP</label>
                            <input type="tel" name="hp" class="form-control rounded-3" value="{{ Auth::user()->hp }}">
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Alamat</label>
                            <textarea name="alamat" class="form-control rounded-3" rows="3">{{ Auth::user()->alamat }}</textarea>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-4 fw-bold">Simpan Perubahan</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bagian 2: Keamanan Akun (Ganti Password) -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold mb-0">Keamanan Akun</h5>
            </div>
            <div class="card-body p-4">
                {{-- Form untuk mengupdate password --}}
                <form action="{{ route('panitia.profile.update') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password Saat Ini</label>
                        <input type="password" name="current_password" class="form-control rounded-3" placeholder="********">
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password Baru</label>
                        <input type="password" name="new_password" class="form-control rounded-3" placeholder="Minimal 8 karakter">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Konfirmasi Password</label>
                        <input type="password" name="new_password_confirmation" class="form-control rounded-3" placeholder="Ulangi password baru">
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-outline-primary rounded-pill fw-bold">Update Password</button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Bagian 3: Pengaturan Foto Profil -->
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white border-0 py-3">
                <h5 class="fw-bold mb-0">Foto Profil</h5>
            </div>
            <div class="card-body p-4">
                <div class="d-flex align-items-center flex-column flex-sm-row">
                    {{-- Preview foto profil --}}
                    @if(Auth::user()->foto)
                        <img src="{{ asset(Auth::user()->foto) }}" class="rounded-4 mb-3 mb-sm-0 me-sm-4 shadow-sm object-fit-cover" width="128" height="128">
                    @else
                        <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama) }}&background=0D6EFD&color=fff&size=128" 
                             class="rounded-4 mb-3 mb-sm-0 me-sm-4 shadow-sm" width="128" height="128">
                    @endif
                    
                    <div>
                        <p class="text-muted small mb-3">Gunakan foto wajah yang jelas. Format JPG atau PNG, maksimal 2MB.</p>
                        <div class="d-flex gap-2">
                            <form action="{{ route('panitia.profile.update') }}" method="POST" enctype="multipart/form-data" id="form-foto">
                                @csrf
                                <input type="hidden" name="nama" value="{{ Auth::user()->nama }}">
                                <input type="hidden" name="email" value="{{ Auth::user()->email }}">
                                <input type="file" name="foto" id="input-foto" class="d-none" onchange="document.getElementById('form-foto').submit()">
                                <button type="button" class="btn btn-sm btn-primary rounded-pill px-3" onclick="document.getElementById('input-foto').click()">Ganti Foto</button>
                            </form>
                            
                            @if(Auth::user()->foto)
                            <form action="{{ route('panitia.profile.delete-foto') }}" method="POST">
                                @csrf
                                <button type="submit" class="btn btn-sm btn-light rounded-pill px-3">Hapus</button>
                            </form>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
