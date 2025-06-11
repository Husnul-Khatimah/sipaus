{{-- File: resources/views/admin/home_admin.blade.php --}}

@extends('layouts.admin')

@section('title', 'Dashboard Admin')
@section('header_title', 'HOME')

@push('styles')
    {{-- PENTING: Jalur ke home_admin.css --}}
    <link rel="stylesheet" href="{{ asset('css/admin/home_admin.css') }}" />
@endpush

@section('content')
    <h1>Home</h1>
    <div class="content">
        <div class="dashboard">
            <div class="stats">
                <div class="stat-card">
                    <div class="stat-label">Total Barang</div>
                    <div class="stat-value">{{ $totalBarang ?? 0 }}</div>
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
            <table id="riwayat-pengambilan-table">
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        <th>Tanggal Ambil</th>
                    </tr>
                </thead>
                <tbody id="riwayat-pengambilan-body">
                </tbody>
            </table>
        </div>

        <div class="charts-wrapper">
            <div class="chart-container">
                <h2>Jumlah Peminjaman Barang</h2>
                <canvas id="barChart"></canvas>
            </div>

            <div class="chart-container">
                <h2>Tren Peminjaman per Bulan</h2>
                <canvas id="lineChart"></canvas>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
{{-- Chart.js sudah di-include di layouts/admin.blade.php --}}
<script>
    const API_DASHBOARD_DATA_URL = '{{ url('/api/admin/dashboard-data') }}';

    document.addEventListener("DOMContentLoaded", async () => {
        async function loadDashboardData() {
            try {
                const response = await fetch(API_DASHBOARD_DATA_URL);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                const data = await response.json();

                document.querySelector('.stat-card:nth-child(1) .stat-value').textContent = data.totalBarang ?? 0;
                document.querySelector('.stat-card:nth-child(2) .stat-value').textContent = data.totalPermintaan ?? 0;
                document.querySelector('.stat-card:nth-child(3) .stat-value').textContent = data.totalDisetujui ?? 0;
                document.querySelector('.stat-card:nth-child(4) .stat-value').textContent = data.totalPengguna ?? 0;
                document.querySelector('.stat-card:nth-child(5) .stat-value').textContent = data.totalSurvei ?? 0;
                document.querySelector('.stat-card:nth-child(6) .stat-value').textContent = data.totalPesan ?? 0;

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

                updateCharts(data.chartData);
            } catch (error) {
                console.error('Error loading dashboard data:', error);
                // Tambahkan notifikasi jika diperlukan
            }
        }

        function updateCharts(chartData) {
            const barData = {
                labels: chartData.barLabels || ["Kertas A4", "Spidol", "Pulpen", "Penghapus", "Buku Tulis"],
                datasets: [{
                    label: "Jumlah Peminjaman",
                    data: chartData.barValues || [20, 15, 10, 8, 12],
                    backgroundColor: "rgba(75, 192, 192, 0.6)",
                    borderColor: "rgba(75, 192, 192, 1)",
                    borderWidth: 1
                }]
            };
            const barOptions = { responsive: true, scales: { y: { beginAtZero: true } } };
            const barCtx = document.getElementById("barChart").getContext("2d");
            new Chart(barCtx, { type: "bar", data: barData, options: barOptions });

            const lineData = {
                labels: chartData.lineLabels || ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun"],
                datasets: [{
                    label: "Jumlah Peminjaman",
                    data: chartData.lineValues || [50, 60, 55, 70, 65, 80],
                    fill: false,
                    borderColor: "rgba(54, 162, 235, 1)",
                    tension: 0.1
                }]
            };
            const lineOptions = { responsive: true, scales: { y: { beginAtZero: true } } };
            const lineCtx = document.getElementById("lineChart").getContext("2d");
            new Chart(lineCtx, { type: "line", data: lineData, options: lineOptions });
        }

        loadDashboardData();
    });
</script>
@endpush
