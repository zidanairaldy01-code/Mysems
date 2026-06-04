@extends('layouts.admin')

@section('title', 'Edit Akun Panitia - MySEMS')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold">Edit Akun Panitia</h2>
    <p class="text-muted">Perbarui informasi akun panitia {{ $panitia->nama }}.</p>
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

                <form action="{{ route('admin.panitia.update', $panitia->id) }}" method="POST">
                    @csrf
                    @method('PUT')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Lengkap</label>
                        <input type="text" name="nama" class="form-control rounded-3 @error('nama') is-invalid @enderror" placeholder="Contoh: Budi Utomo" value="{{ old('nama', $panitia->nama) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control rounded-3 @error('email') is-invalid @enderror" placeholder="panitia@example.com" value="{{ old('email', $panitia->email) }}" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Password (Kosongkan jika tidak ingin diubah)</label>
                        <input type="password" name="password" class="form-control rounded-3 @error('password') is-invalid @enderror" placeholder="Minimal 8 karakter">
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">No. HP / WhatsApp</label>
                        <input type="tel" name="hp" class="form-control rounded-3 @error('hp') is-invalid @enderror" placeholder="08xxxxxxxxxx" value="{{ old('hp', $panitia->hp) }}" required>
                    </div>
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Alamat (Opsional)</label>
                        <textarea name="alamat" class="form-control rounded-3" placeholder="Alamat lengkap panitia">{{ old('alamat', $panitia->alamat) }}</textarea>
                    </div>
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold">Simpan Perubahan</button>
                        <a href="{{ route('admin.panitia.index') }}" class="btn btn-light rounded-pill py-2 fw-bold">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
