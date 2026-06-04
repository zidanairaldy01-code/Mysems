@extends('layouts.admin')

@section('title', 'Detail Arsip - ' . $event->nama_event)

@section('content')
<div class="mb-4 d-flex justify-content-between align-items-center">
    <div>
        <a href="{{ Auth::user()->role == '1' ? route('admin.arsip.index') : route('panitia.arsip.index') }}" class="btn btn-light rounded-pill px-3 mb-3">
            <i class="bi bi-arrow-left me-1"></i> Kembali ke Daftar Arsip
        </a>
        <h2 class="fw-bold">Arsip: {{ $event->nama_event }}</h2>
        <p class="text-muted">Data lengkap turnamen yang sudah selesai diarsipkan.</p>
    </div>
    <div class="d-flex align-items-center">
        <form action="{{ route('admin.arsip.destroy', $event->id) }}" method="POST" id="deleteArsipForm" class="me-2">
            @csrf
            @method('DELETE')
            <button type="button" class="btn btn-outline-danger rounded-pill px-4 shadow-sm fw-bold" onclick="confirmDeleteArsip()">
                <i class="bi bi-trash me-2"></i> Hapus Arsip
            </button>
        </form>

        @push('scripts')
        <script>
            function confirmDeleteArsip() {
                Swal.fire({
                    title: 'Hapus Arsip Permanen?',
                    text: "Data arsip event ini akan dihapus selamanya!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#3085d6',
                    confirmButtonText: 'Ya, Hapus Permanen!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        document.getElementById('deleteArsipForm').submit();
                    }
                })
            }
        </script>
        @endpush
        <button onclick="window.print()" class="btn btn-primary rounded-pill px-4 shadow-sm fw-bold">
            <i class="bi bi-printer me-2"></i> Cetak Dokumen
        </button>
    </div>
</div>

<div class="row g-4">
    <!-- Kolom Kiri: Info Event & Peserta -->
    <div class="col-lg-8">
        <!-- Detail Event -->
        <div class="card border-0 shadow-sm rounded-4 mb-4">
            <div class="card-body p-4">
                <h5 class="fw-bold mb-4 border-bottom pb-2">Informasi Turnamen</h5>
                <div class="row g-3">
                    <div class="col-md-6">
                        <small class="text-muted d-block">Penyelenggara</small>
                        <span class="fw-semibold">{{ $event->nama_panitia }} ({{ $event->user->nama }})</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Tanggal Pelaksanaan</small>
                        <span class="fw-semibold">{{ \Carbon\Carbon::parse($event->tanggal_event)->format('d F Y') }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Lokasi Turnamen</small>
                        <span class="fw-semibold">{{ $event->lokasi }}</span>
                    </div>
                    <div class="col-md-6">
                        <small class="text-muted d-block">Pemenang Turnamen</small>
                        <span class="badge bg-warning text-dark fw-bold fs-6 rounded-pill px-3">
                            <i class="bi bi-trophy-fill me-1"></i> {{ $winner }}
                        </span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Daftar Peserta -->
        <div class="card border-0 shadow-sm rounded-4">
            <div class="card-header bg-white p-4 border-0">
                <h5 class="fw-bold mb-0">Daftar Tim & Peserta</h5>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="bg-light">
                            <tr>
                                <th class="ps-4">Tim</th>
                                <th>Kapten/Ketua</th>
                                <th>Anggota</th>
                                <th class="pe-4 text-end">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($registrations as $reg)
                            <tr>
                                <td class="ps-4 fw-bold text-primary" style="min-width: 150px;">{{ $reg->nama_tim }}</td>
                                <td style="min-width: 150px;">{{ $reg->nama_kapten }}<br><small class="text-muted">{{ $reg->nomor_wa }}</small></td>
                                <td class="text-wrap" style="max-width: 250px;"><small class="text-muted">{{ $reg->anggota_tim }}</small></td>
                                <td class="pe-4 text-end">
                                    <span class="badge bg-success bg-opacity-10 text-success rounded-pill px-3">Settlement</span>
                                </td>
                            </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Kolom Kanan: Bagan Pertandingan (Miniatur) -->
    <div class="col-lg-4">
        <div class="card border-0 shadow-sm rounded-4 h-100">
            <div class="card-header bg-white p-4 border-0">
                <h5 class="fw-bold mb-0">Riwayat Match</h5>
            </div>
            <div class="card-body">
                <a href="{{ Auth::user()->role == '1' ? route('admin.klasemen.index', ['event_id' => $event->id]) : route('panitia.klasemen.index', ['event_id' => $event->id]) }}" class="btn btn-outline-primary w-100 rounded-pill mb-4 fw-bold">
                    <i class="bi bi-diagram-3 me-2"></i> Lihat Bagan Klasemen Lengkap
                </a>
                <div class="match-history">
                    @foreach($matches as $round => $roundMatches)
                    <div class="mb-4">
                        <h6 class="fw-bold text-muted small text-uppercase mb-3">Round {{ $round }}</h6>
                        @foreach($roundMatches as $match)
                        <div class="p-3 rounded-3 bg-light mb-2 border-start border-4 {{ $match->team1_score > $match->team2_score ? 'border-primary' : 'border-secondary' }}">
                            <div class="d-flex justify-content-between align-items-center mb-1">
                                <span class="small {{ $match->team1_score > $match->team2_score ? 'fw-bold' : '' }}">{{ $match->team1_name }}</span>
                                <span class="badge bg-white text-dark border">{{ $match->team1_score }}</span>
                            </div>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="small {{ $match->team2_score > $match->team1_score ? 'fw-bold' : '' }}">{{ $match->team2_name }}</span>
                                <span class="badge bg-white text-dark border">{{ $match->team2_score }}</span>
                            </div>
                        </div>
                        @endforeach
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<style>
    @media print {
        @page {
            size: A4;
            margin: 1.5cm;
        }
        .btn, .sidebar, .topbar, .no-print { display: none !important; }
        .main-wrapper { margin-left: 0 !important; padding: 0 !important; width: 100% !important; }
        .card { box-shadow: none !important; border: 1px solid #dee2e6 !important; }
        .col-lg-8, .col-lg-4 { width: 100% !important; flex: 0 0 100% !important; max-width: 100% !important; }
        .table { width: 100% !important; border: 1px solid #dee2e6 !important; }
        .table-responsive { overflow: visible !important; }
    }
</style>
@endsection
