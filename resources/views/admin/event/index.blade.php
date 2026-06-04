@extends('layouts.admin')

@php
    $statusLabel = 'Data Event';
    if (isset($status)) {
        if ($status === 'pending') {
            $statusLabel = 'Pending Event';
        } elseif ($status === 'success') {
            $statusLabel = 'Success Event';
        } elseif ($status === 'fail') {
            $statusLabel = 'Fail Event';
        }
    }
@endphp

@section('title', $statusLabel . ' - MySEMS')

@section('content')

<style>
@media (min-width: 768px) {
    .table-responsive {
        overflow: visible !important;
    }
}
</style>

{{-- Header Halaman: Judul dan Tombol Tambah --}}
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">{{ $statusLabel }} {{ $type == 'umum' ? 'Umum' : ($type == 'tournament' ? 'Turnamen' : '') }}</h2>
        <p class="text-muted">Daftar {{ $statusLabel === 'Data Event' ? 'seluruh event' : strtolower($statusLabel) }} {{ $type ?? 'semua' }} yang terdaftar di MySMES.</p>
    </div>
    <a href="{{ route('admin.event.create', ['type' => $type]) }}" class="btn btn-primary rounded-pill px-4 fw-bold">
        <i class="bi bi-plus-lg me-2"></i>Tambah Event Baru
    </a>
</div>

{{-- Tabel Data Event --}}
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 border-0">Nama Event</th>
                        <th class="py-3 border-0">Penyelenggara</th>
                        <th class="py-3 border-0">Tanggal / Harga</th>
                        <th class="py-3 border-0">Slot</th>
                        <th class="py-3 border-0">Status</th>
                        <th class="pe-4 py-3 border-0 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    {{-- Looping semua data event --}}
                    @forelse($events as $event)
                    <tr>
                        <td class="ps-4">
                            <div class="fw-bold text-dark mb-1">{{ $event->nama_event }}</div>
                            <div class="small text-muted">
                                <i class="bi bi-geo-alt me-1"></i>{{ $event->lokasi }} | 
                                <i class="bi bi-clock me-1"></i><span class="fw-bold">{{ \Carbon\Carbon::parse($event->jam_event)->format('H:i') }} WIB</span>
                            </div>
                        </td>
                        <td>
                            <div class="fw-semibold">{{ $event->user->nama }}</div>
                            <div class="x-small text-muted" style="font-size: 0.7rem;">{{ $event->nama_panitia }}</div>
                        </td>
                        <td>
                            <div class="fw-bold text-primary">{{ \Carbon\Carbon::parse($event->tanggal_event)->format('d M Y') }}</div>
                            <div class="small {{ $event->harga_pendaftaran == 0 ? 'text-success' : 'text-primary' }}">
                                {{ $event->harga_pendaftaran == 0 ? 'Gratis' : 'Rp '.number_format($event->harga_pendaftaran,0,',','.') }}
                            </div>
                        </td>
                        <td>
                            <div class="badge bg-light text-dark border rounded-pill px-3">
                                {{ $event->slot_tim }} Tim
                            </div>
                        </td>
                        <td>
                            {{-- Menampilkan badge berdasarkan status (Pending/Diterima/Selesai/Ditolak) --}}
                            @if($event->status == 0)
                                <span class="badge bg-warning bg-opacity-10 text-warning rounded-pill px-3">Pending</span>
                            @elseif($event->status == 1)
                                <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Diterima</span>
                            @elseif($event->status == 2)
                                <span class="badge bg-info bg-opacity-10 text-info rounded-pill px-3">Selesai</span>
                            @else
                                <span class="badge bg-danger bg-opacity-10 text-danger rounded-pill px-3">Ditolak</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-boundary="window">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('admin.event.show', $event->uuid) }}">
                                            <i class="bi bi-eye me-2 text-primary"></i> Detail
                                        </a>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item py-2" onclick="navigator.clipboard.writeText('{{ route('public.event.show', $event->uuid) }}'); Swal.fire({toast: true, position: 'top-end', icon: 'success', title: 'Link disalin!', showConfirmButton: false, timer: 1500});">
                                            <i class="bi bi-link-45deg me-2 text-secondary"></i> Salin Link
                                        </button>
                                    </li>
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('admin.event.edit', $event->uuid) }}">
                                            <i class="bi bi-pencil me-2 text-warning"></i> Edit
                                        </a>
                                    </li>
                                    @if($event->status == 1)
                                    <li>
                                        <form action="{{ route('admin.event.finish', $event->uuid) }}" method="POST" class="finish-form">
                                            @csrf
                                            <button type="button" class="dropdown-item py-2 finish-btn">
                                                <i class="bi bi-check-all me-2 text-info"></i> Selesaikan
                                            </button>
                                        </form>
                                    </li>
                                    @endif
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('admin.event.destroy', $event->uuid) }}" method="POST" class="delete-form">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="dropdown-item py-2 text-danger delete-btn">
                                                <i class="bi bi-trash me-2"></i> Hapus
                                            </button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    {{-- Tampilan jika tabel kosong --}}
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">Belum ada data event.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Konfirmasi Selesaikan Event
        const finishBtns = document.querySelectorAll('.finish-btn');
        finishBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                const form = this.closest('.finish-form');
                Swal.fire({
                    title: 'Selesaikan Event?',
                    text: "Event akan dipindahkan ke arsip dan pendaftaran akan ditutup.",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#0dcaf0',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Selesaikan!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        form.submit();
                    }
                });
            });
        });
    });
</script>
@endpush
@endsection
