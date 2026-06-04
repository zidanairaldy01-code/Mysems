@extends('layouts.admin')

@section('title', 'Buat Akun Panitia - MySEMS')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold">Buat Akun Panitia</h2>
    <p class="text-muted">Daftarkan panitia baru untuk membantu mengelola event.</p>
</div>

<div class="row">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger rounded-4 border-0 shadow-sm mb-4">
                        <ul class="mb-0">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                {{-- Form untuk membuat akun panitia baru --}}
                <form action="{{ route('admin.panitia.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control rounded-3 @error('nama') is-invalid @enderror" placeholder="Contoh: Budi Utomo" value="{{ old('nama') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control rounded-3 @error('email') is-invalid @enderror" placeholder="panitia@example.com" value="{{ old('email') }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password</label>
                        <input type="password" name="password" class="form-control rounded-3 @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">No. HP / WhatsApp</label>
                        <input type="tel" name="hp" class="form-control rounded-3 @error('hp') is-invalid @enderror" placeholder="08xxxxxxxxxx" value="{{ old('hp') }}" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Alamat (Opsional)</label>
                        <textarea name="alamat" class="form-control rounded-3" placeholder="Alamat lengkap panitia"></textarea>
                    </div>
                    <div class="d-grid">
                        <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold">Buat Akun Panitia</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
