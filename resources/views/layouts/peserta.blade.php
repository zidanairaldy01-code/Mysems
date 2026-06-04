<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MySEMS - Platform Event Sekolah')</title>
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Global Custom Style -->
    <link rel="stylesheet" href="{{ asset('css/global-style.css') }}?v={{ time() }}">
    
    <style>
        /* Override global styles for Navbar Layout */
        body {
            padding-top: 70px; /* Space for fixed navbar */
        }
        
        .navbar {
            background-color: var(--bg-card) !important;
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 0;
            transition: all 0.3s ease;
        }
        
        .navbar-brand img {
            max-height: 45px;
            transition: transform 0.3s ease;
        }
        
        .nav-link {
            color: var(--text-muted) !important;
            font-weight: 500;
            padding: 0.5rem 1rem !important;
            border-radius: 10px;
            transition: all 0.2s ease;
        }
        
        .nav-link:hover {
            color: var(--primary-color) !important;
            background-color: var(--bg-archive);
        }
        
        .nav-link.active {
            color: var(--primary-color) !important;
            background-color: rgba(37, 99, 235, 0.1);
            font-weight: 700;
        }
        
        .navbar-toggler {
            border: none;
            padding: 0;
            color: var(--text-main);
        }
        
        .navbar-toggler:focus {
            box-shadow: none;
        }

        /* Peserta Specific Main Wrapper */
        .peserta-wrapper {
            min-height: calc(100vh - 70px);
            display: flex;
            flex-direction: column;
        }

        .content-body {
            padding: 2rem 0;
        }

        [data-theme='dark'] .navbar {
            background-color: var(--bg-card) !important;
        }

        .navbar-brand img.logo-dark {
            display: none;
        }
        
        [data-theme='dark'] .navbar-brand img.logo-light {
            display: none;
        }
        
        [data-theme='dark'] .navbar-brand img.logo-dark {
            display: block;
        }
    </style>
    
    @stack('styles')
