<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Panitia Dashboard - MySEMS')</title>
    @include('layouts.favicon')
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/css/admin.css', 'resources/js/app.js'])
    
    <!-- Global Custom Style (Separated) -->
    <link rel="stylesheet" href="{{ asset('css/global-style.css') }}?v={{ time() }}">
    
    @stack('styles')
</head>
<body data-theme="light">
    {{-- Top Loading Bar --}}
    <div id="top-loading-bar"></div>

    {{-- Floating Hamburger Toggle (Always on Top) --}}
    <button class="btn d-lg-none hamburger-btn shadow-lg" id="sidebar-toggle">
        <i class="bi bi-list fs-3" id="toggle-icon"></i>
    </button>

    <script>
        // Cek tema yang tersimpan di localStorage sebelum halaman dirender sepenuhnya
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            document.body.setAttribute('data-theme', savedTheme);
        })();
    </script>

    <!-- Sidebar: Menu Navigasi Samping (Khusus Panitia) -->
    <aside class="sidebar" id="sidebar">
        
        
        <div class="sidebar-brand text-center" style="padding: 20px 10px;">
            <a href="{{ route('panitia.index') }}" class="text-decoration-none text-white d-block">
                <img src="{{ asset('img/logo-mysems.png') }}" alt="MySEMS Logo" class="img-fluid" style="max-height: 120px; width: auto; filter: drop-shadow(0 0 12px rgba(255,255,255,0.15)); transition: transform 0.3s ease;">
            </a>
            <small class="d-block fw-normal opacity-75 mt-2 px-2" style="font-size: 0.65rem; color: #cbd5e1; letter-spacing: 0.5px; line-height: 1.2;">
                MySEMS Adalah Solusi Digital Management<br>Event Sekolah Anda
            </small>
        </div>
        
        <div class="sidebar-menu">
            <!-- Dashboard -->
            <div class="sidebar-item">
                <a href="{{ route('panitia.index') }}" class="sidebar-link {{ request()->is('panitia/index') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2"></i>
                    <span>Dashboard</span>
                </a>
            </div>

            <!-- Event Turnamen -->
            <div class="sidebar-item">
                <a href="{{ route('panitia.event.index', ['type' => 'tournament']) }}" class="sidebar-link {{ (request()->is('panitia/event') && request()->get('type') == 'tournament') ? 'active' : '' }}"><i class="bi bi-trophy"></i> <span>Event Turnamen</span></a>
            </div>

            <!-- Event Umum -->
            <div class="sidebar-item">
                <a href="{{ route('panitia.event.index', ['type' => 'umum']) }}" class="sidebar-link {{ (request()->is('panitia/event') && request()->get('type') == 'umum') ? 'active' : '' }}"><i class="bi bi-calendar-event"></i> <span>Event Umum</span></a>
            </div>

            <!-- Kelola Peserta (Dropdown) -->
            <div class="sidebar-item">
                <a href="#" class="sidebar-link d-flex justify-content-between align-items-center {{ (request()->is('panitia/registration') || request()->is('panitia/registration/index')) ? '' : 'collapsed' }}" data-bs-toggle="collapse" data-bs-target="#registrationMenu" aria-expanded="{{ (request()->is('panitia/registration') || request()->is('panitia/registration/index')) ? 'true' : 'false' }}">
                    <div>
                        <i class="bi bi-people-fill"></i>
                        <span>Kelola Peserta</span>
                    </div>
                    <i class="bi bi-chevron-down small"></i>
                </a>
                <div class="collapse {{ (request()->is('panitia/registration') || request()->is('panitia/registration/index')) ? 'show' : '' }}" id="registrationMenu">
                    <ul class="sidebar-dropdown">
                        <li><a href="{{ route('panitia.registration.index', ['type' => 'tournament']) }}" class="sidebar-dropdown-link {{ (request()->is('panitia/registration') && request()->get('type') == 'tournament') ? 'text-white' : '' }}">Data Peserta Turnamen</a></li>
                        <li><a href="{{ route('panitia.registration.index', ['type' => 'umum']) }}" class="sidebar-dropdown-link {{ (request()->is('panitia/registration') && request()->get('type') == 'umum') ? 'text-white' : '' }}">Data Peserta Umum</a></li>
                    </ul>
                </div>
            </div>

            <!-- Scan QR Kehadiran (Standalone) -->
            <div class="sidebar-item">
                <a href="{{ route('panitia.registration.scan') }}" class="sidebar-link {{ request()->is('panitia/registration/scan') ? 'active' : '' }}">
                    <i class="bi bi-qr-code-scan"></i>
                    <span>Scan Kehadiran</span>
                </a>
            </div>

            <!-- Laporan Event -->
            <div class="sidebar-item">
                <a href="{{ route('panitia.laporan.index') }}" class="sidebar-link {{ request()->is('panitia/laporan*') ? 'active' : '' }}">
                    <i class="bi bi-file-earmark-bar-graph"></i>
                    <span>Laporan Event</span>
                </a>
            </div>

            <!-- Klasemen Event -->
            <div class="sidebar-item">
                <a href="{{ route('panitia.klasemen.index') }}" class="sidebar-link {{ request()->is('panitia/klasemen*') ? 'active' : '' }}">
                    <i class="bi bi-trophy"></i>
                    <span>Klasemen Event</span>
                </a>
            </div>

            <hr class="mx-3 my-4 border-secondary opacity-25">

            <!-- Logout -->
            <div class="sidebar-item">
                <form action="{{ route('logout') }}" method="POST" id="logout-form" class="d-none">
                    @csrf
                </form>
                <a href="#" class="sidebar-link text-danger" onclick="confirmLogout(event)">
                    <i class="bi bi-box-arrow-right"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </aside>

    <!-- Overlay untuk mobile -->
    <div class="sidebar-overlay d-lg-none" id="sidebar-overlay"></div>

    <!-- Main Wrapper -->
    <div class="main-wrapper">
        <!-- Topbar: Navigasi Bagian Atas -->
        <header class="topbar">

            {{-- Jam Server: Sejajar di Pojok Kiri --}}
            <div class="d-none d-md-flex align-items-center">
                <div class="bg-light shadow-sm rounded-pill px-3 py-1 border small">
                    <i class="bi bi-clock-fill text-primary me-2"></i>
                    <span id="server-clock" class="fw-bold text-dark">Memuat waktu...</span>
                    <span class="badge bg-primary ms-1" style="font-size: 0.6rem;">WIB</span>
                </div>
            </div>

            <div class="d-flex align-items-center ms-auto">
                {{-- Toggle Dark/Light Mode --}}
                <button class="btn btn-link text-dark me-3 p-0 border-0" id="themeToggle" title="Ganti Tema">
                    <i class="bi bi-moon-stars fs-5" id="themeIcon"></i>
                </button>

                {{-- Ikon Notifikasi --}}
                <div class="dropdown me-3">
                    <a href="#" class="text-decoration-none text-dark position-relative" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell fs-5"></i>
                        @if($unreadNotificationsCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.55rem; padding: 0.2rem 0.35rem;">
                            {{ $unreadNotificationsCount }}
                            <span class="visually-hidden">notifikasi belum dibaca</span>
                        </span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3 p-0 overflow-hidden" style="min-width: 320px; border-radius: 15px;">
                        <div class="bg-primary p-3 text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('panitia.notifications.index') }}" class="text-white text-decoration-none h6 mb-0 fw-bold hover-opacity-75">Notifikasi</a>
                                <span class="badge bg-white text-primary rounded-pill small">{{ $unreadNotificationsCount }} Baru</span>
                            </div>
                        </div>
                        <div class="notification-list" style="max-height: 350px; overflow-y: auto;">
                            @forelse($notifications as $notification)
                            <!-- Item Notifikasi Dynamic -->
                            <a href="{{ $notification->data['url'] ?? '#' }}" class="dropdown-item p-3 border-bottom d-flex align-items-start gap-3 text-wrap">
                                <div class="bg-{{ $notification->data['type'] ?? 'primary' }} bg-opacity-10 p-2 rounded-circle text-{{ $notification->data['type'] ?? 'primary' }}">
                                    <i class="bi {{ $notification->data['icon'] ?? 'bi-info-circle' }}"></i>
                                </div>
                                <div>
                                    <p class="mb-1 small"><strong>{{ $notification->data['title'] }}</strong>: {{ $notification->data['message'] }}</p>
                                    <small class="text-muted">{{ $notification->created_at->diffForHumans() }}</small>
                                </div>
                            </a>
                            @empty
                            <div class="p-4 text-center">
                                <i class="bi bi-bell-slash text-muted fs-2 mb-2 d-block"></i>
                                <p class="small text-muted mb-0">Tidak ada notifikasi baru</p>
                            </div>
                            @endforelse
                        </div>
                        <div class="bg-light p-2 text-center border-top">
                            <a href="{{ route('panitia.notifications.index') }}" class="small fw-bold text-primary text-decoration-none py-1 d-block hover-underline">Lihat Semua Notifikasi</a>
                        </div>
                    </ul>
                </div>

                {{-- Dropdown Profil Pengguna --}}
                <div class="dropdown">
                    <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle text-dark" data-bs-toggle="dropdown">
                        <div class="text-end me-2 d-none d-sm-block">
                            <p class="mb-0 fw-bold small text-primary">{{ Auth::user()->nama }}</p>
                            <p class="mb-0 x-small text-muted" style="font-size: 0.75rem;">Panitia Event</p>
                        </div>
                        {{-- Foto Profil (Avatar Otomatis / Foto Asli) --}}
                        @if(Auth::user()->foto)
                            <img src="{{ asset(Auth::user()->foto) }}" alt="Profile" class="rounded-circle object-fit-cover" width="40" height="40">
                        @else
                            <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama) }}&background=0D6EFD&color=fff" alt="Profile" class="rounded-circle" width="40" height="40">
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3">
                        {{-- Link ke Halaman Profil Panitia --}}
                        <li><a class="dropdown-item py-2" href="{{ route('panitia.profile.index') }}"><i class="bi bi-person me-2"></i> Profil Saya</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            {{-- Form & Tombol Logout --}}
                            <a class="dropdown-item py-2 text-danger" href="#" onclick="confirmLogout(event)">
                                <i class="bi bi-box-arrow-right me-2"></i> Logout
                            </a>
                        </li>
                    </ul>
                </div>
            </div>
        </header>

        <!-- Page Content -->
        <main class="content-body">
            <div class="content-container content-fade-in">
                @yield('content')
            </div>
        </main>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Notifikasi Toast untuk Dashboard (Panitia)
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true,
            didOpen: (toast) => {
                toast.addEventListener('mouseenter', Swal.stopTimer)
                toast.addEventListener('mouseleave', Swal.resumeTimer)
            }
        });

        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: "{{ session('error') }}",
                confirmButtonColor: '#2563eb',
            });
        @endif

        @if(session('info'))
            Toast.fire({
                icon: 'info',
                title: "{{ session('info') }}"
            });
        @endif

        @if(session('warning'))
            Toast.fire({
                icon: 'warning',
                title: "{{ session('warning') }}"
            });
        @endif

        @if(session('info'))
            Toast.fire({
                icon: 'info',
                title: "{{ session('info') }}"
            });
        @endif
        // Logic for Sidebar Toggle (Mobile)
        const sidebarToggle = document.getElementById('sidebar-toggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebar-overlay');
        const toggleIcon = document.getElementById('toggle-icon');

        if(sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                
                const isOpen = sidebar.classList.toggle('show');
                if(sidebarOverlay) sidebarOverlay.classList.toggle('show');
                
                // Toggle body scroll
                document.body.classList.toggle('sidebar-open', isOpen);
                
                // Change icon
                if (toggleIcon) {
                    toggleIcon.className = isOpen ? 'bi bi-x fs-3' : 'bi bi-list fs-3';
                }
            });
        }

        // Close sidebar only with the toggle button as requested
        // (Removed auto-close when clicking outside or overlay)

        // Handle window resize
        window.addEventListener('resize', () => {
            if (window.innerWidth >= 992) {
                sidebar.classList.remove('show');
                sidebarOverlay.classList.remove('show');
                // Allow body scroll
                document.body.classList.remove('sidebar-open');
                if (toggleIcon) {
                    toggleIcon.className = 'bi bi-list fs-3';
                }
            }
        });

        // Jam Server Global (Top Bar)
        function updateClock() {
            const clockEl = document.getElementById('server-clock');
            if(!clockEl) return;
            
            const now = new Date();
            const options = { 
                weekday: 'long', 
                year: 'numeric', 
                month: 'long', 
                day: 'numeric',
                hour: '2-digit',
                minute: '2-digit',
                second: '2-digit',
                timeZone: 'Asia/Jakarta'
            };
            const formatter = new Intl.DateTimeFormat('id-ID', options);
            clockEl.textContent = formatter.format(now);
        }

        setInterval(updateClock, 1000);
        updateClock();

        // LOGIKA DARK / LIGHT MODE
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const htmlElement = document.documentElement;

        function updateThemeIcon(theme) {
            if (theme === 'dark') {
                themeIcon.classList.replace('bi-moon-stars', 'bi-sun-fill');
            } else {
                themeIcon.classList.replace('bi-sun-fill', 'bi-moon-stars');
            }
        }

        // Set icon awal sesuai tema yang aktif
        updateThemeIcon(localStorage.getItem('theme') || 'light');

        if(themeToggle) {
            themeToggle.addEventListener('click', () => {
                const currentTheme = htmlElement.getAttribute('data-theme') || 'light';
                const newTheme = currentTheme === 'light' ? 'dark' : 'light';
                
                htmlElement.setAttribute('data-theme', newTheme);
                document.body.setAttribute('data-theme', newTheme);
                localStorage.setItem('theme', newTheme);
                
                updateThemeIcon(newTheme);
            });
        }

        // LOGIKA PAGE LOADING BAR
        window.addEventListener('load', () => {
            const bar = document.getElementById('top-loading-bar');
            if(bar) {
                bar.style.width = '100%';
                setTimeout(() => {
                    bar.style.opacity = '0';
                    setTimeout(() => {
                        bar.style.width = '0';
                        bar.style.opacity = '1';
                    }, 400);
                }, 300);
            }
        });

        // Trigger loading bar saat klik link (internal)
        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (link && 
                link.getAttribute('href') && 
                !link.getAttribute('href').startsWith('#') && 
                !link.getAttribute('href').startsWith('javascript') && 
                link.getAttribute('target') !== '_blank') {
                document.getElementById('top-loading-bar').style.width = '70%';
            }
        });

        // Konfirmasi Logout Global
        function confirmLogout(e) {
            e.preventDefault();
            Swal.fire({
                title: 'Konfirmasi Keluar',
                text: "Apakah Anda yakin ingin keluar dari akun ini?",
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#6c757d',
                confirmButtonText: 'Ya, Keluar!',
                cancelButtonText: 'Batal',
                reverseButtons: true
            }).then((result) => {
                if (result.isConfirmed) {
                    document.getElementById('logout-form').submit();
                }
            });
        }
    </script>
    
    @stack('modals')
    @stack('scripts')
</body>
</html>
