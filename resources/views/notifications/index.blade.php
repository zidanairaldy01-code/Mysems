@php
    $layout = Auth::user()->role == '1' ? 'layouts.admin' : (Auth::user()->role == '0' ? 'layouts.panitia' : 'layouts.peserta');
@endphp

@extends($layout)

@section('title', 'Semua Notifikasi - MySEMS')

@section('content')
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <div>
            <h2 class="fw-bold">Pusat Notifikasi</h2>
            <p class="text-muted mb-0">Pantau semua aktivitas dan pemberitahuan sistem Anda di sini.</p>
        </div>
        @if($allNotifications->count() > 0)
            <form action="{{ route($rolePrefix . '.notifications.deleteAll') }}" method="POST" id="formDeleteAll">
                @csrf
                @method('DELETE')
                <button type="button" class="btn btn-outline-danger rounded-pill px-4 fw-bold shadow-sm btn-sm"
                    onclick="confirmDeleteAll(this)">
                    <i class="bi bi-trash3 me-2"></i>Hapus Semua
                </button>
            </form>
        @endif
    </div>

    @push('scripts')
        <script>
            function confirmDeleteAll(btn) {
                Swal.fire({
                    title: 'Hapus Semua Notifikasi?',
                    text: "Tindakan ini tidak dapat dibatalkan!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#0d6efd',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus Semua!',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.closest('form').submit();
                    }
                })
            }
            function confirmDestroy(btn) {
                Swal.fire({
                    title: 'Hapus Notifikasi?',
                    text: "Notifikasi ini akan dihapus permanen!",
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonColor: '#d33',
                    cancelButtonColor: '#6c757d',
                    confirmButtonText: 'Ya, Hapus',
                    cancelButtonText: 'Batal'
                }).then((result) => {
                    if (result.isConfirmed) {
                        btn.closest('form').submit();
                    }
                })
            }
        </script>
    @endpush

    <div class="card border-0 shadow-sm rounded-4 overflow-hidden">
        <div class="card-header bg-white border-0 py-3 d-flex justify-content-between align-items-center">
            <h5 class="fw-bold mb-0"><i class="bi bi-bell me-2 text-primary"></i>Riwayat Notifikasi</h5>
            <span class="badge bg-primary rounded-pill px-3">{{ $allNotifications->total() }} Total</span>
        </div>
        <div class="card-body p-0">
            <div class="list-group list-group-flush">
                @forelse($allNotifications as $notification)
                    <div
                        class="list-group-item p-4 border-start border-4 {{ $notification->read_at ? 'border-transparent' : 'border-primary bg-primary bg-opacity-10' }} transition-all">
                        <div class="d-flex align-items-start gap-3">
                            <div
                                class="bg-{{ $notification->data['type'] ?? 'primary' }} bg-opacity-10 p-3 rounded-circle text-{{ $notification->data['type'] ?? 'primary' }} flex-shrink-0">
                                <i class="bi {{ $notification->data['icon'] ?? 'bi-bell' }} fs-4"></i>
                            </div>
                            <div class="flex-grow-1">
                                <div class="d-flex justify-content-between align-items-start mb-1">
                                    <h6 class="fw-bold mb-0 text-dark">{{ $notification->data['title'] }}</h6>
                                    <small class="text-muted fw-medium">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                                <p class="text-muted mb-3">{{ $notification->data['message'] }}</p>
                                <div class="d-flex gap-2 align-items-center">
                                    @if($notification->data['url'] ?? false)
                                        <a href="{{ route('notifications.read', $notification->id) }}"
                                            class="btn btn-sm btn-primary rounded-pill px-3 shadow-sm">
                                            <i class="bi bi-eye me-1"></i> Lihat Detail
                                        </a>
                                    @endif
                                    <form action="{{ route('notifications.destroy', $notification->id) }}" method="POST"
                                        class="d-inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="button" class="btn btn-sm btn-outline-danger rounded-pill px-3 shadow-sm"
                                            onclick="confirmDestroy(this)">
                                            <i class="bi bi-trash me-1"></i> Hapus
                                        </button>
                                    </form>
                                    @if(!$notification->read_at)
                                        <span class="badge bg-warning text-dark rounded-pill px-2 py-1 small my-auto">Belum
                                            Dibaca</span>
                                    @endif
                                </div>
                            </div>
                        </div>
                    </div>
                @empty
                    <div class="p-5 text-center">
                        <div class="mb-4">
                            <i class="bi bi-bell-slash text-muted" style="font-size: 5rem; opacity: 0.3;"></i>
                        </div>
                        <h5 class="text-muted">Tidak ada riwayat notifikasi</h5>
                        <p class="text-muted small">Anda akan menerima pemberitahuan saat ada aktivitas sistem yang baru.</p>
                    </div>
                @endforelse
            </div>
        </div>
        @if($allNotifications->hasPages())
            <div class="card-footer bg-white border-0 py-3">
                {{ $allNotifications->links() }}
            </div>
        @endif
    </div>

    <style>
        .border-transparent {
            border-left-color: transparent !important;
        }

        .transition-all {
            transition: all 0.2s ease;
        }

        .list-group-item:hover {
            background-color: rgba(var(--bs-primary-rgb), 0.02);
        }

        /* Fix for large pagination icons */
        .pagination svg {
            width: 20px;
            height: 20px;
        }
    </style>
@endsection