</head>
<body data-theme="light">
    {{-- Top Loading Bar --}}
    <div id="top-loading-bar"></div>

    <script>
        // Pre-render theme check
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            document.body.setAttribute('data-theme', savedTheme);
        })();
    </script>

    <!-- Navbar: Navigasi Atas (Khusus Peserta/Public) -->
    <nav class="navbar navbar-expand-lg fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ route('peserta.index') }}">
                <img src="{{ asset('img/logo-sertifikat1.png') }}" alt="MySEMS" class="me-2 logo-light">
                <img src="{{ asset('img/logo-mysems.png') }}" alt="MySEMS" class="me-2 logo-dark">
                <div class="d-none d-sm-block ms-2 lh-1">
                    <span class="fw-bold d-block" style="color: var(--text-main); font-size: 1.1rem;">MySEMS</span>
                    <small class="text-muted d-block" style="font-size: 0.65rem;">Solusi Digital Management<br>Event Sekolah Anda</small>
                </div>
            </a>
            
            <div class="d-flex align-items-center order-lg-last gap-2">
                {{-- Toggle Dark/Light Mode --}}
                <button class="btn btn-link text-dark p-2 border-0" id="themeToggle" title="Ganti Tema">
                    <i class="bi bi-moon-stars fs-5" id="themeIcon"></i>
                </button>

                {{-- Ikon Notifikasi --}}
                @auth
                <div class="dropdown me-1">
                    <a href="#" class="text-decoration-none text-dark position-relative p-2" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-bell fs-5"></i>
                        @if($unreadNotificationsCount > 0)
                        <span class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger border border-light" style="font-size: 0.5rem; padding: 0.15rem 0.3rem;">
                            {{ $unreadNotificationsCount }}
                        </span>
                        @endif
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-3 p-0 overflow-hidden" style="min-width: 300px; border-radius: 15px;">
                        <div class="bg-primary p-3 text-white">
                            <div class="d-flex justify-content-between align-items-center">
                                <a href="{{ route('peserta.notifications.index') }}" class="text-white text-decoration-none h6 mb-0 fw-bold">Notifikasi</a>
                                <span class="badge bg-white text-primary rounded-pill small">{{ $unreadNotificationsCount }} Baru</span>
                            </div>
                        </div>
                        <div class="notification-list" style="max-height: 300px; overflow-y: auto;">
                            @forelse($notifications as $notification)
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
                                <i class="bi bi-bell-slash text-muted fs-4 mb-2 d-block"></i>
                                <p class="small text-muted mb-0">Tidak ada notifikasi baru</p>
                            </div>
                            @endforelse
                        </div>
                        <div class="bg-light p-2 text-center border-top">
                            <a href="{{ route('peserta.notifications.index') }}" class="small fw-bold text-primary text-decoration-none py-1 d-block">Lihat Semua</a>
                        </div>
                    </ul>
                </div>
                @endauth

                @auth
                    {{-- Profile Dropdown --}}
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle px-2" data-bs-toggle="dropdown">
                            @if(Auth::user()->foto)
                                <img src="{{ asset(Auth::user()->foto) }}" alt="Profile" class="rounded-circle object-fit-cover" width="35" height="35">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama) }}&background=0D6EFD&color=fff" alt="Profile" class="rounded-circle" width="35" height="35">
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-4 overflow-hidden">
                            <li class="px-3 py-2 bg-light bg-opacity-50">
                                <p class="mb-0 fw-bold small text-primary text-truncate" style="max-width: 150px;">{{ Auth::user()->nama }}</p>
                                <p class="mb-0 x-small text-muted" style="font-size: 0.7rem;">Peserta</p>
                            </li>
                            <li><hr class="dropdown-divider my-0 opacity-10"></li>
                            <li><a class="dropdown-item py-2" href="{{ route('peserta.profile.index') }}"><i class="bi bi-person me-2"></i> Profil</a></li>
                            <li><hr class="dropdown-divider my-0 opacity-10"></li>
                            <li>
                                <form action="{{ route('logout') }}" method="POST" id="logout-form" class="d-none">
                                    @csrf
                                </form>
                                <a class="dropdown-item py-2 text-danger" href="#" onclick="confirmLogout(event)">
                                    <i class="bi bi-box-arrow-right me-2"></i> Logout
                                </a>
                            </li>
                        </ul>
                    </div>
                @else
                    <a href="{{ route('login') }}" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold">Login</a>
                @endauth

                <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <i class="bi bi-list fs-2"></i>
                </button>
            </div>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto me-lg-4 gap-lg-2">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('peserta/index') ? 'active' : '' }}" href="{{ route('peserta.index') }}">
                            <i class="bi bi-house-door me-1"></i> Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('peserta/event*') ? 'active' : '' }}" href="{{ route('peserta.event.index') }}">
                            <i class="bi bi-calendar-event me-1"></i> Event Sekolah
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('peserta/klasemen*') ? 'active' : '' }}" href="{{ route('peserta.klasemen.index') }}">
                            <i class="bi bi-trophy me-1"></i> Klasemen
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Wrapper -->
    <div class="peserta-wrapper">
        <!-- Page Content -->
        <main class="container content-body content-fade-in">
            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
                    <i class="bi bi-check-circle-fill me-2"></i>
                    {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @if(session('error'))
                <div class="alert alert-danger alert-dismissible fade show border-0 shadow-sm rounded-4 mb-4" role="alert">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    {{ session('error') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            @yield('content')
        </main>
        
        <footer class="mt-auto py-5 text-white-50" style="background: var(--bg-sidebar);">
            <div class="container text-center">
                <p class="mb-0 small">&copy; {{ date('Y') }} MySEMS. All rights reserved.</p>
            </div>
        </footer>
    </div>

    <!-- Bootstrap 5 JS Bundle -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Notifikasi Toast
        const Toast = Swal.mixin({
            toast: true,
            position: 'top-end',
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });

        @if(session('success'))
            Toast.fire({
                icon: 'success',
                title: "{{ session('success') }}"
            });
        @endif

        @if(session('error'))
            Toast.fire({
                icon: 'error',
                title: "{{ session('error') }}"
            });
        @endif

        @if(session('info'))
            Toast.fire({
                icon: 'info',
                title: "{{ session('info') }}"
            });
        @endif
    </script>
    
    <script>
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

        document.addEventListener('click', (e) => {
            const link = e.target.closest('a');
            if (link && 
                link.getAttribute('href') && 
                !link.getAttribute('href').startsWith('#') && 
                !link.getAttribute('href').startsWith('javascript') && 
                link.getAttribute('target') !== '_blank') {
                const bar = document.getElementById('top-loading-bar');
                if(bar) bar.style.width = '70%';
            }
        });

        // Konfirmasi Logout
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
    
    @stack('scripts')
</body>
</html>
