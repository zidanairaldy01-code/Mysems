@extends('layouts.admin')

@section('title', 'Kelola Peserta - MySEMS')

@section('content')
    <div class="row animate__animated animate__fadeIn">
        <div class="col-12">
            <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
                <div>
                    <h2 class="fw-bold text-dark mb-1">Kelola Peserta
                        {{ $type == 'umum' ? 'Umum' : ($type == 'tournament' ? 'Turnamen' : '') }}</h2>
                    <p class="text-muted mb-0">Daftar {{ $type == 'umum' ? 'peserta' : 'tim' }} yang telah mendaftar di
                        event bertipe {{ $type ?? 'semua' }}.</p>
                </div>

                @php
                    $selectedEventObj = $events->where('id', $selected_event_id)->first();
                    $eventNameTitle = $selectedEventObj ? $selectedEventObj->nama_event : ($type == 'umum' ? 'Semua Event Umum' : ($type == 'tournament' ? 'Semua Event Turnamen' : 'Semua Event'));
                    $cleanEventTitle = preg_replace('/[^A-Za-z0-9_\-]/', '_', $eventNameTitle);

                    $exportList = [];
                    $idx = 1;
                    foreach ($registrations as $reg) {
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
                    <!-- Dropdown Filter Event -->
                    <div class="filter-box p-2 bg-card-custom rounded-4 shadow-sm border border-custom">
                        <form action="{{ route('admin.registration.index') }}" method="GET"
                            class="d-flex align-items-center mb-0">
                            @if(request('type'))
                                <input type="hidden" name="type" value="{{ request('type') }}">
                            @endif
                            <label for="event_id"
                                class="mx-3 fw-semibold text-muted small text-nowrap text-uppercase tracking-wider">Filter
                                Event:</label>
                            <select name="event_id" id="event_id"
                                class="form-select border-0 shadow-none fw-bold text-primary bg-transparent"
                                onchange="this.form.submit()" style="min-width: 220px; cursor: pointer;">
                                <option value="">Semua Event
                                    {{ $type == 'umum' ? 'Umum' : ($type == 'tournament' ? 'Turnamen' : '') }}</option>
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
                        <button type="button" onclick="openExportModal('excel')"
                            class="btn btn-success rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center"
                            title="Export Excel">
                            <i class="bi bi-file-earmark-excel me-2 fs-5"></i> Excel
                        </button>
                        <button type="button" onclick="openExportModal('pdf')"
                            class="btn btn-danger rounded-pill px-4 fw-bold shadow-sm d-flex align-items-center"
                            title="Export PDF">
                            <i class="bi bi-file-earmark-pdf me-2 fs-5"></i> PDF
                        </button>
                    </div>
                </div>
            </div>

            <div class="card border-0 shadow-sm rounded-4 bg-card-custom">
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0 custom-table">
                        <thead>
                            <tr>
                                <th class="ps-4 py-3">{{ $type == 'umum' ? 'Nama Peserta' : 'Nama Tim' }}</th>
                                <th class="py-3">{{ $type == 'umum' ? 'Biodata' : 'Kapten & Kontak' }}</th>
                                <th class="py-3">Mengikuti Event</th>
                                <th class="py-3 text-center">Pembayaran</th>
                                @if($type == 'umum')
                                    <th class="py-3 text-center">Kehadiran</th>
                                @endif
                                <th class="pe-4 py-3 text-end">Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($registrations as $reg)
                                <tr>
                                    <td class="ps-4">
                                        <div class="d-flex align-items-center">
                                            <div class="avatar-circle me-3 bg-primary bg-opacity-10 text-primary fw-bold">
                                                {{ substr($reg->nama_tim, 0, 1) }}
                                            </div>
                                            <div>
                                                <div class="fw-bold text-main-custom">{{ $reg->nama_tim }}</div>
                                                <div class="text-muted small">ID: #REG-{{ $reg->id }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="fw-semibold text-main-custom">{{ $reg->nama_kapten }}</div>
                                        <a href="https://wa.me/{{ $reg->nomor_wa }}" target="_blank"
                                            class="text-success text-decoration-none small d-flex align-items-center">
                                            <i class="bi bi-whatsapp me-1"></i> {{ $reg->nomor_wa }}
                                        </a>
                                    </td>
                                    <td>
                                        <div
                                            class="badge bg-primary bg-opacity-10 text-primary border border-primary border-opacity-10 rounded-pill px-3 py-2 fw-bold">
                                            <i class="bi bi-tag-fill me-1 text-primary"></i> {{ $reg->event->nama_event }}
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        @if($reg->payment_status == 'settlement')
                                            <span class="status-pill status-paid">
                                                <i class="bi bi-check-circle-fill"></i> Lunas
                                            </span>
                                        @elseif($reg->payment_status == 'pending')
                                            <span class="status-pill status-pending">
                                                <i class="bi bi-clock-history"></i> Pending
                                            </span>
                                        @else
                                            <span class="status-pill status-other">
                                                {{ strtoupper($reg->payment_status) }}
                                            </span>
                                        @endif
                                    </td>
                                    @if($type == 'umum')
                                        <td class="text-center">
                                            @if($reg->attended_at)
                                                <div class="text-success fw-bold small animate__animated animate__fadeIn">
                                                    <i class="bi bi-person-check-fill fs-5"></i><br>Hadir
                                                </div>
                                            @else
                                                <div class="text-muted opacity-50 small">
                                                    <i class="bi bi-person-dash fs-5"></i><br>Belum Absen
                                                </div>
                                            @endif
                                        </td>
                                    @endif
                                    <td class="pe-4 text-end">
                                        <div class="dropdown">
                                            <button class="btn btn-action shadow-none" type="button" data-bs-toggle="dropdown"
                                                data-bs-boundary="viewport">
                                                <i class="bi bi-three-dots-vertical"></i>
                                            </button>
                                            <ul
                                                class="dropdown-menu dropdown-menu-end border-0 shadow-lg rounded-4 p-2 animate__animated animate__fadeIn animate__faster">
                                                <li>
                                                    <button class="dropdown-item rounded-3 py-2" data-bs-toggle="modal"
                                                        data-bs-target="#modalDetail{{ $reg->id }}">
                                                        <i class="bi bi-eye me-2 text-primary"></i> Lihat Detail
                                                    </button>
                                                </li>
                                                <li>
                                                    <hr class="dropdown-divider opacity-10">
                                                </li>
                                                <li>
                                                    <form action="{{ route('admin.registration.destroy', $reg->id) }}"
                                                        method="POST" id="delete-form-{{ $reg->id }}">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="button" class="dropdown-item rounded-3 py-2 text-danger"
                                                            onclick="confirmDelete('{{ $reg->id }}', '{{ $reg->event->type == 'umum' ? 'Peserta' : 'Tim' }}')">
                                                            <i class="bi bi-trash me-2"></i> Hapus
                                                            {{ $reg->event->type == 'umum' ? 'Peserta' : 'Tim' }}
                                                        </button>
                                                    </form>
                                                </li>
                                            </ul>
                                        </div>

                                        @push('modals')
                                            <!-- Modal Detail (Polished) -->
                                            <div class="modal fade" id="modalDetail{{ $reg->id }}" tabindex="-1" aria-hidden="true">
                                                <div class="modal-dialog modal-dialog-centered modal-lg">
                                                    <div
                                                        class="modal-content border-0 rounded-5 shadow-lg overflow-hidden bg-card-custom">
                                                        <div class="modal-header bg-primary p-4 border-0 text-white">
                                                            <div class="d-flex align-items-center">
                                                                <div class="bg-white bg-opacity-20 rounded-circle p-2 me-3">
                                                                    <i class="bi bi-person-lines-fill fs-3"></i>
                                                                </div>
                                                                <div>
                                                                    <h5 class="modal-title fw-bold mb-0">Detail Registrasi
                                                                        {{ $reg->event->type == 'umum' ? 'Peserta' : 'Tim' }}</h5>
                                                                    <p class="mb-0 small opacity-75">ID Transaksi:
                                                                        #REG-{{ $reg->id }}</p>
                                                                </div>
                                                            </div>
                                                            <button type="button" class="btn-close btn-close-white"
                                                                data-bs-dismiss="modal"></button>
                                                        </div>
                                                        <div class="modal-body p-4 p-md-5">
                                                            <div class="row g-4">
                                                                <div class="col-md-6">
                                                                    <div
                                                                        class="info-section p-4 rounded-4 bg-card-custom border border-custom h-100">
                                                                        <h6
                                                                            class="fw-bold text-primary mb-4 text-uppercase tracking-wider">
                                                                            Informasi
                                                                            {{ $reg->event->type == 'umum' ? 'Peserta' : 'Pendaftar' }}
                                                                        </h6>
                                                                        <div class="mb-3">
                                                                            <small class="text-muted d-block">Nama
                                                                                {{ $reg->event->type == 'umum' ? 'Peserta' : 'Tim' }}</small>
                                                                            <span
                                                                                class="fw-bold fs-5 text-dark">{{ $reg->nama_tim }}</span>
                                                                        </div>
                                                                        @if($reg->event->type != 'umum')
                                                                            <div class="mb-3">
                                                                                <small class="text-muted d-block">Nama Kapten</small>
                                                                                <span
                                                                                    class="fw-bold text-dark">{{ $reg->nama_kapten }}</span>
                                                                            </div>
                                                                        @endif
                                                                        <div class="mb-3">
                                                                            <small class="text-muted d-block">WhatsApp</small>
                                                                            <span
                                                                                class="fw-bold text-dark">{{ $reg->nomor_wa }}</span>
                                                                        </div>
                                                                        @if($reg->event->type == 'umum')
                                                                            <div class="mb-3">
                                                                                <small class="text-muted d-block">Email</small>
                                                                                <span
                                                                                    class="fw-bold text-primary">{{ $reg->email ?? '-' }}</span>
                                                                            </div>
                                                                        @endif
                                                                    </div>
                                                                </div>
                                                                <div class="col-md-6">
                                                                    <div
                                                                        class="info-section p-4 rounded-4 border border-custom h-100">
                                                                        <h6
                                                                            class="fw-bold text-success mb-4 text-uppercase tracking-wider">
                                                                            Status & Event</h6>
                                                                        <div class="mb-3">
                                                                            <small class="text-muted d-block">Event Diikuti</small>
                                                                            <span
                                                                                class="badge bg-primary rounded-pill">{{ $reg->event->nama_event }}</span>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <small class="text-muted d-block">Status
                                                                                Pembayaran</small>
                                                                            <span
                                                                                class="badge {{ $reg->payment_status == 'settlement' ? 'bg-success' : 'bg-warning' }} rounded-pill">{{ strtoupper($reg->payment_status) }}</span>
                                                                        </div>
                                                                        <div class="mb-3">
                                                                            <small class="text-muted d-block">Status
                                                                                Kehadiran</small>
                                                                            @if($reg->attended_at)
                                                                                <span class="text-success fw-bold"><i
                                                                                        class="bi bi-check-all"></i> Terverifikasi:
                                                                                    <span
                                                                                        class="fw-bold">{{ $reg->attended_at }}</span></span>
                                                                            @else
                                                                                <span class="text-muted">Belum hadir di lokasi</span>
                                                                            @endif
                                                                        </div>
                                                                    </div>
                                                                </div>

                                                                @if($reg->event->type != 'umum')
                                                                    <div class="col-12">
                                                                        <div class="p-4 rounded-4 bg-main-custom border border-custom">
                                                                            <h6 class="fw-bold text-dark mb-3"><i
                                                                                    class="bi bi-people me-2"></i>Anggota Tim</h6>
                                                                            <div class="row g-2">
                                                                                @foreach(explode(',', $reg->anggota_tim) as $anggota)
                                                                                    <div class="col-md-4">
                                                                                        <div
                                                                                            class="bg-card-custom p-2 rounded-3 border border-custom small text-main-custom">
                                                                                            <i
                                                                                                class="bi bi-person-check me-2 text-primary"></i>{{ trim($anggota) }}
                                                                                        </div>
                                                                                    </div>
                                                                                @endforeach
                                                                            </div>
                                                                        </div>
                                                                    </div>
                                                                @endif
                                                            </div>
                                                        </div>
                                                        <div class="modal-footer p-4 border-0">
                                                            <button type="button"
                                                                class="btn btn-outline-secondary rounded-pill px-4"
                                                                data-bs-dismiss="modal">Tutup</button>
                                                            <a href="https://wa.me/{{ $reg->nomor_wa }}" target="_blank"
                                                                class="btn btn-success rounded-pill px-4">
                                                                <i class="bi bi-whatsapp me-2"></i>WhatsApp
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
                                    <td colspan="6" class="text-center py-5">
                                        <img src="https://illustrations.popsy.co/amber/no-results.svg" style="width: 150px;"
                                            class="mb-3">
                                        <p class="text-muted">Belum ada pendaftar untuk kategori ini.</p>
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
            .custom-table thead th {
                background: var(--bg-main);
                text-transform: uppercase;
                font-size: 0.75rem;
                letter-spacing: 0.05em;
                font-weight: 700;
                color: var(--text-muted);
                border-bottom: 1px solid var(--border-color);
            }

            .custom-table tbody tr {
                transition: all 0.2s;
            }

            .avatar-circle {
                width: 45px;
                height: 45px;
                border-radius: 50%;
                display: flex;
                align-items: center;
                justify-content: center;
                font-size: 1.2rem;
            }

            .btn-action {
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

            .btn-action:hover {
                background: var(--primary-color);
                color: white;
                border-color: var(--primary-color);
            }

            .tracking-wider {
                letter-spacing: 0.1em;
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
                            <button type="button"
                                class="btn btn-sm btn-outline-primary rounded-pill px-3 fw-semibold shadow-none"
                                onclick="toggleAllColumns(this)">Batal Pilih Semua</button>
                        </div>
                        <div class="row g-3" id="exportColumnsContainer">
                            <!-- Checkboxes akan diisi dinamis via JS -->
                        </div>
                    </div>
                    <div class="modal-footer p-4 border-0 bg-main-custom border-top border-custom">
                        <button type="button" class="btn btn-outline-secondary rounded-pill px-4"
                            data-bs-dismiss="modal">Batal</button>
                        <button type="button" onclick="executeExport()"
                            class="btn btn-primary rounded-pill px-5 fw-bold shadow-sm d-flex align-items-center">
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
                    doc.text(`Tanggal Cetak: ${new Date().toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric', hour: '2-digit', minute: '2-digit' })}`, 14, 28);

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

            function confirmDelete(id, type) {
                Swal.fire({
                    title: 'Hapus Data ' + type + '?',
                    text: "Data pendaftaran ini akan dihapus secara permanen!",
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