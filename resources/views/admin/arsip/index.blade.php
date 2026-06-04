@extends('layouts.admin')

@section('title', $title . ' - MySEMS')

@section('content')
<div class="mb-4">
    <h2 class="fw-bold">{{ $title }}</h2>
    <p class="text-muted">Riwayat lengkap {{ $type === 'umum' ? 'event umum' : ($type === 'tournament' ? 'event turnamen' : 'seluruh event') }} yang telah dilaksanakan atau didaftarkan.</p>
</div>

<div class="row g-4">
    @forelse($events as $event)
    <div class="col-md-4">
        <div class="card border-0 shadow-sm rounded-4 overflow-hidden h-100 transition-hover">
            <div class="position-relative">
                @if($event->foto_event)
                    <img src="{{ asset($event->foto_event) }}" class="card-img-top" style="height: 180px; object-fit: cover;">
                @else
                    <div class="bg-primary bg-opacity-10 d-flex align-items-center justify-content-center" style="height: 180px;">
                        <i class="bi bi-archive fs-1 text-primary"></i>
                    </div>
                @endif
                <div class="position-absolute top-0 end-0 m-3">
                    <span class="badge {{ $event->status == 1 ? 'bg-success' : 'bg-secondary' }} rounded-pill px-3 shadow-sm">
                        {{ $event->status == 1 ? 'Aktif' : 'Non-Aktif' }}
                    </span>
                </div>
            </div>
            <div class="card-body p-4">
                <h5 class="fw-bold mb-1">{{ $event->nama_event }}</h5>
                <p class="text-muted small mb-3">
                    <i class="bi bi-calendar-event me-1"></i> {{ \Carbon\Carbon::parse($event->tanggal_event)->format('d M Y') }}
                </p>
                
                <div class="d-flex align-items-center justify-content-between pt-3 border-top">
                    <div class="small">
                        <span class="text-muted">Penyelenggara:</span><br>
                        <span class="fw-semibold">{{ $event->user->nama }}</span>
                    </div>
                    <div class="d-flex align-items-center">
                        <!-- PERBAIKAN DI SINI: Menghapus @method('DELETE') agar menjadi murni POST request -->
                        <form action="{{ route('admin.arsip.destroy', $event->uuid) }}" method="POST" id="delete-form-{{ $event->uuid }}" class="d-inline">
                            @csrf
                            <button type="button" class="btn btn-outline-danger rounded-circle p-2 lh-1 me-2" title="Hapus Arsip" onclick="confirmDelete('{{ $event->uuid }}')">
                                <i class="bi bi-trash"></i>
                            </button>
                        </form>
                        <a href="{{ Auth::user()->role == '1' ? route('admin.arsip.show', $event->uuid) : route('panitia.arsip.show', $event->uuid) }}" class="btn btn-primary rounded-pill px-3">
                            Detail
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @empty
    <div class="col-12 text-center py-5">
        <i class="bi bi-archive text-muted" style="font-size: 4rem;"></i>
        <h5 class="mt-3 text-muted">Belum ada event yang diarsipkan.</h5>
    </div>
    @endforelse
</div>

<style>
    .transition-hover {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .transition-hover:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 25px rgba(0,0,0,0.1) !important;
    }
</style>
@push('scripts')
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: 'Hapus Arsip Permanen?',
            text: "Data arsip event ini akan dihapus selamanya!",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Ya, Hapus!',
            cancelButtonText: 'Batal'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete-form-' + id).submit();
            }
        })
    }
</script>
@endpush
@endsection