<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - MySEMS</title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css">
    <!-- Google Fonts: Inter & Outfit -->
    <link
        href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&family=Outfit:wght@300;400;500;600;700;800;900&display=swap"
        rel="stylesheet">

    <!-- Custom Login CSS (Separated) -->
    <link rel="stylesheet" href="{{ asset('css/login.css') }}?v={{ time() }}">
</head>

<body>

    <!-- BACKGROUND ELEMENTS: Gambar olahraga melayang secara random -->
    <div class="floating-bg" id="dynamicBg">
        <!-- Elemen akan diisi oleh JavaScript -->
    </div>

    <!-- PRELOADER: Animasi loading awal -->
    <div id="preloader">
        <div class="loader-wrapper">
            <div class="loader-icon mb-2">
                <img src="{{ asset('img/logo-mysems.png') }}" alt="Logo" style="width: 200px; height: auto;">
            </div>
            <div class="loader-progress">
                <div class="loader-progress-bar"></div>
            </div>
            <p class="fw-bold text-white opacity-75 small text-uppercase letter-spacing-2 mt-3"
                style="letter-spacing: 3px;">MySEMS SYSTEM</p>
        </div>
    </div>

    <!-- MAIN LOGIN CONTAINER -->
    <div class="login-page-wrapper">
        <div class="login-container" id="loginContent">
            {{-- Tombol Musik (Mute/Unmute) --}}
            <div class="music-control-wrapper">
                <div class="music-title">
                    <span>Now Playing: MySEMS - Tournament Background Music &nbsp;&nbsp;&nbsp;</span>
                    <span>Now Playing: MySEMS - Tournament Background Music &nbsp;&nbsp;&nbsp;</span>
                </div>
                <div class="music-control" id="musicToggle" title="Play/Pause">
                    <i class="bi bi-volume-up-fill" id="musicIcon"></i>
                </div>
                <div class="music-control" id="musicSkip" title="Skip Lagu">
                    <i class="bi bi-skip-forward-fill"></i>
                </div>
                <audio id="bgMusic">
                    <source id="musicSource" src="{{ asset('audio/bg_music.mp3') }}" type="audio/mpeg">
                    Browser Anda tidak mendukung elemen audio.
                </audio>
            </div>

            <div class="login-card">

                <div class="text-center mb-4">
                    <div class="brand-logo-wrapper mx-auto mb-2">
                        <img src="{{ asset('img/logo-sertifikat1.png') }}" alt="MySEMS Logo" class="login-brand-logo">
                    </div>
                    <h2 class="fw-800 text-dark mb-1">Selamat Datang</h2>
                    <p class="text-muted small">Silakan masuk untuk mengelola event sekolah Anda</p>
                </div>

                {{-- Tampilan Error Validation --}}
                @if($errors->any())
                    <div class="alert alert-danger border-0 rounded-4 py-3 small mb-4">
                        <i class="bi bi-exclamation-triangle-fill me-2"></i>
                        {{ $errors->first() }}
                    </div>
                @endif

                <form method="POST" action="{{ route('login.post') }}">
                    @csrf

                    <!-- Email Field -->
                    <div class="mb-4">
                        <label for="email" class="form-label">ALAMAT EMAIL</label>
                        <input type="email" class="form-control" id="email" name="email" value="{{ old('email') }}"
                            placeholder="contoh@email.com" required autofocus>
                    </div>

                    <!-- Password Field -->
                    <div class="mb-4">
                        <div class="d-flex justify-content-between align-items-center mb-1">
                            <label for="password" class="form-label mb-0">KATA SANDI</label>
                            <a href="#" class="text-primary text-decoration-none x-small fw-bold"
                                style="font-size: 0.75rem;">Lupa Password?</a>
                        </div>
                        <div class="input-group">
                            <input type="password" class="form-control password-input" id="password" name="password"
                                placeholder="••••••••" required>
                            <span class="input-group-text" id="togglePassword">
                                <i class="bi bi-eye-slash" id="toggleIcon"></i>
                            </span>
                        </div>
                    </div>

                    <!-- Remember Me -->
                    <div class="mb-4 form-check">
                        <input type="checkbox" class="form-check-input" id="remember" name="remember" value="1">
                        <label class="form-check-label small text-muted" for="remember">Ingat saya di perangkat
                            ini</label>
                    </div>

                    <!-- Submit Button/ini Tombol Login -->
                    <div class="d-grid">
                        <button type="submit" class="btn btn-login shadow-sm">
                            Masuk Ke Dashboard <i class="bi bi-arrow-right-short ms-1"></i>
                        </button>
                    </div>
                </form>

                <div class="text-center mt-5">
                    <p class="small text-muted mb-0">Belum punya akun? <a href="#"
                            class="fw-bold text-primary text-decoration-none">Hubungi Admin</a></p>
                    <hr class="my-4 opacity-10">
                    <a href="{{ url('/') }}" class="text-decoration-none text-muted small fw-medium">
                        <i class="bi bi-arrow-left-circle me-1"></i> Kembali ke Halaman Peserta
                    </a>
                </div>

            </div>
        </div>
    </div>

    <!-- SCRIPTS -->
    <script>
        // Konfigurasi gambar olahraga
        const sportsImages = [
            'https://images.unsplash.com/photo-1593787467023-50684704734a?q=80&w=400&auto=format&fit=crop', // Volleyball
            'https://images.unsplash.com/photo-1574629810360-7efbbe195018?q=80&w=400&auto=format&fit=crop', // Soccer
            'https://images.unsplash.com/photo-1626224583764-f87db24ac4ea?q=80&w=400&auto=format&fit=crop', // Badminton
            'https://images.unsplash.com/photo-1546519638-68e109498ffc?q=80&w=400&auto=format&fit=crop', // Basketball
            'https://images.unsplash.com/photo-1542751371-adc38448a05e?q=80&w=400&auto=format&fit=crop', // Gaming
            'https://images.unsplash.com/photo-1517466787929-bc90951d0974?q=80&w=400&auto=format&fit=crop', // Football
            'https://images.unsplash.com/photo-1595435934249-5df7ed86e1c0?q=80&w=400&auto=format&fit=crop', // Table Tennis
            'https://images.unsplash.com/photo-1612872087720-bb876e2e67d1?q=80&w=400&auto=format&fit=crop', // Volleyball 2
        ];

        function createBgElements() {
            const container = document.getElementById('dynamicBg');
            const elementCount = 20;

            for (let i = 0; i < elementCount; i++) {
                let top, left;
                let isSafe = false;

                while (!isSafe) {
                    top = Math.random() * 100;
                    left = Math.random() * 100;

                    const isLeftSide = left < 22;
                    const isRightSide = left > 78;

                    if (isLeftSide || isRightSide) {
                        isSafe = true;
                    }
                }

                const img = document.createElement('img');
                const randomSrc = sportsImages[Math.floor(Math.random() * sportsImages.length)];

                img.src = randomSrc;
                img.className = 'floating-item animate-item';

                const size = 180 + Math.random() * 220;
                const delay = Math.random() * 10;

                img.style.top = `${top}%`;
                img.style.left = `${left}%`;
                img.style.width = `${size}px`;
                img.style.animationDelay = `${delay}s`;

                container.appendChild(img);
            }
        }

        window.addEventListener('load', function () {
            createBgElements();
            const preloader = document.getElementById('preloader');
            const loginContent = document.getElementById('loginContent');

            setTimeout(() => {
                preloader.style.opacity = '0';
                preloader.style.visibility = 'hidden';
                loginContent.classList.add('loaded');
            }, 1000);
        });

        // Toggle Password Visibility
        const toggleBtn = document.getElementById('togglePassword');
        const passwordInput = document.getElementById('password');
        const icon = document.getElementById('toggleIcon');

        toggleBtn.addEventListener('click', function () {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);

            if (type === 'text') {
                icon.classList.replace('bi-eye-slash', 'bi-eye');
            } else {
                icon.classList.replace('bi-eye', 'bi-eye-slash');
            }
        });

        // Logika Kontrol Musik & Playlist
        const musicToggle = document.getElementById('musicToggle');
        const musicSkip = document.getElementById('musicSkip');
        const bgMusic = document.getElementById('bgMusic');
        const musicSource = document.getElementById('musicSource');
        const musicIcon = document.getElementById('musicIcon');
        const musicTitleSpans = document.querySelectorAll('.music-title span');

        const playlist = [
            { file: 'bg_music.mp3', title: 'MySEMS - Themes Sound Track' },
            { file: 'Skaville Sunrise (Take 2).mp3', title: 'Skaville Sunrise' },
            { file: 'Sengit dan Epik (Operatic Symphony Version).mp3', title: 'Operatic Symphony' },
            { file: 'Championship Stakes.mp3', title: 'Championship Stakes' },
            { file: 'The Final Push.mp3', title: 'The Final Push' }
        ];

        let currentTrackIndex = Math.floor(Math.random() * playlist.length);
        let isPlaying = false;

        loadTrack(currentTrackIndex);

        function loadTrack(index) {
            const track = playlist[index];
            musicSource.src = `{{ asset('audio/') }}/${track.file}`;
            bgMusic.load();

            musicTitleSpans.forEach(span => {
                span.innerHTML = `Now Playing: ${track.title} &nbsp;&nbsp;&nbsp;`;
            });

            if (isPlaying) {
                bgMusic.play();
            }
        }

        musicToggle.addEventListener('click', function () {
            if (!isPlaying) {
                bgMusic.play();
                musicIcon.classList.replace('bi-volume-up-fill', 'bi-volume-up');
                musicToggle.classList.remove('muted');
                isPlaying = true;
            } else {
                if (bgMusic.muted) {
                    bgMusic.muted = false;
                    musicIcon.classList.replace('bi-volume-mute-fill', 'bi-volume-up-fill');
                    musicToggle.classList.remove('muted');
                } else {
                    bgMusic.muted = true;
                    musicIcon.classList.replace('bi-volume-up-fill', 'bi-volume-mute-fill');
                    musicToggle.classList.add('muted');
                }
            }
        });

        musicSkip.addEventListener('click', function () {
            currentTrackIndex = (currentTrackIndex + 1) % playlist.length;
            loadTrack(currentTrackIndex);

            if (!isPlaying) {
                isPlaying = true;
                bgMusic.play();
                musicIcon.classList.replace('bi-volume-up-fill', 'bi-volume-up');
            }
        });

        bgMusic.addEventListener('ended', function () {
            currentTrackIndex = (currentTrackIndex + 1) % playlist.length;
            loadTrack(currentTrackIndex);
        });

        document.addEventListener('click', function () {
            if (!isPlaying) {
                bgMusic.play().then(() => {
                    isPlaying = true;
                }).catch(error => console.log("Autoplay blocked"));
            }
        }, { once: true });
        // Logika Remember Me (Save Email to LocalStorage)
        document.addEventListener('DOMContentLoaded', function () {
            const rememberCheckbox = document.getElementById('remember');
            const emailInput = document.getElementById('email');
            const loginForm = document.querySelector('form');

            // Cek apakah ada email tersimpan
            const savedEmail = localStorage.getItem('remembered_email');
            if (savedEmail) {
                emailInput.value = savedEmail;
                rememberCheckbox.checked = true;
            }

            loginForm.addEventListener('submit', function () {
                if (rememberCheckbox.checked) {
                    localStorage.setItem('remembered_email', emailInput.value);
                } else {
                    localStorage.removeItem('remembered_email');
                }
            });
        });
    </script>
</body>

</html>