{{-- File: resources/views/layouts/admin.blade.php --}}

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>SiManis Admin - @yield('title')</title>

    {{-- Font Awesome (dari CDN) --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />

    {{-- CSS Umum untuk Admin --}}
    {{-- PENTING: Jalur ke admin_common.css --}}
    <link rel="stylesheet" href="{{ asset('css/admin/admin_common.css') }}">

    {{-- Chart.js (Jika digunakan di banyak halaman admin, lebih baik di sini) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    {{-- Stylesheets spesifik halaman (akan di-push dari child views) --}}
    @stack('styles')
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="profile" id="profile">
            <div class="profile-img"><i class="fas fa-user-circle"></i></div>
            <div>{{ Auth::check() ? Auth::user()->name : 'Guest' }}</div>
        </div>
        
        <div class="menu">
            <a href="{{ route('admin.home') }}" class="menu-item @if(Request::routeIs('admin.home')) active @endif"><i class="fas fa-home"></i> <span>HOME</span></a>
            <a href="{{ route('admin.data_atk') }}" class="menu-item @if(Request::routeIs('admin.data_atk')) active @endif"><i class="fas fa-box"></i> <span>Data Barang</span></a>
            <a href="{{ route('admin.permintaan') }}" class="menu-item @if(Request::routeIs('admin.permintaan')) active @endif"><i class="fas fa-file-alt"></i> <span>Permintaan</span></a>
            <a href="{{ route('admin.pesan_atk') }}" class="menu-item @if(Request::routeIs('admin.pesan_atk')) active @endif"><i class="fas fa-cart-plus"></i> <span>Pesan ATK</span></a>
            <a href="{{ route('admin.pengguna') }}" class="menu-item @if(Request::routeIs('admin.pengguna')) active @endif"><i class="fas fa-user"></i><span>Pengguna</span></a>
            <a href="{{ route('admin.data_survai') }}" class="menu-item @if(Request::routeIs('admin.data_survai')) active @endif"><i class="fas fa-clipboard-list"></i> <span>Data Survai</span></a>
            <a href="#" id="logout-btn" class="menu-item"><i class="fas fa-sign-out-alt"></i> <span>Log out</span></a>
        </div>
    </div>

    <div id="logout-popup">
        <div class="popup-content">
            <p>Apakah Anda yakin ingin logout?</p>
            <form id="logout-form" action="{{ route('logout') }}" method="POST" style="display: none;">
                @csrf
            </form>
            <button type="button" class="confirm-logout" id="confirm-logout-btn">Logout</button>
            <button type="button" class="cancel-logout" id="cancel-logout">Batal</button>
        </div>
    </div>

    <div class="main-content" id="main-content">
        <div class="header">
            <button class="toggle-btn" id="toggle-btn"><i class="fas fa-bars"></i></button>
            <div class="brand">SiManis</div>
            <span>@yield('header_title', 'Dashboard')</span> 
        </div>

        @yield('content') 

    </div>

    {{-- PENTING: Jalur ke admin_common.js --}}
    <script src="{{ asset('js/admin/admin_common.js') }}"></script>

    @stack('scripts')
</body>
</html>
