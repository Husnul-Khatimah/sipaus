    {{-- File: resources/views/auth/login.blade.php --}}

    <!DOCTYPE html>
    <html lang="id">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Login & Sign Up Animasi Baru</title>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
        <meta name="csrf-token" content="{{ csrf_token() }}"> {{-- PENTING: Tambahkan CSRF token --}}
        <style>
            *,
            *::before,
            *::after {
                margin: 0;
                padding: 0;
                box-sizing: border-box;
            }

            body {
                font-family: 'Poppins', sans-serif;
                background-color: #f0f2f5; /* Latar belakang abu-abu muda */
                display: flex;
                justify-content: center;
                align-items: center;
                min-height: 100vh;
                padding: 20px;
            }

            .container {
                background-color: #fff;
                border-radius: 10px;
                box-shadow: 0 14px 28px rgba(0, 0, 0, 0.25), 0 10px 10px rgba(0, 0, 0, 0.22);
                position: relative;
                overflow: hidden; /* Penting untuk animasi geser */
                width: 900px; /* Lebar bisa disesuaikan */
                max-width: 100%;
                min-height: 550px; /* Tinggi bisa disesuaikan */
            }

            .form-container {
                position: absolute;
                top: 0;
                height: 100%;
                transition: all 0.6s ease-in-out;
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                padding: 0 50px;
                text-align: center;
            }

            .sign-in-container {
                left: 0;
                width: 50%;
                z-index: 2; /* Di atas sign-up saat tidak aktif */
            }

            .sign-up-container {
                left: 0; /* Awalnya di posisi yang sama */
                width: 50%;
                opacity: 0; /* Tidak terlihat */
                z-index: 1; /* Di belakang */
            }

            /* Ketika container aktif untuk sign-up */
            .container.right-panel-active .sign-in-container {
                transform: translateX(100%); /* Geser sign-in ke kanan (keluar area kiri) */
                opacity: 0; /* Sembunyikan */
            }

            .container.right-panel-active .sign-up-container {
                transform: translateX(100%); /* Geser sign-up ke area kiri */
                opacity: 1;
                z-index: 5; /* Bawa ke depan */
                animation: show 0.6s;
            }

            @keyframes show {
                0%, 49.99% {
                    opacity: 0;
                    z-index: 1;
                }
                50%, 100% {
                    opacity: 1;
                    z-index: 5;
                }
            }

            .title {
                font-size: 2.2rem;
                color: #333;
                margin-bottom: 20px;
                font-weight: 600;
            }

            .input-field {
                background-color: #eee;
                border: none;
                padding: 12px 15px;
                margin: 8px 0;
                width: 100%;
                border-radius: 25px;
                font-size: 0.9rem;
            }
            .input-field:focus {
                outline-color: #0052D4;
            }


            .btn-action { /* Tombol Login/Sign Up di form */
                border-radius: 20px;
                border: 1px solid #0052D4;
                background-color: #0052D4; /* Warna biru utama */
                color: #FFFFFF;
                font-size: 12px;
                font-weight: bold;
                padding: 12px 45px;
                letter-spacing: 1px;
                text-transform: uppercase;
                transition: transform 80ms ease-in, background-color 0.3s;
                cursor: pointer;
                margin-top: 15px;
            }

            .btn-action:active {
                transform: scale(0.95);
            }
            .btn-action:hover {
                background-color: #0041a3;
            }


            .social-container {
                margin: 20px 0;
            }

            .social-container a {
                border: 1px solid #DDDDDD;
                border-radius: 50%;
                display: inline-flex;
                justify-content: center;
                align-items: center;
                margin: 0 5px;
                height: 40px;
                width: 40px;
                color: #333;
                text-decoration: none;
                transition: background-color 0.3s, color 0.3s;
            }
            .social-container a:hover {
                background-color: #0052D4;
                color: #fff;
            }
            .social-text {
                font-size: 0.9rem;
                color: #555;
                margin-bottom: 10px;
            }


            /* Overlay Container dan Panelnya */
            .overlay-container {
                position: absolute;
                top: 0;
                left: 50%; /* Awalnya di sisi kanan */
                width: 50%;
                height: 100%;
                overflow: hidden;
                transition: transform 0.6s ease-in-out;
                z-index: 100; /* Paling atas */
            }

            .container.right-panel-active .overlay-container {
                transform: translateX(-100%); /* Geser overlay ke kiri */
            }

            .overlay {
                background: linear-gradient(135deg, #367BF5, #0052D4);
                background-repeat: no-repeat;
                background-size: cover;
                background-position: 0 0;
                color: #FFFFFF;
                position: relative;
                left: -100%; /* Untuk menggeser konten di dalam overlay */
                height: 100%;
                width: 200%; /* Dua kali lebar container overlay */
                transform: translateX(0);
                transition: transform 0.6s ease-in-out;
                display: flex; /* Untuk menata panel overlay berdampingan */
            }

            .container.right-panel-active .overlay {
                transform: translateX(50%); /* Geser konten overlay agar panel kanan terlihat */
            }

            .overlay-panel {
                position: absolute;
                display: flex;
                align-items: center;
                justify-content: center;
                flex-direction: column;
                padding: 0 40px;
                text-align: center;
                top: 0;
                height: 100%;
                width: 50%; /* Setiap panel overlay mengisi setengah dari .overlay */
                transform: translateX(0);
                transition: transform 0.6s ease-in-out;
            }
            .overlay-panel h1 { font-weight: bold; margin: 0; font-size: 1.8rem; margin-bottom: 10px;}
            .overlay-panel p { font-size: 14px; font-weight: 300; line-height: 20px; letter-spacing: 0.5px; margin: 20px 0 30px; }

            .btn-ghost { /* Tombol Sign In/Sign Up di overlay */
                background-color: transparent;
                border-color: #FFFFFF;
                border-width: 2px;
                border-style: solid;
            }
            .btn-ghost:hover {
                background-color: rgba(255,255,255,0.1);
            }


            .overlay-left {
                /* Panel ini terlihat saat overlay di sisi kanan (default) */
                transform: translateX(-20%); /* Sedikit efek parallax saat tidak aktif */
            }
            .container.right-panel-active .overlay-left {
                transform: translateX(0); /* Kembali normal saat overlay geser ke kiri */
            }

            .overlay-right {
                /* Panel ini terlihat saat overlay di sisi kiri */
                right: 0;
                transform: translateX(0);
            }
            .container.right-panel-active .overlay-right {
                transform: translateX(20%); /* Sedikit efek parallax saat aktif */
            }

            .image-placeholder-overlay {
                width: 150px; /* Sesuaikan ukuran */
                height: 150px;
                margin-top: 20px;
                /* Styling placeholder jika tidak ada gambar asli */
                /* background-color: rgba(255,255,255,0.2);
                border: 2px dashed rgba(255,255,255,0.5);
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 8px; */
            }
            .image-placeholder-overlay img {
                max-width: 100%;
                max-height: 100%;
                object-fit: contain;
            }

            /* Responsive */
            @media (max-width: 768px) {
                .container { width: 95%; min-height: 600px; }
                .form-container { padding: 0 20px;}
                .title { font-size: 1.8rem; }
                .overlay-container { display: none; } /* Sembunyikan overlay di mobile, tampilkan form berurutan atau tab */
                /* Jika ingin tetap ada overlay di mobile, perlu penyesuaian lebih lanjut */
                .sign-in-container, .sign-up-container { width: 100%; }
                .container.right-panel-active .sign-in-container { transform: translateY(100%); }
                .container.right-panel-active .sign-up-container { transform: translateY(100%); }
                /* Logika mobile bisa dibuat berbeda, misal form atas bawah */
            }

            /* Error and Status Messages */
            .error-message, #status-message {
                padding: 10px;
                border-radius: 5px;
                margin-bottom: 10px;
                font-size: 0.9rem;
                text-align: left;
            }
            .error-message {
                background-color: #f8d7da;
                color: #721c24;
                border: 1px solid #f5c6cb;
            }
            #status-message {
                background-color: #d4edda;
                color: #155724;
                border: 1px solid #c3e6cb;
            }
        </style>
    </head>
    <body>

        <div class="container" id="container">
            <div class="form-container sign-up-container">
                <form method="POST" action="{{ route('forgot_password_submit') }}"> {{-- Asumsi rute untuk ganti sandi --}}
                    @csrf
                    <h1 class="title">Ganti Sandi</h1>
                    <p class="social-text">Masukkan email Anda untuk mengganti sandi</p> {{-- Tambahkan teks petunjuk --}}
                    <input class="input-field" type="email" name="email" placeholder="Email" required />
                    <button class="btn-action">Kirim Reset Link</button>
                </form>
            </div>

            <div class="form-container sign-in-container">
                <form method="POST" action="{{ route('login') }}"> {{-- Rute login --}}
                    @csrf
                    <h1 class="title">Login</h1>
                    <p class="social-text">Gunakan akun Anda</p>
                    <input class="input-field" type="email" name="email" placeholder="Email" value="{{ old('email') }}" required autofocus />
                    <input class="input-field" type="password" name="password" placeholder="Password" required />
                    <a href="{{ route('forgot_password') }}" style="font-size:0.8rem; margin:10px 0 15px 0; color:#555;">Lupa Password?</a> {{-- Link ke lupa password --}}
                    <button class="btn-action">Login</button>
                </form>
            </div>

            <div class="overlay-container">
                <div class="overlay">
                    <div class="overlay-panel overlay-left">
                        <h1>Selamat Datang Kembali!</h1>
                        <p>Untuk tetap terhubung dengan kami, silakan login dengan informasi pribadi Anda.</p>
                        <button class="btn-action btn-ghost" id="signIn">Login</button>
                        <div class="image-placeholder-overlay">
                            <img src="{{ asset('img/undraw_access-account_aydp.svg') }}" onerror="this.onerror=null;this.src='https://placehold.co/150x150/e0e0e0/ffffff?text=Image';" alt="Desk Illustration">
                        </div>
                    </div>
                    <div class="overlay-panel overlay-right">
                        <h1>Halo, Teman!</h1>
                        <p>Masukkan detail pribadi Anda dan mulailah perjalanan bersama kami.</p>
                        <button class="btn-action btn-ghost" id="signUp">Ganti Sandi</button> {{-- Text disesuaikan --}}
                        <div class="image-placeholder-overlay">
                            <img src="{{ asset('img/undraw_forgot-password_odai.svg') }}" onerror="this.onerror=null;this.src='https://placehold.co/150x150/e0e0e0/ffffff?text=Image';" alt="Rocket Illustration">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <script>
            const signUpButton = document.getElementById('signUp');
            const signInButton = document.getElementById('signIn');
            const container = document.getElementById('container');

            signUpButton.addEventListener('click', () => {
                container.classList.add("right-panel-active");
            });

            signInButton.addEventListener('click', () => {
                container.classList.remove("right-panel-active");
            });

            // Menampilkan pesan error atau status dari Laravel
            document.addEventListener('DOMContentLoaded', function() {
                const errorMessageDiv = document.querySelector('.error-message');
                const statusMessageDiv = document.getElementById('status-message');

                if (errorMessageDiv && errorMessageDiv.children.length > 0) {
                    // Jika ada error, pastikan panel sign-in aktif
                    container.classList.remove("right-panel-active");
                    errorMessageDiv.style.display = 'block';
                } else if (statusMessageDiv && statusMessageDiv.textContent.trim() !== '') {
                    // Jika ada status, pastikan panel sign-in aktif
                    container.classList.remove("right-panel-active");
                    statusMessageDiv.style.display = 'block';
                }
            });
        </script>

    </body>
    </html>
