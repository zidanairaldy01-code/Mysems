<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MySMES - Platform Event Sekolah')</title>
    @include('/layouts.favicon')
    
    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts: Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <!-- Animate.css -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css"/>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <!-- Global Custom Style -->
    <link rel="stylesheet" href="{{ asset('css/global-style.css') }}?v={{ time() }}">
    
    <style>
        /* Navbar Style */
        body {
            padding-top: 70px;
        }
        
        .navbar {
            background-color: var(--bg-card) !important;
            border-bottom: 1px solid var(--border-color);
            padding: 0.75rem 0;
            transition: all 0.3s ease;
        }
        
        .navbar-brand img {
            max-height: 45px;
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

        .main-wrapper-public {
            min-height: calc(100vh - 70px);
            display: flex;
            flex-direction: column;
        }

        .content-body {
            padding: 2rem 0;
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

        .server-clock-pill {
            background-color: var(--bg-archive);
            border: 1px solid var(--border-color);
            border-radius: 50px;
            padding: 4px 12px;
            font-size: 0.75rem;
            color: var(--text-main);
        }

        /* Footer Links Hover Effect */
        .footer-link {
            transition: color 0.2s ease;
        }
        .footer-link:hover {
            color: #ffffff !important;
        }
    </style>
    
    @stack('styles')
</head>
<body data-theme="light">
    {{-- Top Loading Bar --}}
    <div id="top-loading-bar"></div>

    <script>
        (function() {
            const savedTheme = localStorage.getItem('theme') || 'light';
            document.documentElement.setAttribute('data-theme', savedTheme);
            document.body.setAttribute('data-theme', savedTheme);
        })();
    </script>

    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg fixed-top shadow-sm">
        <div class="container">
            <a class="navbar-brand d-flex align-items-center" href="{{ url('/') }}">
                <img src="{{ asset('img/logo-sertifikat1.png') }}" alt="MySEMS" class="me-2 logo-light">
                <img src="{{ asset('img/logo-mysems.png') }}" alt="MySEMS" class="me-2 logo-dark">
                <div class="d-none d-sm-block ms-2 lh-1">
                    <small class="text-muted d-block" style="font-size: 0.65rem;">Solusi Digital Management<br>Event Sekolah Anda</small>
                </div>
            </a>
            
            <div class="d-flex align-items-center order-lg-last gap-2">
                {{-- Clock --}}
                <div class="d-none d-md-flex align-items-center server-clock-pill me-2">
                    <i class="bi bi-clock me-2 text-primary"></i>
                    <span id="server-clock" class="fw-bold">Loading...</span>
                </div>

                {{-- Toggle Dark/Light Mode --}}
                <button class="btn btn-link text-dark p-2 border-0" id="themeToggle" title="Ganti Tema">
                    <i class="bi bi-moon-stars fs-5" id="themeIcon"></i>
                </button>

                @auth
                    <div class="dropdown">
                        <a href="#" class="d-flex align-items-center text-decoration-none dropdown-toggle px-2" data-bs-toggle="dropdown">
                            @if(Auth::user()->foto)
                                <img src="{{ asset(Auth::user()->foto) }}" alt="Profile" class="rounded-circle object-fit-cover" width="35" height="35">
                            @else
                                <img src="https://ui-avatars.com/api/?name={{ urlencode(Auth::user()->nama) }}&background=0D6EFD&color=fff" alt="Profile" class="rounded-circle" width="35" height="35">
                            @endif
                        </a>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0 mt-2 rounded-4 overflow-hidden">
                            <li class="px-3 py-2 bg-light">
                                <p class="mb-0 fw-bold small text-primary">{{ Auth::user()->nama }}</p>
                                <p class="mb-0 x-small text-muted" style="font-size: 0.7rem;">
                                    @if(Auth::user()->role == '1') Admin @elseif(Auth::user()->role == '0') Panitia @else Peserta @endif
                                </p>
                            </li>
                            <li><hr class="dropdown-divider my-0 opacity-10"></li>
                            @if(Auth::user()->role == '1')
                                <li><a class="dropdown-item py-2" href="{{ route('admin.index') }}"><i class="bi bi-speedometer2 me-2"></i> Dashboard Admin</a></li>
                            @elseif(Auth::user()->role == '0')
                                <li><a class="dropdown-item py-2" href="{{ route('panitia.index') }}"><i class="bi bi-speedometer2 me-2"></i> Dashboard Panitia</a></li>
                            @else
                                <li><a class="dropdown-item py-2" href="{{ route('peserta.index') }}"><i class="bi bi-speedometer2 me-2"></i> Dashboard Peserta</a></li>
                                <li><a class="dropdown-item py-2" href="{{ route('peserta.profile.index') }}"><i class="bi bi-person me-2"></i> Profil</a></li>
                            @endif
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
                    <a href="{{ route('login') }}" class="btn btn-primary rounded-pill px-4 btn-sm fw-bold shadow-sm">Login</a>
                @endauth

                <button class="navbar-toggler ms-2" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                    <i class="bi bi-list fs-2"></i>
                </button>
            </div>

            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto me-lg-4 gap-lg-2">
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('/') ? 'active' : '' }}" href="{{ url('/') }}">
                            <i class="bi bi-house-door me-1"></i> Beranda
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('event*') ? 'active' : '' }}" href="{{ route('public.event.index') }}" id="nav-public-event">
                            <i class="bi bi-calendar-event me-1"></i> Event
                        </a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link {{ request()->is('klasemen*') ? 'active' : '' }}" href="{{ route('public.klasemen.index') }}">
                            <i class="bi bi-trophy me-1"></i> Klasemen
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Main Wrapper -->
    <div class="main-wrapper-public">
        <!-- Page Content -->
        <main class="container content-body content-fade-in">
            @yield('content')
        </main>
        
        <!-- ================= NEW FOOTER SECTION ================= -->
        <footer class="mt-auto pt-5 pb-3 text-white-50" style="background: var(--bg-sidebar);">
            <div class="container">
                <div class="row gy-4">
                    <!-- Kolom 1: Tentang Platform -->
                    <div class="col-lg-5 col-md-12">
                        <h5 class="text-white mb-3 d-flex align-items-center">
                            <img src="{{ asset('/favicon.png') }}" alt="MySEMS Logo" class="me-2" style="height: 40px;"> MySEMS
                        </h5>
                        <p class="small mb-0 pe-lg-4">
                            Solusi Digital Management Event Sekolah Anda. Kami menyediakan platform yang memudahkan pengelolaan, pendaftaran, dan pemantauan berbagai kegiatan serta acara di lingkungan sekolah secara efisien.
                        </p>
                    </div>

                    <!-- Kolom 2: Tautan Cepat -->
                    <div class="col-lg-3 col-md-6">
                        <h5 class="text-white mb-3">Tautan Singkat</h5>
                        <ul class="list-unstyled small mb-0">
                            <li class="mb-2">
                                <a href="{{ url('/') }}" class="text-white-50 text-decoration-none footer-link"><i class="bi bi-chevron-right me-1"></i> Beranda</a>
                            </li>
                            <li class="mb-2">
                                <a href="{{ route('public.event.index') }}" class="text-white-50 text-decoration-none footer-link"><i class="bi bi-chevron-right me-1"></i> Daftar Event</a>
                            </li>
                            <li class="mb-2">
                                <a href="{{ route('public.klasemen.index') }}" class="text-white-50 text-decoration-none footer-link"><i class="bi bi-chevron-right me-1"></i> Papan Klasemen</a>
                            </li>
                        </ul>
                    </div>

                    <!-- Kolom 3: Informasi Kontak -->
                    <div class="col-lg-4 col-md-6">
                        <h5 class="text-white mb-3">Hubungi Kami</h5>
                        <ul class="list-unstyled small mb-0">
                            <li class="mb-3 d-flex">
                                <i class="bi bi-envelope-fill fs-6 me-3 text-white"></i> 
                                <a href="mailto:info@mysems.my.id" class="text-white-50 text-decoration-none footer-link">info@mysems.my.id</a>
                            </li>
                            <li class="mb-3 d-flex">
                                <i class="bi bi-whatsapp fs-6 me-3 text-white"></i> 
                                <a href="https://wa.me/6281234567890" target="_blank" class="text-white-50 text-decoration-none footer-link">+62 812-3456-7890</a>
                                <!-- Ganti nomor WhatsApp di atas sesuai kebutuhan -->
                            </li>
                            <li class="mb-2 d-flex">
                                <i class="bi bi-geo-alt-fill fs-6 me-3 text-white"></i> 
                                <span>Gedung Sekolah Terpadu,<br>Jl. Pendidikan No. 123, Indonesia</span>
                            </li>
                        </ul>
                    </div>
                </div>
                
                <hr class="mt-4 mb-3 border-secondary opacity-50">
                
                <div class="d-flex flex-column flex-md-row justify-content-between align-items-center">
                    <p class="mb-0 small">&copy; {{ date('Y') }} MySEMS. All rights reserved.</p>
                    <div class="mt-2 mt-md-0 d-flex gap-3">
                        <a href="#" class="text-white-50 footer-link"><i class="bi bi-instagram"></i></a>
                        <a href="#" class="text-white-50 footer-link"><i class="bi bi-facebook"></i></a>
                        <a href="#" class="text-white-50 footer-link"><i class="bi bi-youtube"></i></a>
                    </div>
                </div>
            </div>
        </footer>
        <!-- ================= END NEW FOOTER SECTION ================= -->
    </div>

    <!-- Scripts -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <script>
        // Jam Server
        function updateClock() {
            const clockEl = document.getElementById('server-clock');
            if(!clockEl) return;
            const now = new Date();
            const options = { weekday: 'short', day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit', second: '2-digit' };
            clockEl.textContent = new Intl.DateTimeFormat('id-ID', options).format(now);
        }
        setInterval(updateClock, 1000);
        updateClock();

        // Dark Mode
        const themeToggle = document.getElementById('themeToggle');
        const themeIcon = document.getElementById('themeIcon');
        const htmlElement = document.documentElement;

        function updateThemeIcon(theme) {
            if (theme === 'dark') {
                themeIcon.className = 'bi bi-sun-fill fs-5 text-warning';
            } else {
                themeIcon.className = 'bi bi-moon-stars fs-5 text-dark';
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

        // Loading Bar
        window.addEventListener('load', () => {
            const bar = document.getElementById('top-loading-bar');
            if(bar) {
                bar.style.width = '100%';
                setTimeout(() => { bar.style.opacity = '0'; setTimeout(() => { bar.style.width = '0'; bar.style.opacity = '1'; }, 400); }, 300);
            }
        });

        // Notifications
        @if(session('success'))
            Swal.fire({ toast: true, position: 'top-end', icon: 'success', title: "{{ session('success') }}", showConfirmButton: false, timer: 3000 });
        @endif
        @if(session('error'))
            Swal.fire({ title: 'Error', text: "{{ session('error') }}", icon: 'error' });
        @endif
    </script>
    <script>
        @if(session('success'))
            Swal.fire({
                icon: 'success',
                title: 'Berhasil!',
                text: @json(session('success')),
                showConfirmButton: false,
                timer: 3000
            });
        @endif

        @if(session('error'))
            Swal.fire({
                icon: 'error',
                title: 'Oops...',
                text: @json(session('error')),
                confirmButtonColor: 'var(--primary-color)'
            });
        @endif

        @if($errors->any())
            Swal.fire({
                icon: 'error',
                title: 'Validasi Gagal',
                text: @json($errors->first()),
                confirmButtonColor: 'var(--primary-color)'
            });
        @endif

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