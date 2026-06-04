@extends('layouts.admin')

@section('title', 'Persetujuan Event - MySEMS')

@section('content')
    {{-- Bagian Header Halaman --}}
    <div class="mb-4">
        <h2 class="fw-bold">Persetujuan Event</h2>
        <p class="text-muted">Tinjau dan setujui event yang diajukan oleh Panitia agar bisa dipublikasikan.</p>
    </div>

    {{-- Tabel Pengajuan Event --}}
    <div class="card border-0 shadow-sm rounded-4">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr>
                            <th class="ps-4 py-3 border-0">Nama Event</th>
                            <th class="py-3 border-0">Diajukan Oleh</th>
                            <th class="py-3 border-0">Tanggal Event</th>
                            <th class="py-3 border-0">Lokasi</th>
                            <th class="pe-4 py-3 border-0 text-end">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        {{-- Melakukan looping pada data event yang berstatus pending (0) --}}
                        @forelse($events as $event)
                            <tr>
                                <td class="ps-4 fw-bold text-primary">{{ $event->nama_event }}</td>
                                <td>
                                    <div class="d-flex align-items-center">
                                        <img src="https://ui-avatars.com/api/?name={{ urlencode($event->user->nama) }}&background=E2E8F0&color=475569"
                                            class="rounded-circle me-2" width="25">
                                        <span class="small">{{ $event->user->nama }}</span>
                                    </div>
                                </td>
                                <td>{{ \Carbon\Carbon::parse($event->tanggal_event)->format('d M Y') }}</td>
                                <td>{{ $event->lokasi }}</td>
                                <td class="pe-4">
                                    <div class="d-flex justify-content-end align-items-center gap-1 gap-md-2">
                                        {{-- Tombol Detail Event --}}
                                        <button type="button" class="btn btn-sm btn-outline-primary rounded-pill px-2 px-md-3"
                                            data-bs-toggle="modal" data-bs-target="#detailModal-{{ $event->uuid }}"
                                            title="Detail Event">
                                            <i class="bi bi-info-circle"></i><span class="d-none d-md-inline ms-1">Detail</span>
                                        </button>

                                        {{-- Form untuk menyetujui event --}}
                                        <form action="{{ route('admin.event.approve', $event->uuid) }}" method="POST"
                                            class="approve-form m-0">
                                            @csrf
                                            <button type="button"
                                                class="btn btn-sm btn-success rounded-pill px-2 px-md-3 approve-btn"
                                                title="Setujui Event">
                                                <i class="bi bi-check-lg"></i><span
                                                    class="d-none d-md-inline ms-1">Terima</span>
                                            </button>
                                        </form>

                                        {{-- Form untuk menolak event --}}
                                        <form action="{{ route('admin.event.reject', $event->uuid) }}" method="POST"
                                            class="reject-form m-0">
                                            @csrf
                                            <button type="button"
                                                class="btn btn-sm btn-outline-danger rounded-pill px-2 px-md-3 reject-btn"
                                                title="Tolak Event">
                                                <i class="bi bi-x-lg"></i><span class="d-none d-md-inline ms-1">Tolak</span>
                                            </button>
                                        </form>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            {{-- Tampilan jika tidak ada data pengajuan --}}
                            <tr>
                                <td colspan="5" class="text-center py-5 text-muted">
                                    <i class="bi bi-clipboard-check fs-1 d-block mb-2"></i>
                                    Tidak ada pengajuan event saat ini.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    @push('modals')
        @foreach($events as $event)
            <!-- Modal Detail Event -->
            <div class="modal fade" id="detailModal-{{ $event->uuid }}" tabindex="-1"
                aria-labelledby="detailModalLabel-{{ $event->uuid }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered modal-lg">
                    <div class="modal-content border-0 shadow-lg rounded-4 text-start">
                        <div class="modal-header border-0 pb-0">
                            <h5 class="modal-title fw-bold" id="detailModalLabel-{{ $event->uuid }}">Detail Pengajuan Event</h5>
                            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                        </div>
                        <div class="modal-body p-4">
                            <div class="row g-4">
                                {{-- Sisi Kiri: Poster --}}
                                <div class="col-md-5">
                                    <div class="rounded-4 overflow-hidden border bg-light mb-3 position-relative"
                                        style="height: 250px;">
                                        @if($event->foto_event)
                                            <img src="{{ asset($event->foto_event) }}" class="w-100 h-100 object-fit-cover"
                                                alt="{{ $event->nama_event }}">
                                        @else
                                            <div class="d-flex flex-column align-items-center justify-content-center h-100 text-muted">
                                                <i class="bi bi-image fs-1 mb-2"></i>
                                                <span class="small">Tidak ada foto</span>
                                            </div>
                                        @endif
                                        <span
                                            class="badge bg-primary position-absolute top-0 start-0 m-3 px-3 py-2 rounded-pill shadow-sm">
                                            {{ $event->type === 'umum' ? 'Event Umum' : 'Turnamen' }}
                                        </span>
                                    </div>
                                </div>

                                {{-- Sisi Kanan: Informasi --}}
                                <div class="col-md-7">
                                    <h3 class="fw-bold text-primary mb-3">{{ $event->nama_event }}</h3>

                                    <div class="row g-3">
                                        <div class="col-6">
                                            <small class="text-muted d-block mb-1">Diajukan Oleh</small>
                                            <span class="fw-semibold text-dark">{{ $event->user->nama }}</span>
                                            <span class="d-block small text-muted">({{ $event->nama_panitia }})</span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block mb-1">Biaya Pendaftaran</small>
                                            <span
                                                class="fw-bold {{ $event->harga_pendaftaran == 0 ? 'text-success' : 'text-primary' }}">
                                                {{ $event->harga_pendaftaran == 0 ? 'GRATIS' : 'Rp ' . number_format($event->harga_pendaftaran, 0, ',', '.') }}
                                            </span>
                                        </div>

                                        <div class="col-6">
                                            <small class="text-muted d-block mb-1">Tanggal Pelaksanaan</small>
                                            <span class="fw-semibold text-dark">
                                                <i class="bi bi-calendar3 me-1 text-primary"></i>
                                                {{ \Carbon\Carbon::parse($event->tanggal_event)->format('d M Y') }}
                                            </span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block mb-1">Waktu</small>
                                            <span class="fw-semibold text-dark">
                                                <i class="bi bi-clock me-1 text-primary"></i>
                                                {{ \Carbon\Carbon::parse($event->jam_event)->format('H:i') }} WIB
                                            </span>
                                        </div>

                                        <div class="col-6">
                                            <small class="text-muted d-block mb-1">Lokasi</small>
                                            <span class="fw-semibold text-dark">
                                                <i class="bi bi-geo-alt me-1 text-primary"></i>
                                                {{ $event->lokasi }}
                                            </span>
                                        </div>
                                        <div class="col-6">
                                            <small class="text-muted d-block mb-1">Kuota Slot</small>
                                            <span class="fw-semibold text-dark">
                                                <i class="bi bi-people me-1 text-primary"></i>
                                                {{ $event->slot_tim }} Tim
                                            </span>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr class="my-4 opacity-50">

                            {{-- Deskripsi Event --}}
                            <div>
                                <h6 class="fw-bold text-dark mb-2">Deskripsi Event</h6>
                                <div class="p-3 bg-light rounded-3 text-muted"
                                    style="white-space: pre-line; max-height: 150px; overflow-y: auto; font-size: 0.9rem;">
                                    {{ $event->deskripsi ?? 'Tidak ada deskripsi lengkap untuk event ini.' }}
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer border-0 pt-0">
                            {{-- Tombol Aksi di Modal --}}
                            <form action="{{ route('admin.event.approve', $event->uuid) }}" method="POST"
                                class="d-inline approve-form">
                                @csrf
                                <button type="button" class="btn btn-success rounded-pill px-4 me-1 approve-btn">Terima</button>
                            </form>
                            <form action="{{ route('admin.event.reject', $event->uuid) }}" method="POST"
                                class="d-inline reject-form">
                                @csrf
                                <button type="button"
                                    class="btn btn-outline-danger rounded-pill px-4 me-1 reject-btn">Tolak</button>
                            </form>
                            <button type="button" class="btn btn-light rounded-pill px-4" data-bs-dismiss="modal">Tutup</button>
                        </div>
                    </div>
                </div>
            </div>
        @endforeach
    @endpush

    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                // Event listener click untuk approve-btn
                document.addEventListener('click', function (e) {
                    if (e.target.classList.contains('approve-btn') || e.target.closest('.approve-btn')) {
                        const btn = e.target.classList.contains('approve-btn') ? e.target : e.target.closest('.approve-btn');
                        const form = btn.closest('.approve-form');

                        Swal.fire({
                            title: 'Setujui Event?',
                            text: "Event akan dipublikasikan dan dapat diakses oleh publik.",
                            icon: 'question',
                            showCancelButton: true,
                            confirmButtonColor: '#198754',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, Setujui!',
                            cancelButtonText: 'Batal'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                form.submit();
                            }
                        });
                    }

                    // Event listener click untuk reject-btn
                    if (e.target.classList.contains('reject-btn') || e.target.closest('.reject-btn')) {
                        const btn = e.target.classList.contains('reject-btn') ? e.target : e.target.closest('.reject-btn');
                        const form = btn.closest('.reject-form');

                        Swal.fire({
                            title: 'Tolak Event?',
                            text: "Masukkan alasan penolakan pengajuan event ini:",
                            input: 'textarea',
                            inputPlaceholder: 'Tulis alasan penolakan di sini...',
                            inputAttributes: {
                                'aria-label': 'Tulis alasan penolakan di sini'
                            },
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonColor: '#dc3545',
                            cancelButtonColor: '#6c757d',
                            confirmButtonText: 'Ya, Tolak!',
                            cancelButtonText: 'Batal',
                            preConfirm: (value) => {
                                if (!value || value.trim() === '') {
                                    Swal.showValidationMessage('Alasan penolakan wajib diisi!')
                                }
                                return value;
                            }
                        }).then((result) => {
                            if (result.isConfirmed) {
                                // Tambahkan input hidden ke form sebelum submit
                                const hiddenInput = document.createElement('input');
                                hiddenInput.type = 'hidden';
                                hiddenInput.name = 'alasan_ditolak';
                                hiddenInput.value = result.value;
                                form.appendChild(hiddenInput);
                                form.submit();
                            }
                        });
                    }
                });
            });
        </script>
    @endpush
@endsection