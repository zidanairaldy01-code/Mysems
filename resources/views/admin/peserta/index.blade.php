@extends('layouts.admin')

@section('title', 'Data Peserta - MySEMS')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">Data Peserta</h2>
        <p class="text-muted">Daftar seluruh peserta yang terdaftar di MySMES.</p>
    </div>
</div>

<div class="card border-0 shadow-sm rounded-4">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover align-middle mb-0">
                <thead class="bg-light">
                    <tr>
                        <th class="ps-4 py-3 border-0">Nama Peserta</th>
                        <th class="py-3 border-0">Email</th>
                        <th class="py-3 border-0">No. HP</th>
                        <th class="py-3 border-0">Tgl Bergabung</th>
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
                        <td>{{ $peserta->email }}</td>
                        <td>{{ $peserta->hp ?? '-' }}</td>
                        <td>{{ $peserta->created_at->format('d M Y') }}</td>
                        <td class="pe-4 text-end">
                            <div class="dropdown">
                                <button class="btn btn-light btn-sm rounded-circle" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                                    <i class="bi bi-three-dots-vertical"></i>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end shadow border-0">
                                    <li>
                                        <a class="dropdown-item py-2" href="{{ route('admin.peserta.show', $peserta->id) }}">
                                            <i class="bi bi-eye me-2 text-primary"></i> Detail
                                        </a>
                                    </li>
                                    <li><hr class="dropdown-divider"></li>
                                    <li>
                                        <form action="{{ route('admin.peserta.destroy', $peserta->id) }}" method="POST" class="delete-form">
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
                    <tr>
                        <td colspan="5" class="text-center py-5 text-muted">
                            <i class="bi bi-people fs-1 d-block mb-2"></i>
                            Belum ada data peserta.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
