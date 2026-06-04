@extends('layouts.panitia')

@section('title', 'Laporan Event Detail - MySMES')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="fw-bold">Laporan Event Saya</h2>
        <p class="text-muted">Pilih salah satu event Anda untuk melihat detail pendaftar.</p>
    </div>
</div>

<!-- Filter Event -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('panitia.laporan.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label fw-bold">Pilih Event Anda</label>
                <select name="event_id" class="form-select rounded-pill" required onchange="this.form.submit()">
                    <option value="">-- Pilih Event --</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}" {{ $selectedEventId == $event->id ? 'selected' : '' }}>
                            {{ $event->nama_event }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-6 text-md-end">
                @if($selectedEventId)
                    <a href="{{ request()->fullUrlWithQuery(['export' => 1]) }}" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm">
                        <i class="bi bi-file-earmark-excel me-2"></i>Ekspor Excel
                    </a>
                    <button onclick="window.print()" class="btn btn-outline-primary rounded-pill px-4 fw-bold ms-2 shadow-sm">
                        <i class="bi bi-printer me-2"></i>Cetak Laporan
                    </button>
                @endif
            </div>
        </form>
    </div>
</div>

@if($selectedEvent)
    <!-- Header Cetak -->
    <div class="d-none d-print-block text-center mb-5">
        <h3 class="fw-bold text-uppercase">Laporan Peserta Event</h3>
        <h4 class="text-primary">{{ $selectedEvent->nama_event }}</h4>
        <p class="mb-0 text-muted small fw-bold">ID Event: #E-{{ $selectedEvent->id }} | Dicetak: {{ date('d M Y, H:i') }}</p>
        <hr>
    </div>

    <!-- Ringkasan Statistik -->
    <div class="row g-3 mb-4">
        <div class="col-md-6">
            <div class="p-3 rounded-4 border bg-white shadow-sm d-flex align-items-center">
                <div class="bg-primary bg-opacity-10 text-primary rounded-3 p-3 me-3">
                    <i class="bi bi-people fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0 small">Total Tim Lunas</h6>
                    <h3 class="fw-bold mb-0">{{ $registrations->count() }} Tim</h3>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="p-3 rounded-4 border bg-white shadow-sm d-flex align-items-center">
                <div class="bg-success bg-opacity-10 text-success rounded-3 p-3 me-3">
                    <i class="bi bi-cash-stack fs-3"></i>
                </div>
                <div>
                    <h6 class="text-muted mb-0 small">Pendapatan Terkumpul</h6>
                    <h3 class="fw-bold mb-0 text-success">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h3>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabel Detail Tim -->
    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-primary text-white">
                        <tr>
                            <th class="ps-4 py-3">No</th>
                            <th class="py-3">Nama Tim</th>
                            <th class="py-3">Kapten & Kontak</th>
                            <th class="py-3">Anggota Tim</th>
                            <th class="pe-4 py-3 text-end">Biaya</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registrations as $index => $reg)
                        <tr>
                            <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                            <td><span class="fw-bold">{{ $reg->nama_tim }}</span></td>
                            <td>
                                <div class="fw-semibold">{{ $reg->nama_kapten }}</div>
                                <div class="small text-muted">{{ $reg->nomor_wa }}</div>
                            </td>
                            <td>
                                <div class="small text-muted" style="max-width: 350px; white-space: normal;">
                                    {{ $reg->anggota_tim }}
                                </div>
                            </td>
                            <td class="pe-4 text-end fw-bold text-success">
                                Rp {{ number_format($selectedEvent->harga_pendaftaran, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Belum ada peserta lunas untuk event ini.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light fw-bold border-top-0">
                        <tr>
                            <td colspan="4" class="ps-4 py-3 text-end text-uppercase">Total Pendapatan Terkumpul</td>
                            <td class="pe-4 py-3 text-end text-primary fs-5">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</td>
                        </tr>
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@else
    <div class="card border-0 shadow-sm rounded-4 py-5">
        <div class="card-body text-center">
            <i class="bi bi-search text-muted opacity-25" style="font-size: 4rem;"></i>
            <h5 class="text-muted mt-3">Pilih event Anda untuk menampilkan laporan detail.</h5>
        </div>
    </div>
@endif

<style>
    @media print {
        @page {
            size: A4;
            margin: 1cm;
        }
        .sidebar, .topbar, form, .btn, .d-flex.justify-content-between, .no-print {
            display: none !important;
        }
        .main-wrapper {
            margin-left: 0 !important;
            padding: 0 !important;
            width: 100% !important;
        }
        .card {
            box-shadow: none !important;
            border: none !important;
        }
        .table {
            border: 1px solid #dee2e6 !important;
            width: 100% !important;
            font-size: 0.85rem !important;
        }
        .bg-primary {
            background-color: #0d6efd !important;
            color: white !important;
            -webkit-print-color-adjust: exact;
            print-color-adjust: exact;
        }
        .table-responsive {
            overflow: visible !important;
        }
    }
</style>
@endsection
