@extends('layouts.panitia')

@section('title', 'Tambah Event Umum - MySMES')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold">Tambah Event Umum Baru</h2>
    <p class="text-muted">Isi formulir di bawah untuk mengajukan event umum baru.</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                <form action="{{ route('panitia.event.store') }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    <input type="hidden" name="type" value="umum">
                    
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Judul Event</label>
                            <input type="text" name="nama_event" class="form-control rounded-3 @error('nama_event') is-invalid @enderror" value="{{ old('nama_event') }}" placeholder="Contoh: Workshop Seni MySMES" required>
                            @error('nama_event')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">ID Sekolah</label>
                            <input type="text" name="school_id" class="form-control rounded-3 @error('school_id') is-invalid @enderror" value="{{ old('school_id') }}" placeholder="Kosongkan jika Event Umum / Publik">
                            @error('school_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Poster Event</label>
                            <input type="file" name="foto_event" class="form-control rounded-3 @error('foto_event') is-invalid @enderror" accept="image/*">
                            @error('foto_event')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Gunakan gambar menarik (JPG/PNG).</div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Pelaksanaan</label>
                            <input type="date" name="tanggal_event" class="form-control rounded-3 @error('tanggal_event') is-invalid @enderror" value="{{ old('tanggal_event') }}" required>
                            @error('tanggal_event')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jam Pelaksanaan</label>
                            <input type="time" name="jam_event" class="form-control rounded-3 @error('jam_event') is-invalid @enderror" value="{{ old('jam_event') }}" required>
                            @error('jam_event')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Penanggung Jawab</label>
                            <input type="text" name="nama_panitia" class="form-control rounded-3 @error('nama_panitia') is-invalid @enderror" value="{{ old('nama_panitia') }}" placeholder="Nama orang atau organisasi" required>
                            @error('nama_panitia')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Biaya Pendaftaran</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light rounded-start-3">Rp</span>
                                <input type="number" name="harga_pendaftaran" class="form-control rounded-end-3 @error('harga_pendaftaran') is-invalid @enderror" placeholder="Isi 0 jika Gratis" min="0" value="{{ old('harga_pendaftaran', 0) }}">
                                @error('harga_pendaftaran')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Kuota Peserta</label>
                            <div class="input-group">
                                <input type="number" name="slot_tim" class="form-control rounded-start-3 @error('slot_tim') is-invalid @enderror" value="{{ old('slot_tim') }}" placeholder="Contoh: 50" min="1" required>
                                <span class="input-group-text bg-light rounded-end-3">Orang</span>
                                @error('slot_tim')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Lokasi (Opsional)</label>
                            <input type="text" name="lokasi" class="form-control rounded-3 @error('lokasi') is-invalid @enderror" value="{{ old('lokasi') }}" placeholder="Contoh: Perpustakaan Sekolah">
                            @error('lokasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi</label>
                            <textarea name="deskripsi" class="form-control rounded-3 @error('deskripsi') is-invalid @enderror" rows="4" placeholder="Jelaskan detail event umum Anda...">{{ old('deskripsi') }}</textarea>
                            @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12 mt-4">
                            <div class="alert alert-warning border-0 rounded-4 small">
                                <i class="bi bi-info-circle me-2"></i>
                                Event ini akan dikirim ke <strong>Admin</strong> untuk persetujuan sebelum dipublikasikan.
                            </div>
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold">Ajukan Event</button>
                        <a href="{{ route('panitia.event.index') }}" class="btn btn-light rounded-pill px-4 py-2 ms-2">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
