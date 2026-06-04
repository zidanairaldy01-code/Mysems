@extends('layouts.panitia')

@section('title', 'Peserta Event - Panitia')

@section('content')
    <div class="row animate__animated animate__fadeIn">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h2 class="fw-bold text-body mb-1">Peserta Event
                        {{ $type == 'umum' ? 'Umum' : ($type == 'tournament' ? 'Turnamen' : '') }}</h2>
                    <p class="text-muted mb-0 small">Kelola kehadiran dan data {{ $type == 'umum' ? 'peserta' : 'tim' }}
                        untuk event Anda.</p>
                </div>

            @php
                $selectedEventObj = $events->where('id', $selected_event_id)->first();
                $eventNameTitle = $selectedEventObj ? $selectedEventObj->nama_event : ($type == 'umum' ? 'Semua Event Umum' : ($type == 'tournament' ? 'Semua Event Turnamen' : 'Semua Event'));
                $cleanEventTitle = preg_replace('/[^A-Za-z0-9_\-]/', '_', $eventNameTitle);

                $exportList = [];
                $idx = 1;
                foreach($registrations as $reg) {
                    $item = [
                        'No' => $idx++,
                        'ID Pendaftaran' => '#REG-' . $reg->id,
                        ($type == 'umum' ? 'Nama Peserta' : 'Nama Tim') => $reg->nama_tim,
                    ];
                    if ($type != 'umum') {
                        $item['Kapten'] = $reg->nama_kapten;
                    }
                    $item['WhatsApp'] = $reg->nomor_wa;
                    if ($type == 'umum') {
                        $item['Email'] = $reg->email ?? '-';
                    } else {
                        $item['Anggota Tim'] = $reg->anggota_tim ? str_replace(',', ', ', $reg->anggota_tim) : '-';
                    }
                    $item['Event'] = $reg->event->nama_event ?? '-';
                    $item['Pembayaran'] = strtoupper($reg->payment_status === 'settlement' ? 'Lunas' : $reg->payment_status);
                    if ($type == 'umum') {
                        $item['Kehadiran'] = $reg->attended_at ? 'Hadir (' . \Carbon\Carbon::parse($reg->attended_at)->format('d/m/Y H:i') . ')' : 'Belum';
                    }
                    $item['Daftar'] = $reg->created_at ? \Carbon\Carbon::parse($reg->created_at)->format('d/m/Y') : '-';
                    $exportList[] = $item;
                }
            @endphp
            <div class="d-flex flex-wrap align-items-center gap-3">
                <div class="filter-wrapper p-2 bg-card-custom rounded-4 shadow-sm border border-custom d-flex align-items-center w-100 w-md-auto">
                    <form action="{{ route('panitia.registration.index') }}" method="GET" class="d-flex align-items-center mb-0 flex-grow-1">
                        @if(request('type'))
                            <input type="hidden" name="type" value="{{ request('type') }}">
                        @endif
                        <i class="bi bi-funnel text-muted ms-2"></i>
                        <select name="event_id" id="event_id" class="form-select border-0 shadow-none fw-bold bg-transparent ms-1" onchange="this.form.submit()" style="cursor: pointer;">
                            <option value="">Semua Event Saya</option>
                            @foreach($events as $event)
                                <option value="{{ $event->id }}" {{ $selected_event_id == $event->id ? 'selected' : '' }}>
                                    {{ $event->nama_event }}
                                </option>
                            @endforeach
                        </select>
                    </form>
                </div>

                <!-- Tombol Export PDF & Excel -->
                <div class="d-flex gap-2">
                    <button type="button" onclick="openExportModal('excel')" class="btn btn-success rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center" title="Export Excel">
                        <i class="bi bi-file-earmark-excel me-2 fs-5"></i> Excel
                    </button>
                    <button type="button" onclick="openExportModal('pdf')" class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center" title="Export PDF">
                        <i class="bi bi-file-earmark-pdf me-2 fs-5"></i> PDF
                    </button>
                </div>
            </div>
        </div>

            <div class="card border-0 shadow-sm rounded-4 overflow-hidden bg-card-custom">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 premium-table">
                        <thead>
                            <tr>
                                <th class="ps-4">{{ $type == 'umum' ? 'Nama Peserta' : 'Nama Tim' }}</th>
                                <th>{{ $type == 'umum' ? 'Biodata' : 'Kapten' }}</th>
                                <th class="text-center">Pembayaran</th>
                                @if($type == 'umum')
                                <th class="text-center">Kehadiran</th>
                                @endif
                                <th class="pe-4 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($registrations as $reg)
                                <tr>
                                    <td class="ps-4">
                                        <div class="fw-bold text-main-custom">{{ $reg->nama_tim }}</div>
                                        <div
                                            class="badge bg-primary bg-opacity-10 text-primary small rounded-pill px-2 py-1 fw-bold">
                                            <i class="bi bi-tag-fill me-1"></i> {{ $reg->event->nama_event }}
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-main-custom">{{ $reg->nama_kapten }}</div>
                                        <div class="text-muted small"><i class="bi bi-whatsapp me-1"></i>{{ $reg->nomor_wa }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($reg->payment_status == 'settlement')
                                            <span
                                                class="badge bg-success bg-opacity-10 text-success border border-success border-opacity-25 rounded-pill px-3 py-2 fw-bold">
                                                <i class="bi bi-check-circle me-1"></i> LUNAS
                                            </span>
                                        @else
                                            <span
                                                class="badge bg-warning bg-opacity-10 text-warning-emphasis border border-warning border-opacity-25 rounded-pill px-3 py-2 fw-bold">
                                                {{ strtoupper($reg->payment_status) }}
                                            </span>
                                        @endif
                                    </td>
                                    @if($type == 'umum')
                                    <td class="text-center">
                                        @if($reg->attended_at)
                                            <span class="text-success small fw-bold">
                                                <i class="bi bi-check2-all fs-5 d-block"></i> Hadir
                                            </span>
                                        @else
                                            <span class="text-muted small opacity-50">
                                                <i class="bi bi-dash-circle d-block fs-5"></i> Belum
                                            </span>
                                        @endif
                                    </td>
                                    @endif
                                    <td class="pe-4 text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-action-custom shadow-none" type="button"
                                                data-bs-toggle="dropdown" aria-expanded="false">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul
                                                class="dropdown-menu dropdown-menu-end shadow-lg border-0 rounded-4 bg-card-custom p-2">
                                                <li>
                                                    <button class="dropdown-item py-2" data-bs-toggle="modal"
                                                        data-bs-target="#modalDetail{{ $reg->id }}">
                                                        <i class="bi bi-eye me-2 text-primary"></i> Lihat Detail
                                                    </button>
                                                </li>
                                                <li>
                                                    <a class="dropdown-item py-2" href="https://wa.me/{{ $reg->nomor_wa }}"
                                                        target="_blank">
                                                        <i class="bi bi-whatsapp me-2 text-success"></i> WhatsApp
                                                    </a>
                                                </li>
                                            </ul>
                                        </div>

                                        @push('modals')
                                            <!-- Modal Detail (Panitia Polished) -->
                                            <div class="modal fade" id="modalDetail{{ $reg->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered">
                                                    <div
                                                        class="modal-content border-0 rounded-5 shadow-lg bg-card-custom overflow-hidden">
                                                        <div class="modal-header bg-dark text-white p-4 border-0">
                                                            <h5 class="modal-title fw-bold">Data
                                                                {{ $reg->event->type == 'umum' ? 'Peserta' : 'Tim' }}</h5>
                                                            <button type="button" class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body p-4">
                                                            <div class="text-center mb-4">
                                                                <div class="avatar-lg mx-auto bg-primary bg-opacity-10 text-primary rounded-circle d-flex align-items-center justify-content-center mb-2"
                                                                    style="width: 70px; height: 70px;">
                                                                    <i class="bi bi-person-badge fs-1"></i>
                                                                </div>
                                                                <h4 class="fw-bold mb-0">{{ $reg->nama_tim }}</h4>
                                                                <p class="text-muted small">{{ $reg->event->nama_event }}</p>
                                                            </div>

                                                            <ul class="list-group list-group-flush border-top border-bottom mb-4">
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                                                                    <span class="text-muted">Nama
                                                                        {{ $reg->event->type == 'umum' ? 'Lengkap' : 'Kapten' }}</span>
                                                                    <span class="fw-bold">{{ $reg->nama_kapten }}</span>
                                                                </li>
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                                                                    <span class="text-muted">No. WhatsApp</span>
                                                                    <span class="fw-bold">{{ $reg->nomor_wa }}</span>
                                                                </li>
                                                                @if($reg->event->type == 'umum')
                                                                    <li
                                                                        class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                                                                        <span class="text-muted">Email</span>
                                                                        <span class="fw-bold">{{ $reg->email ?? '-' }}</span>
                                                                    </li>
                                                                @endif
                                                                <li
                                                                    class="list-group-item d-flex justify-content-between align-items-center px-0 bg-transparent">
                                                                    <span class="text-muted">Waktu Daftar</span>
                                                                    <span
                                                                        class="fw-bold">{{ $reg->created_at->format('d/m/Y H:i') }}</span>
                                                                </li>
                                                            </ul>

                                                            @if($reg->event->type != 'umum')
                                                                <div class="mb-4">
                                                                    <p class="fw-bold small text-uppercase text-muted mb-2">Anggota Tim
                                                                    </p>
                                                                    <div class="d-flex flex-wrap gap-2">
                                                                        @foreach(explode(',', $reg->anggota_tim) as $anggota)
                                                                            <span
                                                                                class="badge bg-main-custom text-main-custom border border-custom rounded-pill px-3 py-2 fw-normal">{{ trim($anggota) }}</span>
                                                                        @endforeach
                                                                    </div>
                                                                </div>
                                                            @endif

                                                            @if($type == 'umum')
                                                            <div class="p-3 bg-main-custom rounded-4 text-center border border-custom">
                                                                <p class="text-muted small mb-1">Status Kehadiran</p>
                                                                @if($reg->attended_at)
                                                                    <h5 class="text-success fw-bold mb-0"><i
                                                                            class="bi bi-check-circle-fill me-2"></i>SUDAH ABSEN</h5>
                                                                    <small class="text-muted fw-bold">{{ $reg->attended_at }}</small>
                                                                @else
                                                                    <h5 class="text-secondary fw-bold mb-0"><i
                                                                            class="bi bi-dash-circle me-2"></i>BELUM ABSEN</h5>
                                                                @endif
                                                            </div>
                                                            @endif
                                                        </div>
                                                        <div class="modal-footer p-4 border-0">
                                                            <a href="https://wa.me/{{ $reg->nomor_wa }}"
                                                                class="btn btn-success rounded-pill w-100 fw-bold py-2">
                                                                <i class="bi bi-whatsapp me-2"></i>Hubungi via WhatsApp
                                                            </a>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endpush
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center py-5">
                                        <i class="bi bi-people display-4 text-muted opacity-25"></i>
                                        <p class="text-muted mt-3">Belum ada peserta yang mendaftar.</p>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    @push('styles')
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" />
        <style>
            .bg-card {
                background-color: var(--bg-card) !important;
                color: var(--text-main) !important;
            }

            .filter-wrapper {
                min-width: 0;
                width: 100%;
                gap: 0.75rem;
            }

            .filter-wrapper form {
                width: 100%;
            }

            .filter-wrapper .form-select {
                min-width: 0;
                width: 100%;
                background-color: var(--bg-body) !important;
                color: var(--text-main) !important;
                border-color: var(--border-color) !important;
            }

            .premium-table thead th {
                font-size: 0.75rem;
                text-transform: uppercase;
                color: var(--text-muted);
                padding: 14px 10px;
                border-bottom: 2px solid var(--border-color);
                white-space: nowrap;
            }

            .premium-table tbody td {
                padding: 14px 10px;
                vertical-align: middle;
            }

            .premium-table tbody td .badge {
                white-space: nowrap;
            }

            .btn-action-custom {
                width: 38px;
                height: 38px;
                padding: 0;
                border-radius: 50%;
                display: inline-flex;
                align-items: center;
                justify-content: center;
                color: var(--text-muted);
                border: 1px solid var(--border-color);
                background: var(--bg-card);
                transition: all 0.3s;
                margin-left: auto;
            }

            .btn-action-custom:hover {
                background: var(--primary-color);
                color: white;
                border-color: var(--primary-color);
            }

            @media (max-width: 767.98px) {
                .filter-wrapper {
                    flex-wrap: wrap;
                    padding: 0.75rem;
                }

                .filter-wrapper .bi-funnel {
                    font-size: 1rem;
                }

                .premium-table thead th {
                    font-size: 0.65rem;
                    padding: 10px 8px;
                }

                .premium-table tbody td {
                    padding: 10px 8px;
                }

                .premium-table thead th:nth-child(2),
                .premium-table thead th:nth-child(4) {
                    display: none;
                }

                .premium-table tbody td:nth-child(2),
                .premium-table tbody td:nth-child(4) {
                    display: none;
                }

                .premium-table tbody td .fw-semibold,
                .premium-table tbody td .text-muted {
                    font-size: 0.8rem;
                }

                .btn.btn-sm {
                    font-size: 0.78rem;
                    padding: 0.45rem 0.8rem;
                }
            }
        </style>
    @endpush

@push('modals')
<!-- Modal Export Filter -->
<div class="modal fade" id="exportModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content border-0 rounded-5 shadow-lg overflow-hidden bg-card-custom border border-custom">
            <div class="modal-header bg-primary p-4 border-0 text-white">
                <div class="d-flex align-items-center">
                    <div class="bg-white bg-opacity-20 rounded-circle p-2 me-3">
                        <i class="bi bi-file-earmark-check fs-3"></i>
                    </div>
                    <div>
                        <h5 class="modal-title fw-bold mb-0" id="exportModalTitle">Pilih Kolom Ekspor</h5>
                        <p class="mb-0 small opacity-75">Tentukan data apa saja yang ingin disertakan pada dokumen</p>
                    </div>
                </div>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4 p-md-5">
                <div class="d-flex justify-content-between align-items-center mb-4 pb-2 border-bottom border-custom">
                    <span class="fw-bold small text-uppercase tracking-wider text-muted">Daftar Kolom Laporan</span>
                    <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold shadow-none" onclick="toggleAllColumns(this)">Batal Pilih Semua</button>
                </div>
                <div class="row g-3" id="exportColumnsContainer">
                    <!-- Checkboxes akan diisi dinamis via JS -->
                </div>
            </div>
            <div class="modal-footer p-4 border-0 bg-main-custom border-top border-custom">
                <button type="button" class="btn btn-outline-secondary rounded-pill px-4" data-bs-dismiss="modal">Batal</button>
                <button type="button" onclick="executeExport()" class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm d-flex align-items-center">
                    <i class="bi bi-download me-2"></i> Download Laporan
                </button>
            </div>
        </div>
    </div>
</div>
@endpush

@push('scripts')
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf-autotable/3.5.28/jspdf.plugin.autotable.min.js"></script>
<script>
    const registrationsData = @json($exportList);
    const eventNameTitle = @json($eventNameTitle);
    const cleanEventTitle = @json($cleanEventTitle);

    let currentExportFormat = 'pdf';
    let exportModalInstance = null;

    function openExportModal(format) {
        if (registrationsData.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Data Kosong', text: 'Tidak ada data peserta untuk diekspor.' });
            return;
        }
        currentExportFormat = format;
        document.getElementById('exportModalTitle').textContent = format === 'pdf' ? 'Pengaturan Export PDF' : 'Pengaturan Export Excel';
        
        const container = document.getElementById('exportColumnsContainer');
        container.innerHTML = '';
        
        const sampleRow = registrationsData[0];
        Object.keys(sampleRow).forEach(key => {
            const id = 'col_' + key.replace(/[^a-zA-Z0-9]/g, '_');
            container.innerHTML += `
                <div class="col-6 col-md-4">
                    <div class="form-check p-3 bg-card-custom rounded-4 border border-custom d-flex align-items-center h-100 shadow-sm">
                        <input class="form-check-input ms-0 me-3 shadow-none col-check" type="checkbox" value="${key}" id="${id}" checked style="cursor: pointer; width: 1.25em; height: 1.25em;">
                        <label class="form-check-label fw-bold small text-body user-select-none flex-grow-1 mb-0" for="${id}" style="cursor: pointer;">${key}</label>
                    </div>
                </div>
            `;
        });

        const modalEl = document.getElementById('exportModal');
        if (!exportModalInstance) {
            exportModalInstance = new bootstrap.Modal(modalEl);
        }
        exportModalInstance.show();
    }

    function toggleAllColumns(btn) {
        const checkboxes = document.querySelectorAll('.col-check');
        const allChecked = Array.from(checkboxes).every(cb => cb.checked);
        checkboxes.forEach(cb => cb.checked = !allChecked);
        btn.textContent = allChecked ? 'Pilih Semua' : 'Batal Pilih Semua';
        btn.classList.toggle('btn-outline-primary', allChecked);
        btn.classList.toggle('btn-primary', !allChecked);
    }

    function executeExport() {
        const selectedCols = Array.from(document.querySelectorAll('.col-check:checked')).map(cb => cb.value);
        if (selectedCols.length === 0) {
            Swal.fire({ icon: 'warning', title: 'Kolom Kosong', text: 'Pilih minimal satu kolom untuk dicetak.' });
            return;
        }
        
        const filteredData = registrationsData.map(row => {
            const newRow = {};
            selectedCols.forEach(col => {
                if (row[col] !== undefined) newRow[col] = row[col];
            });
            return newRow;
        });

        if (currentExportFormat === 'excel') {
            const worksheet = XLSX.utils.json_to_sheet(filteredData);
            const workbook = XLSX.utils.book_new();
            XLSX.utils.book_append_sheet(workbook, worksheet, 'Peserta');
            XLSX.writeFile(workbook, `Data_Peserta_${cleanEventTitle}_${new Date().toISOString().slice(0, 10)}.xlsx`);
        } else {
            if (!window.jspdf || !window.jspdf.jsPDF) {
                Swal.fire({ icon: 'error', title: 'Library Error', text: 'Library PDF belum termuat.' });
                return;
            }
            const { jsPDF } = window.jspdf;
            const doc = new jsPDF({ orientation: 'landscape', unit: 'mm', format: 'a4' });
            
            doc.setFontSize(16);
            doc.setFont('helvetica', 'bold');
            doc.text(`LAPORAN DATA PESERTA - ${eventNameTitle.toUpperCase()}`, 14, 20);
            
            doc.setFontSize(10);
            doc.setFont('helvetica', 'normal');
            doc.text(`Tanggal Cetak: ${new Date().toLocaleDateString('id-ID', {day:'numeric', month:'long', year:'numeric', hour:'2-digit', minute:'2-digit'})}`, 14, 28);
            
            const headers = selectedCols;
            const body = filteredData.map(row => selectedCols.map(col => row[col] !== undefined ? row[col] : '-'));
            
            doc.autoTable({
                head: [headers],
                body: body,
                startY: 34,
                styles: { fontSize: 9, cellPadding: 3, overflow: 'linebreak' },
                headStyles: { fillColor: [13, 110, 253], textColor: [255, 255, 255], fontStyle: 'bold' },
                alternateRowStyles: { fillColor: [248, 249, 250] },
            });
            
            doc.save(`Data_Peserta_${cleanEventTitle}_${new Date().toISOString().slice(0, 10)}.pdf`);
        }
        
        if (exportModalInstance) {
            exportModalInstance.hide();
        } else {
            const el = document.getElementById('exportModal');
            const inst = bootstrap.Modal.getInstance(el);
            if (inst) inst.hide();
        }
    }
    </script>
    @endpush
@endsection