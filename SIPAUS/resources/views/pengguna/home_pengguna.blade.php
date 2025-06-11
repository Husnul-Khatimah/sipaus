{{-- File: resources/views/pengguna/home_pengguna.blade.php --}}

@extends('layouts.pengguna') {{-- Menggunakan master layout pengguna --}}

@section('title', 'Dashboard Pengguna') {{-- Judul halaman untuk browser tab --}}
@section('header_title', 'HOME') {{-- Judul yang muncul di header konten --}}

@push('styles')
    {{-- Memuat CSS spesifik untuk halaman ini. Pastikan public/css/home_pengguna.css ada. --}}
    <link rel="stylesheet" href="{{ asset('css/home_pengguna.css') }}" />
@endpush

@section('content')
    <h1>Home</h1>
    <div class="content">
        <div class="dashboard">
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-label">Total Barang</div>
                    <div class="stat-value">{{ $totalBarang ?? 0 }}</div> {{-- Menggunakan data dari controller jika ada --}}
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Permintaan</div>
                    <div class="stat-value">{{ $totalPermintaan ?? 0 }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Total Disetujui</div>
                    <div class="stat-value">{{ $totalDisetujui ?? 0 }}</div>
                </div>
            </div>

            <div class="stats">
                <div class="stat-card">
                    <div class="stat-label">Penggun</div>
                    <div class="stat-value">{{ $totalPengguna ?? 0 }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Survai</div>
                    <div class="stat-value">{{ $totalSurvei ?? 0 }}</div>
                </div>
                <div class="stat-card">
                    <div class="stat-label">Pesan</div>
                    <div class="stat-value">{{ $totalPesan ?? 0 }}</div>
                </div>
            </div>
        </div>

        <div class="dashboard-box">
            <h2>Riwayat Pengambilan</h2>
            <table id="riwayat-pengambilan-table"> {{-- Memberi ID pada tabel riwayat --}}
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Tanggal Ambil</th> {{-- Tambahkan kolom tanggal jika ada --}}
                    </tr>
                </thead>
                <tbody id="riwayat-pengambilan-body">
                    {{-- Data riwayat akan dimuat dari database secara dinamis di sini --}}
                </tbody>
            </table>
        </div>

        {{-- Jika ada grafik di halaman ini, pastikan Chart.js sudah di-include di layout atau di push scripts --}}
        {{-- <div class="charts-wrapper">
            <div class="chart-container">
                <h2>Jumlah Peminjaman Barang</h2>
                <canvas id="barChart"></canvas>
            </div>
            <div class="chart-container">
                <h2>Tren Peminjaman per Bulan</h2>
                <canvas id="lineChart"></canvas>
            </div>
        </div> --}}
    </div>
@endsection

@push('scripts')
{{-- Jika ada Chart.js atau script spesifik lainnya untuk halaman ini, letakkan di sini --}}
{{-- <script src="https://cdn.jsdelivr.net/npm/chart.js"></script> --}}
<script>
    // Mendefinisikan URL API sebagai konstanta JavaScript dari Blade
    const API_PENGGUNA_DASHBOARD_DATA_URL = '{{ url('/api/pengguna/dashboard-data') }}'; // Asumsi ada API untuk data dashboard pengguna

    document.addEventListener("DOMContentLoaded", async () => {
        // Fungsi untuk memuat data statistik dan riwayat
        async function loadDashboardData() {
            try {
                const response = await fetch(API_PENGGUNA_DASHBOARD_DATA_URL);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();

                // Update stat cards (jika Anda memiliki data di API_PENGGUNA_DASHBOARD_DATA_URL)
                document.querySelector('.stat-card:nth-child(1) .stat-value').textContent = data.totalBarang ?? 0;
                document.querySelector('.stat-card:nth-child(2) .stat-value').textContent = data.totalPermintaan ?? 0;
                document.querySelector('.stat-card:nth-child(3) .stat-value').textContent = data.totalDisetujui ?? 0;
                document.querySelector('.stat-card:nth-child(4) .stat-value').textContent = data.totalPengguna ?? 0;
                document.querySelector('.stat-card:nth-child(5) .stat-value').textContent = data.totalSurvei ?? 0;
                document.querySelector('.stat-card:nth-child(6) .stat-value').textContent = data.totalPesan ?? 0;

                // Muat Riwayat Pengambilan
                const riwayatBody = document.getElementById('riwayat-pengambilan-body');
                riwayatBody.innerHTML = '';
                if (data.riwayatPengambilan && data.riwayatPengambilan.length > 0) {
                    data.riwayatPengambilan.forEach((item, index) => {
                        const tr = document.createElement('tr');
                        tr.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${item.nama_barang}</td>
                            <td>${item.jumlah}</td>
                            <td>${item.tanggal_ambil}</td>
                        `;
                        riwayatBody.appendChild(tr);
                    });
                } else {
                    riwayatBody.innerHTML = '<tr><td colspan="4">Belum ada riwayat pengambilan.</td></tr>';
                }

                // Jika ada grafik, updateChartData(data.chartData);
            } catch (error) {
                console.error('Error loading dashboard data:', error);
                // Tambahkan notifikasi jika diperlukan
            }
        }

        // Panggil fungsi untuk memuat data dashboard saat halaman dimuat
        loadDashboardData();
    });
</script>
@endpush
