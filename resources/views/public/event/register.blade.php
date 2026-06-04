@extends('layouts.app')

@section('title', 'Daftar Event - ' . $event->nama_event)

@section('content')
<div class="row justify-content-center py-4">
    <div class="col-lg-6">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
            <div class="bg-primary p-4 text-white text-center">
                <h4 class="fw-bold mb-0">{{ $event->type_normalized === 'umum' ? 'Form Pendaftaran Peserta' : 'Form Pendaftaran Tim' }}</h4>
                <p class="mb-0 opacity-75">{{ $event->nama_event }}</p>
            </div>
            <div class="card-body p-4">
                <form action="{{ route('public.registration.store') }}" method="POST">
                    @csrf
                    <input type="hidden" name="event_id" value="{{ $event->id }}">
                    
                    @if($event->type_normalized !== 'umum')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Tim</label>
                        <input type="text" name="nama_tim" class="form-control rounded-3 py-2" placeholder="Masukkan nama tim Anda" required>
                    </div>
                    @endif
                    
                    <div class="mb-3">
                        <label class="form-label fw-semibold">{{ $event->type_normalized === 'umum' ? 'Nama Lengkap' : 'Nama Kapten' }}</label>
                        <input type="text" name="nama_kapten" class="form-control rounded-3 py-2" placeholder="Masukkan nama lengkap" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label fw-semibold">Email</label>
                        <input type="email" name="email" class="form-control rounded-3 py-2" placeholder="contoh@email.com" value="{{ Auth::user() ? Auth::user()->email : '' }}" required>
                        <div class="form-text">Tiket dan bukti pendaftaran akan dikirim ke email ini.</div>
                    </div>

                    @if($event->type_normalized !== 'umum')
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Nama Anggota Tim</label>
                        <textarea name="anggota_tim" class="form-control rounded-3" rows="3" placeholder="Contoh: Budi, Andi, Siska, Dst..." required></textarea>
                        <div class="form-text">Pisahkan dengan tanda koma.</div>
                    </div>
                    @endif
                    
                    <div class="mb-4">
                        <label class="form-label fw-semibold">Nomor WhatsApp</label>
                        <div class="input-group">
                            <span class="input-group-text bg-light rounded-start-3">+62</span>
                            <input type="number" name="nomor_wa" class="form-control rounded-end-3 py-2" placeholder="8123456789" required>
                        </div>
                    </div>
                    
                    @if($event->school_id)
                    <div class="mb-4">
                        <label class="form-label fw-semibold text-danger">ID Sekolah (Verifikasi)</label>
                        <input type="text" name="school_id" class="form-control rounded-3 py-2 border-danger @error('school_id') is-invalid @enderror" placeholder="Masukkan ID Sekolah Anda untuk melanjutkan" required>
                        <div class="form-text text-danger">Event ini khusus untuk sekolah tertentu. Harap masukkan ID Sekolah yang tepat.</div>
                        @error('school_id')
                            <div class="invalid-feedback d-block">{{ $message }}</div>
                        @enderror
                    </div>
                    @endif
                    
                    @if($event->type_normalized === 'umum')
                    <div class="alert alert-warning border-0 rounded-4 small mb-4">
                        <i class="bi bi-qr-code-scan me-2"></i>
                        Setelah pembayaran berhasil, Anda akan mendapatkan **QR Code** yang wajib ditunjukkan saat kehadiran di lokasi event.
                    </div>
                    @else
                    <div class="alert alert-info border-0 rounded-4 small mb-4">
                        <i class="bi bi-info-circle me-2"></i>
                        Setelah mendaftar, tim Anda akan **otomatis ditempatkan** secara acak ke dalam bagan pertandingan.
                    </div>
                    @endif
                    
                    <div class="d-grid gap-2">
                        <button type="submit" class="btn btn-primary rounded-pill py-2 fw-bold" onclick="this.disabled=true; this.innerHTML='<span class=\'spinner-border spinner-border-sm me-2\'></span>Memproses...'; this.form.submit();">Daftar Sekarang</button>
                        <a href="{{ route('public.event.index') }}" class="btn btn-light rounded-pill py-2">Batal</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
