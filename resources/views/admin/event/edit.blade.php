@extends('layouts.admin')

@section('title', 'Edit Event - MySEMS')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold">Edit Event</h2>
    <p class="text-muted">Perbarui informasi event Anda di bawah ini.</p>
</div>

<div class="row">
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-body p-4">
                @if ($errors->any())
                    <div class="alert alert-danger rounded-4 mb-4 border-0 shadow-sm">
                        <div class="fw-bold mb-1"><i class="bi bi-exclamation-triangle-fill me-2"></i>Terdapat kesalahan pengisian form:</div>
                        <ul class="mb-0 small">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                <form action="{{ route('admin.event.update', $event->uuid) }}" method="POST" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Nama Event</label>
                            <input type="text" name="nama_event" class="form-control rounded-3 @error('nama_event') is-invalid @enderror" value="{{ old('nama_event', $event->nama_event) }}" required>
                            @error('nama_event')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">ID Sekolah</label>
                            <input type="text" name="school_id" class="form-control rounded-3 @error('school_id') is-invalid @enderror" value="{{ old('school_id', $event->school_id) }}" placeholder="Kosongkan jika Event Umum / Publik">
                            @error('school_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Foto Event</label>
                            @if($event->foto_event)
                                <div class="mb-3">
                                    <img src="{{ asset('storage/' . $event->foto_event) }}" alt="Current Photo" class="rounded-3 shadow-sm" style="max-height: 150px; width: auto;">
                                    <p class="small text-muted mt-2">Foto saat ini</p>
                                </div>
                            @endif
                            <input type="file" name="foto_event" class="form-control rounded-3 @error('foto_event') is-invalid @enderror" accept="image/*">
                            @error('foto_event')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <div class="form-text">Biarkan kosong jika tidak ingin mengganti foto.</div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Tanggal Pelaksanaan</label>
                            <input type="date" name="tanggal_event" class="form-control rounded-3 @error('tanggal_event') is-invalid @enderror" value="{{ old('tanggal_event', $event->tanggal_event) }}" required>
                            @error('tanggal_event')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Jam Pelaksanaan</label>
                            <input type="time" name="jam_event" class="form-control rounded-3 @error('jam_event') is-invalid @enderror" value="{{ old('jam_event', $event->jam_event ? date('H:i', strtotime($event->jam_event)) : '') }}" required>
                            @error('jam_event')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Nama Panitia / Penyelenggara</label>
                            <input type="text" name="nama_panitia" class="form-control rounded-3 @error('nama_panitia') is-invalid @enderror" value="{{ old('nama_panitia', $event->nama_panitia) }}" required>
                            @error('nama_panitia')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Harga Pendaftaran</label>
                            <div class="input-group">
                                <span class="input-group-text bg-light rounded-start-3">Rp</span>
                                <input type="number" step="any" name="harga_pendaftaran" class="form-control rounded-end-3 @error('harga_pendaftaran') is-invalid @enderror" min="0" value="{{ old('harga_pendaftaran', floatval($event->harga_pendaftaran)) }}" required>
                                @error('harga_pendaftaran')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">Slot Tim / Kuota</label>
                            <div class="input-group">
                                <input type="number" name="slot_tim" class="form-control rounded-start-3 @error('slot_tim') is-invalid @enderror" min="1" value="{{ old('slot_tim', $event->slot_tim) }}" required>
                                <span class="input-group-text bg-light rounded-end-3">Tim</span>
                                @error('slot_tim')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-semibold">Lokasi</label>
                            <input type="text" name="lokasi" class="form-control rounded-3 @error('lokasi') is-invalid @enderror" value="{{ old('lokasi', $event->lokasi) }}" required>
                            @error('lokasi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                        <div class="col-12">
                            <label class="form-label fw-semibold">Deskripsi Event</label>
                            <textarea name="deskripsi" class="form-control rounded-3 @error('deskripsi') is-invalid @enderror" rows="4">{{ old('deskripsi', $event->deskripsi) }}</textarea>
                            @error('deskripsi')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>
                    <div class="mt-4">
                        <button type="submit" class="btn btn-primary rounded-pill px-5 py-2 fw-bold shadow-sm">
                            <i class="bi bi-save me-2"></i>Simpan Perubahan
                        </button>
                        <a href="{{ route('admin.event.index') }}" class="btn btn-light rounded-pill px-4 py-2 ms-2">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
