@extends($layout)

@section('title', 'Detail Peserta - MySEMS')

@section('content')
<div class="mb-4">
    <a href="{{ route($rolePrefix . '.peserta.index') }}" class="btn btn-light rounded-pill px-3 mb-3">
        <i class="bi bi-arrow-left me-2"></i>Kembali
    </a>
    <h2 class="fw-bold">Detail Peserta</h2>
</div>

<div class="row">
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 text-center p-4 mb-4">
            <img src="https://ui-avatars.com/api/?name={{ urlencode($peserta->nama) }}&size=128&background=0D6EFD&color=fff" class="rounded-circle mx-auto mb-3" width="120">
            <h4 class="fw-bold mb-1">{{ $peserta->nama }}</h4>
            <span class="badge bg-primary bg-opacity-10 text-primary rounded-pill px-3">Peserta Aktif</span>
            <hr class="my-4 opacity-25">
            <div class="text-start">
                <p class="mb-2 text-muted small uppercase fw-bold">Kontak</p>
                <p class="mb-3"><i class="bi bi-envelope me-2"></i>{{ $peserta->email }}</p>
                <p class="mb-0"><i class="bi bi-whatsapp me-2"></i>{{ $peserta->hp ?? 'Tidak ada No. HP' }}</p>
            </div>
        </div>
    </div>
    
    <div class="col-lg-8">
        <div class="card border-0 shadow-sm rounded-4 p-4">
            <h5 class="fw-bold mb-4">Informasi Lengkap</h5>
            <div class="row g-4">
                <div class="col-md-6">
                    <label class="text-muted small fw-bold d-block mb-1">Username</label>
                    <p class="fw-semibold">{{ $peserta->username ?? '-' }}</p>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small fw-bold d-block mb-1">Tanggal Terdaftar</label>
                    <p class="fw-semibold">{{ $peserta->created_at->format('d F Y, H:i') }}</p>
                </div>
                <div class="col-md-6">
                    <label class="text-muted small fw-bold d-block mb-1">Status Keikutsertaan</label>
                    <p class="fw-semibold">
                        @if($peserta->event_participation_status === 'Sudah Mengikuti Event')
                            <span class="badge bg-success rounded-pill px-3">Sudah Mengikuti Event</span>
                        @else
                            <span class="badge bg-warning text-dark rounded-pill px-3">Belum Mengikuti Event</span>
                        @endif
                    </p>
                </div>
                <div class="col-md-6">
                    &nbsp;
                </div>
                <div class="col-12">
                    <label class="text-muted small fw-bold d-block mb-1">Alamat</label>
                    <p class="fw-semibold">{{ $peserta->alamat ?? 'Alamat belum diisi.' }}</p>
                </div>
            </div>
            
            @if(auth()->user()->role == '1')
            <div class="mt-5 pt-3 border-top d-flex justify-content-end">
                <form action="{{ route('admin.peserta.destroy', $peserta->id) }}" method="POST" id="deletePesertaForm">
                    @csrf
                    @method('DELETE')
                    <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-4" onclick="confirmDeletePeserta()">Hapus Akun Peserta</button>
                </form>
            </div>

            @push('scripts')
            <script>
                function confirmDeletePeserta() {
                    Swal.fire({
                        title: 'Hapus Peserta?',
                        text: "Seluruh data akun peserta ini akan dihapus secara permanen!",
                        icon: 'warning',
                        showCancelButton: true,
                        confirmButtonColor: '#d33',
                        cancelButtonColor: '#3085d6',
                        confirmButtonText: 'Ya, Hapus Akun!',
                        cancelButtonText: 'Batal'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            document.getElementById('deletePesertaForm').submit();
                        }
                    })
                }
            </script>
            @endpush
            @endif
        </div>
    </div>
</div>
@endsection
