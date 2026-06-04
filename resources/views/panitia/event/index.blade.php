@extends('layouts.panitia')

@section('title', 'Data Event ' . ($type == 'umum' ? 'Umum' : ($type == 'tournament' ? 'Turnamen' : '')) .' - MySEMS')

@section('content')

<style>
@media (min-width: 768px) {
    .table-responsive {
        overflow: visible !important;
    }
}
</style>

<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">Event {{ $type == 'umum' ? 'Umum' : ($type == 'tournament' ? 'Turnamen' : '') }} Saya</h2>
        <p class="text-muted">Kelola event bertipe {{ $type ?? 'semua' }} yang Anda ajukan.</p>
    </div>
</div>

<div class="d-flex flex-column gap-3 mb-4">
    <div class="d-flex justify-content-start">
        <a href="{{ route('panitia.event.create', ['type' => $type]) }}" class="btn btn-primary btn-sm rounded-pill px-3 py-2 fw-bold d-inline-flex align-items-center-auto">
            <i class="bi bi-plus-lg me-2"></i>Ajukan Event Baru
        </a>
    </div>

    <div class="d-grid gap-2 w-100" style="grid-template-columns: repeat(auto-fit,minmax(170px,1fr));">
        <a href="{{ route('panitia.event.index', ['type' => $type]) }}" 
           class="btn btn-sm rounded-pill px-3 py-2 {{ empty($status) ? 'btn-dark fw-bold' : 'btn-outline-secondary' }} w-100">
           Semua Status
        </a>
        <a href="{{ route('panitia.event.index', ['type' => $type, 'status' => 'pending']) }}" 
           class="btn btn-sm rounded-pill px-3 py-2 {{ $status == 'pending' ? 'btn-warning fw-bold text-dark' : 'btn-outline-warning' }} w-100">
           Event di Pending
        </a>
        <a href="{{ route('panitia.event.index', ['type' => $type, 'status' => 'success']) }}" 
           class="btn btn-sm rounded-pill px-3 py-2 {{ $status == 'success' ? 'btn-success fw-bold' : 'btn-outline-success' }} w-100">
           Event Sukses
        </a>
        <a href="{{ route('panitia.event.index', ['type' => $type, 'status' => 'fail']) }}" 
           class="btn btn-sm rounded-pill px-3 py-2 {{ $status == 'fail' ? 'btn-danger fw-bold' : 'btn-outline-danger' }} w-100">
           Event Ditolak
        </a>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-sm align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 border-0">Nama Event</th>
                        <th class="py-3 border-0 d-none d-md-table-cell">Penyelenggara</th>
                        <th class="py-3 border-0">Tanggal / Harga</th>
                        <th class="py-3 border-0 d-none d-lg-table-cell">Lokasi / Slot</th>
                        <th class="py-3 border-0">Status</th>
                        <th class="pe-4 py-3 border-0 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($events as $event)
                    <tr>
                        <td class="ps-4" data-label="Nama Event">
                            <div class="fw-bold text-dark mb-1">{{ $event->nama_event }}</div>
                            <div class="small text-muted">
                                <i class="bi bi-clock me-1"></i><span class="fw-bold">{{ \Carbon\Carbon::parse($event->jam_event)->format('H:i') }} WIB</span>
                            </div>
                        </td>
                        <td class="d-none d-md-table-cell" data-label="Penyelenggara">
                            <div class="fw-semibold">{{ $event->user->nama }}</div>
                            <div class="x-small text-muted" style="font-size: 0.7rem;">{{ $event->nama_panitia }}</div>
                        </td>
                        <td data-label="Tanggal / Harga">
                            <div class="fw-bold text-primary">{{ \Carbon\Carbon::parse($event->tanggal_event)->format('d M Y') }}</div>
                            <div class="small {{ $event->harga_pendaftaran == 0 ? 'text-success' : 'text-primary' }}">
                                {{ $event->harga_pendaftaran == 0 ? 'Gratis' : 'Rp '.number_format($event->harga_pendaftaran,0,',','.') }}
                            </div>
                        </td>
                        <td class="d-none d-lg-table-cell" data-label="Lokasi / Slot">
                            <div class="fw-semibold">{{ $event->lokasi }}</div>
                            <div class="badge bg-secondary text-white border rounded-pill px-2 mt-1" style="font-size: 0.7rem;">
                                {{ $event->slot_tim }} Tim
                            </div>
                        </td>
                        <td data-label="Status">
                            @if($event->status == 0)
                                <span class="badge bg-warning text-white rounded-pill px-3">Menunggu Persetujuan</span>
                            @elseif($event->status == 1)
                                <span class="badge bg-success text-white rounded-pill px-3">Diterima oleh Admin</span>
                            @elseif($event->status == 2)
                                <span class="badge bg-info text-white rounded-pill px-3">Selesai / Arsip</span>
                            @else
                                <span class="badge bg-danger text-white rounded-pill px-3" data-bs-toggle="tooltip" data-bs-placement="top" title="Alasan: {{ $event->alasan_ditolak ?? 'Tidak ada alasan spesifik.' }}">Ditolak</span>
                                @if($event->alasan_ditolak)
                                    <div class="x-small text-danger mt-1 text-truncate" style="font-size: 0.7rem; max-width: 130px;" title="{{ $event->alasan_ditolak }}">
                                        Alasan: {{ $event->alasan_ditolak }}
                                    </div>
                                @endif
                            @endif
                        </td>
                        <td class="pe-4 text-end" data-label="Aksi">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false" data-bs-boundary="window">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('panitia.event.show', $event->uuid) }}">
                                            <i class="bi bi-eye me-2 text-primary"></i> Detail
                                        </a>
                                    </li>
                                    <li>
                                        <button type="button" class="dropdown-item py-2" onclick="navigator.clipboard.writeText('{{ route('public.event.show', $event->uuid) }}'); Swal.fire({toast: true, position: 'top-end', icon: 'success', title: 'Link disalin!', showConfirmButton: false, timer: 1500});">
                                            <i class="bi bi-link-45deg me-2 text-secondary"></i> Salin Link
                                        </button>
                                    </li>
                                    @if($event->user_id == Auth::id() && $event->status == 0)
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('panitia.event.edit', $event->uuid) }}">
                                            <i class="bi bi-pencil me-2 text-warning"></i> Edit
                                        </a>
                                    </li>
                                    @endif
                                </ul>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr class="d-none d-md-table-row">
                        <td colspan="6" class="text-center py-5 text-muted">
                            @if($status)
                                Tidak ada data event dengan status <strong>{{ $status }}</strong>.
                            @else
                                Anda belum mengajukan event apa pun.
                            @endif
                        </td>
                    </tr>
                    <tr class="d-md-none">
                        <td colspan="6" class="text-center py-5 text-muted">
                            @if($status)
                                Tidak ada data event dengan status <strong>{{ $status }}</strong>.
                            @else
                                Anda belum mengajukan event apa pun.
                            @endif
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection