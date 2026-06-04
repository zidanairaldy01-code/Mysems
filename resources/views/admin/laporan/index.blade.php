@extends('layouts.admin')

@section('title', 'Laporan Event Detail - MySEMS')

@section('content')


<style>
    @media print {
        @page {
            size: A4;
            margin: 1cm;
        }
        .sidebar, .topbar, .card-body form, .btn, .d-flex.justify-content-between, .no-print {
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
            width: 100% !important;
            border: 1px solid #dee2e6 !important;
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

<div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
    <div>
        <h2 class="fw-bold">Laporan Detail Event</h2>
        <p class="text-muted">Pilih event untuk melihat daftar tim dan rincian pendaftaran lengkap.</p>
    </div>
    <div class="d-flex flex-wrap gap-2">
        <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-bold export-report-btn" data-format="pdf">
            <i class="bi bi-file-earmark-pdf-fill me-2"></i>Download PDF
        </button>
        <button type="button" class="btn btn-outline-success rounded-pill px-4 fw-bold export-report-btn" data-format="excel">
            <i class="bi bi-file-earmark-spreadsheet-fill me-2"></i>Download Excel
        </button>
    </div>
</div>

<!-- Export Filter Modal -->
<div class="modal fade" id="exportReportModal" tabindex="-1" aria-labelledby="exportReportModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-xl">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exportReportModalLabel">Filter Export Data Event</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="row g-3">
                    <div class="col-md-4">
                        <label for="exportEventType" class="form-label fw-semibold">Jenis Event</label>
                        <select id="exportEventType" class="form-select rounded-pill">
                            <option value="all">Semua Event</option>
                            <option value="tournament">Event Turnamen</option>
                            <option value="umum">Event Umum</option>
                        </select>
                    </div>
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">Pilih Event</label>
                        <div class="d-flex justify-content-between align-items-center mb-2">
                            <small class="text-muted">Pilih event dengan mencentang pilihan di bawah ini (Kosongkan untuk memilih semua).</small>
                            <button type="button" class="btn btn-sm btn-link p-0 text-decoration-none fw-bold" id="toggleExportEventSelection">Pilih Semua</button>
                        </div>
                        <!-- Area Checkbox Event -->
                        <div class="border rounded-3 p-3 bg-light shadow-sm" style="max-height: 180px; overflow-y: auto;">
                            <div class="row gx-3 gy-2" id="exportEventCheckboxContainer">
                                <!-- Checkbox event akan dimuat di sini oleh JS -->
                            </div>
                        </div>
                    </div>

                    <div class="col-12 mt-4">
                        <label class="form-label fw-semibold border-top pt-3 d-block">Pilih Kolom</label>
                        <div class="row gx-2 gy-2">
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="form-check">
                                    <input class="form-check-input export-column-checkbox" type="checkbox" value="nama_event" id="exportColNamaEvent" checked>
                                    <label class="form-check-label" for="exportColNamaEvent">Nama Event</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="form-check">
                                    <input class="form-check-input export-column-checkbox" type="checkbox" value="type" id="exportColType" checked>
                                    <label class="form-check-label" for="exportColType">Tipe Event</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="form-check">
                                    <input class="form-check-input export-column-checkbox" type="checkbox" value="penyelenggara" id="exportColPenyelenggara" checked>
                                    <label class="form-check-label" for="exportColPenyelenggara">Penyelenggara</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="form-check">
                                    <input class="form-check-input export-column-checkbox" type="checkbox" value="nama_panitia" id="exportColNamaPanitia" checked>
                                    <label class="form-check-label" for="exportColNamaPanitia">Nama Panitia</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="form-check">
                                    <input class="form-check-input export-column-checkbox" type="checkbox" value="lokasi" id="exportColLokasi" checked>
                                    <label class="form-check-label" for="exportColLokasi">Lokasi</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="form-check">
                                    <input class="form-check-input export-column-checkbox" type="checkbox" value="tanggal_event" id="exportColTanggal" checked>
                                    <label class="form-check-label" for="exportColTanggal">Tanggal</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="form-check">
                                    <input class="form-check-input export-column-checkbox" type="checkbox" value="jam_event" id="exportColJam" checked>
                                    <label class="form-check-label" for="exportColJam">Jam</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="form-check">
                                    <input class="form-check-input export-column-checkbox" type="checkbox" value="harga_pendaftaran" id="exportColHarga" checked>
                                    <label class="form-check-label" for="exportColHarga">Harga</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="form-check">
                                    <input class="form-check-input export-column-checkbox" type="checkbox" value="slot_tim" id="exportColSlot" checked>
                                    <label class="form-check-label" for="exportColSlot">Slot Tim</label>
                                </div>
                            </div>
                            <div class="col-6 col-md-4 col-lg-3">
                                <div class="form-check">
                                    <input class="form-check-input export-column-checkbox" type="checkbox" value="status" id="exportColStatus" checked>
                                    <label class="form-check-label" for="exportColStatus">Status</label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary rounded-pill" data-bs-dismiss="modal">Batal</button>
                <button type="button" class="btn btn-primary rounded-pill" id="runExportReportBtn">Download</button>
            </div>
        </div>
    </div>
</div>

<!-- Filter Event -->
<div class="card border-0 shadow-sm rounded-4 mb-4">
    <div class="card-body p-4">
        <form action="{{ route('admin.laporan.index') }}" method="GET" class="row g-3 align-items-end">
            <div class="col-md-6">
                <label class="form-label fw-bold">Pilih Event</label>
                <select name="event_id" class="form-select rounded-pill" required onchange="this.form.submit()">
                    <option value="">-- Pilih Event --</option>
                    @foreach($events as $event)
                        <option value="{{ $event->id }}" {{ $selectedEventId == $event->id ? 'selected' : '' }}>
                            {{ $event->nama_event }}
                        </option>
                    @endforeach
                </select>
            </div>
        </form>
    </div>
</div>

@if($selectedEvent)
    <!-- Header Laporan Saat Dicetak -->
    <div class="d-none d-print-block text-center mb-5">
        <h3 class="fw-bold">LAPORAN DATA PESERTA EVENT</h3>
        <h4 class="text-primary">{{ $selectedEvent->nama_event }}</h4>
        <p class="mb-0 text-muted small fw-bold">Waktu Cetak: {{ date('d/m/Y H:i') }}</p>
        <hr>
    </div>

    <!-- Info Singkat Event -->
    <div class="row mb-4">
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-light">
                <div class="card-body text-center p-4">
                    <h6 class="text-muted mb-1">Total Tim Terdaftar</h6>
                    <h2 class="fw-bold mb-0 text-primary">{{ $registrations->count() }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-light">
                <div class="card-body text-center p-4">
                    <h6 class="text-muted mb-1">Harga Pendaftaran</h6>
                    <h2 class="fw-bold mb-0 text-dark">Rp {{ number_format($selectedEvent->harga_pendaftaran, 0, ',', '.') }}</h2>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card border-0 shadow-sm rounded-4 bg-primary text-white">
                <div class="card-body text-center p-4">
                    <h6 class="text-white opacity-75 mb-1">Total Pendapatan</h6>
                    <h2 class="fw-bold mb-0">Rp {{ number_format($totalPendapatan, 0, ',', '.') }}</h2>
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
                            <th class="py-3">Kapten & WA</th>
                            <th class="py-3">Anggota Tim</th>
                            <th class="pe-4 py-3 text-end">Biaya</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($registrations as $index => $reg)
                        <tr>
                            <td class="ps-4 text-muted">{{ $index + 1 }}</td>
                            <td><span class="fw-bold text-main-custom">{{ $reg->nama_tim }}</span></td>
                            <td>
                                <div class="fw-semibold">{{ $reg->nama_kapten }}</div>
                                <div class="small text-muted">{{ $reg->nomor_wa }}</div>
                            </td>
                            <td>
                                <div class="small text-muted" style="max-width: 300px; white-space: normal;">
                                    {{ $reg->anggota_tim }}
                                </div>
                            </td>
                            <td class="pe-4 text-end fw-bold text-success">
                                Rp {{ number_format($selectedEvent->harga_pendaftaran, 0, ',', '.') }}
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-5 text-muted">Belum ada tim yang mendaftar dan lunas.</td>
                        </tr>
                        @endforelse
                    </tbody>
                    <tfoot class="bg-light fw-bold">
                    </tfoot>
                </table>
            </div>
        </div>
    </div>
@else
    <div class="text-center py-5">
        <div class="mb-4">
            <i class="bi bi-file-earmark-text text-muted" style="font-size: 5rem;"></i>
        </div>
        <h5 class="text-muted">Silakan pilih event terlebih dahulu untuk melihat laporan.</h5>
    </div>
@endif

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>

@php
    $mappedEvents = $events->map(function ($event) {
        return [
            'id' => $event->id,
            'nama_event' => $event->nama_event,
            'type' => $event->type,
            'penyelenggara' => $event->user->nama ?? '-',
            'nama_panitia' => $event->nama_panitia,
            'lokasi' => $event->lokasi,
            'tanggal_event' => \Carbon\Carbon::parse($event->tanggal_event)->format('d M Y'),
            'jam_event' => $event->jam_event ? \Carbon\Carbon::parse($event->jam_event)->format('H:i') . ' WIB' : '-',
            'harga_pendaftaran' => $event->harga_pendaftaran == 0 ? 'Gratis' : 'Rp '.number_format($event->harga_pendaftaran, 0, ',', '.'),
            'slot_tim' => $event->slot_tim . ' Tim', 
            'status' => $event->status == 0 ? 'Pending' : ($event->status == 1 ? 'Diterima' : ($event->status == 2 ? 'Selesai' : 'Ditolak')),
        ];
    });
@endphp

<script>
   document.addEventListener('DOMContentLoaded', function () {
        const exportButtons = document.querySelectorAll('.export-report-btn');
        const exportModalElement = document.getElementById('exportReportModal');
        
        if (exportModalElement) {
            document.body.appendChild(exportModalElement);
        }
        
        const exportModal = exportModalElement && typeof bootstrap !== 'undefined' && typeof bootstrap.Modal === 'function'
            ? new bootstrap.Modal(exportModalElement, { backdrop: 'static', keyboard: false })
            : null;
            
        const exportEventType = document.getElementById('exportEventType');
        const exportEventCheckboxContainer = document.getElementById('exportEventCheckboxContainer');
        const toggleExportEventSelection = document.getElementById('toggleExportEventSelection');
        const runExportReportBtn = document.getElementById('runExportReportBtn');
        const exportReportModalLabel = document.getElementById('exportReportModalLabel');
        let currentExportFormat = 'pdf';

        const exportRoute = '{{ route('admin.laporan.export_data') }}';
        const events = @json($mappedEvents) || [];

        // Update pembuatan opsi event menggunakan checkbox
        function buildEventOptions() {
            if (!exportEventCheckboxContainer) return;
            const selectedType = exportEventType ? exportEventType.value : 'all';
            const filteredEvents = events
                .filter(event => selectedType === 'all' || event.type === selectedType)
                .sort((a, b) => a.nama_event.localeCompare(b.nama_event));

            if (!filteredEvents.length) {
                exportEventCheckboxContainer.innerHTML = '<div class="col-12 text-center text-muted small py-3">Tidak ada event yang tersedia</div>';
                return;
            }

            const selectedIds = getSelectedEventIds();
            exportEventCheckboxContainer.innerHTML = filteredEvents.map(event => {
                const isChecked = selectedIds.includes(event.id) ? 'checked' : '';
                return `
                    <div class="col-12 col-md-6">
                        <div class="form-check">
                            <input class="form-check-input export-event-checkbox" type="checkbox" value="${event.id}" id="chkEvent_${event.id}" ${isChecked}>
                            <label class="form-check-label text-truncate d-block" for="chkEvent_${event.id}" title="${event.nama_event}">
                                ${event.nama_event}
                                <small class="text-muted d-block" style="font-size: 0.75rem;">${event.type === 'umum' ? 'Umum' : 'Turnamen'}</small>
                            </label>
                        </div>
                    </div>
                `;
            }).join('');
        }

        // Ambil data ID event dari daftar checkbox yang dicentang
        function getSelectedEventIds() {
            return Array.from(document.querySelectorAll('.export-event-checkbox:checked'))
                .map(cb => parseInt(cb.value, 10))
                .filter(Number.isFinite);
        }

        function getSelectedColumns() {
            return Array.from(document.querySelectorAll('.export-column-checkbox:checked')).map(input => input.value);
        }

        function buildExportParams(format) {
            const params = new URLSearchParams();
            params.append('format', format);
            params.append('event_type', exportEventType ? exportEventType.value : 'all');

            const selectedIds = getSelectedEventIds();
            selectedIds.forEach(id => params.append('event_ids[]', id));

            getSelectedColumns().forEach(column => params.append('columns[]', column));
            return params;
        }

        function exportToExcel(rows, headers) {
            if (typeof XLSX === 'undefined') {
                Swal.fire({ icon: 'error', title: 'Library Excel tidak tersedia', text: 'Muat ulang halaman dan coba lagi.' });
                return;
            }

            const worksheet = XLSX.utils.json_to_sheet(rows, { header: Object.keys(rows[0] || {}), skipHeader: true });
            XLSX.utils.sheet_add_aoa(worksheet, [headers], { origin: 'A1' });
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, 'Events');
            XLSX.writeFile(workbook, `laporan-event-${new Date().toISOString().slice(0, 10)}.xlsx`);
        }

        function exportToPdf(rows, headers) {
            if (!window.jspdf || typeof window.jspdf.jsPDF !== 'function') {
                Swal.fire({ icon: 'error', title: 'Library PDF tidak tersedia', text: 'Muat ulang halaman dan coba lagi.' });
                return;
            }

            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ 
                orientation: 'landscape',
                unit: 'mm',
                format: 'a4'
            });
            const body = rows.map(row => Object.values(row));

            doc.setFontSize(14);
            doc.text('Laporan Event MySEMS', 14, 20);
            if (typeof doc.autoTable === 'function') {
                doc.autoTable({ head: [headers], body, startY: 30, styles: { fontSize: 10, cellPadding: 3 } });
                doc.save(`laporan-event-${new Date().toISOString().slice(0, 10)}.pdf`);
            } else {
                Swal.fire({ icon: 'error', title: 'Plugin PDF AutoTable tidak tersedia', text: 'Muat ulang halaman dan coba lagi.' });
            }
        }

        function openExportModal(format) {
            if (!events || events.length === 0) {
                Swal.fire({ icon: 'warning', title: 'Tidak ada event', text: 'Tidak ada event yang tersedia untuk export.' });
                return;
            }
            currentExportFormat = format;
            if (exportReportModalLabel) {
                exportReportModalLabel.textContent = format === 'pdf' ? 'Download PDF - Filter Event' : 'Download Excel - Filter Event';
            }
            buildEventOptions();
            if (exportModal) {
                exportModal.show();
            }
        }

        function sendExportRequest(format) {
            const columns = getSelectedColumns();
            if (!columns.length) {
                Swal.fire({ icon: 'warning', title: 'Kolom belum dipilih', text: 'Pilih minimal satu kolom untuk export.' });
                return;
            }

            const params = buildExportParams(format);
            fetch(`${exportRoute}?${params.toString()}`, {
                headers: { 'Accept': 'application/json' },
            })
                .then(response => response.json())
                .then(result => {
                    if (!result.success) {
                        throw new Error(result.message || 'Gagal mengambil data export.');
                    }

                    if (!result.data || !result.data.length) {
                        Swal.fire({ icon: 'warning', title: 'Tidak ada data', text: 'Tidak ditemukan event sesuai filter.' });
                        return;
                    }

                    if (format === 'excel') {
                        exportToExcel(result.data, result.headers);
                    } else {
                        exportToPdf(result.data, result.headers);
                    }

                    closeExportModal();
                })
                .catch(error => {
                    Swal.fire({ icon: 'error', title: 'Gagal Export', text: error.message || 'Terjadi kesalahan saat mengambil data export.' });
                });
        }

        if (exportEventType) {
            exportEventType.addEventListener('change', buildEventOptions);
        }

        // Toggle semua checkbox event
        if (toggleExportEventSelection) {
            toggleExportEventSelection.addEventListener('click', function () {
                const checkboxes = document.querySelectorAll('.export-event-checkbox');
                if (!checkboxes.length) return;
                
                const allChecked = Array.from(checkboxes).every(cb => cb.checked);
                checkboxes.forEach(cb => cb.checked = !allChecked);
                this.textContent = allChecked ? 'Pilih Semua' : 'Bersihkan';
            });
        }

        exportButtons.forEach(button => {
            button.addEventListener('click', function () {
                openExportModal(this.getAttribute('data-format') || 'pdf');
            });
        });

        if (runExportReportBtn) {
            runExportReportBtn.addEventListener('click', function () {
                sendExportRequest(currentExportFormat);
            });
        }

        if (exportModal) {
            exportModalElement.addEventListener('shown.bs.modal', function () {
                buildEventOptions();
            });
        } else {
            const closeBtn = exportModalElement.querySelector('.btn-close');
            if (closeBtn) {
                closeBtn.addEventListener('click', closeExportModal);
            }
        }

        function closeExportModal() {
            if (exportModalElement) {
                if (exportModal) {
                    exportModal.hide();
                } else {
                    exportModalElement.style.display = 'none';
                    exportModalElement.classList.remove('show');
                    document.body.classList.remove('modal-open');
                    const backdrop = document.querySelector('.modal-backdrop');
                    if (backdrop) backdrop.remove();
                }
            }
        }
    });
</script>
@endpush
@endsection