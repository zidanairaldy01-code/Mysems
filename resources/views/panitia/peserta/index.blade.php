@extends('layouts.panitia')

@section('title', 'Data Peserta - MySMES')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">Data Peserta</h2>
        <p class="text-muted">Daftar seluruh peserta yang terdaftar di sistem.</p>
    </div>
</div>

<!-- Tabel Data Peserta -->
<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 border-0">Nama Peserta</th>
                        <th class="py-3 border-0">Asal Sekolah/Instansi</th>
                        <th class="py-3 border-0">Nomor HP</th>
                        <th class="py-3 border-0">Email</th>
                        <th class="py-3 border-0">Status Keikutsertaan</th>
                        <th class="pe-4 py-3 border-0 text-end">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($pesertas as $peserta)
                    <tr>
                        <td class="ps-4">
                            <div class="d-flex align-items-center">
                                <img src="https://ui-avatars.com/api/?name={{ urlencode($peserta->nama) }}&background=E2E8F0&color=475569" class="rounded-circle me-3" width="35">
                                <span class="fw-semibold">{{ $peserta->nama }}</span>
                            </div>
                        </td>
                        <td>{{ $peserta->asal_sekolah ?? '-' }}</td>
                        <td>{{ $peserta->nomor_hp ?? '-' }}</td>
                        <td>{{ $peserta->email }}</td>
                        <td>
                            @if($peserta->event_participation_status === 'Sudah Mengikuti Event')
                                <span class="badge bg-success rounded-pill px-3">Sudah Mengikuti Event</span>
                            @else
                                <span class="badge bg-warning text-dark rounded-pill px-3">Belum Mengikuti Event</span>
                            @endif
                        </td>
                        <td class="pe-4 text-end">
                            <a href="{{ route('panitia.peserta.show', $peserta->id) }}" class="btn btn-sm btn-light rounded-pill px-3">Detail</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="text-center py-5 text-muted">Belum ada data peserta yang terdaftar.</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
