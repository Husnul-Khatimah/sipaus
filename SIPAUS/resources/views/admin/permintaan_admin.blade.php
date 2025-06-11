{{-- File: resources/views/admin/permintaan_admin.blade.php --}}

@extends('layouts.admin')

@section('title', 'Daftar Permintaan')
@section('header_title', 'Permintaan Barang')

@push('styles')
    {{-- Memuat CSS spesifik untuk halaman ini. Pastikan public/css/permintaan_admin.css ada. --}}
    <link rel="stylesheet" href="{{ asset('css/permintaan_admin.css') }}" />
@endpush

@section('content')
    <h1>Permintaan</h1>
    <div class="dashboard-box">
        <div id="notification-permintaan"></div> {{-- ID notifikasi khusus untuk permintaan --}}
        <table id="tabel-permintaan">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Tanggal </th>
                    <th>Tempat </th>
                    <th>Status</th> {{-- Kolom status perlu ditambahkan --}}
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tabel-permintaan-body"> {{-- Tambahkan ID untuk JavaScript nanti --}}
                {{-- Data permintaan akan dimuat dari database secara dinamis di sini --}}
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
<script>
    // Mendefinisikan URL dasar sebagai konstanta JavaScript dari Blade
    const API_PERMINTAAN_URL = '{{ url('/api/admin/permintaan') }}';
    const ADMIN_PERMINTAAN_BASE_URL = '{{ url('/admin/permintaan') }}';
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Script khusus untuk halaman Permintaan
    const notificationPermintaan = document.getElementById('notification-permintaan');
    const tbodyPermintaan = document.getElementById('tabel-permintaan-body');

    function tampilkanNotifPermintaan(msg, type = 'success') {
        notificationPermintaan.textContent = msg;
        notificationPermintaan.classList.remove('success', 'error');
        notificationPermintaan.classList.add('show', type);
        setTimeout(() => {
            notificationPermintaan.classList.remove('show');
            notificationPermintaan.textContent = '';
        }, 2500);
    }

    async function loadPermintaanData() {
        try {
            const response = await fetch(API_PERMINTAAN_URL); // Menggunakan konstanta URL
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
            }
            const data = await response.json();
            tbodyPermintaan.innerHTML = '';

            if (data.length > 0) {
                data.forEach((item, index) => {
                    const row = tbodyPermintaan.insertRow();
                    row.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${item.user ? item.user.name : 'N/A'}</td> {{-- Sesuaikan nama kolom dari DB --}}
                        <td>${item.user ? item.user.nip : 'N/A'}</td>
                        <td>${item.atk ? item.atk.nama : 'N/A'}</td>
                        <td>${item.jumlah}</td>
                        <td>${item.tanggal_permintaan}</td>
                        <td>${item.tempat || 'N/A'}</td>
                        <td>${item.status}</td>
                        <td>
                            <button class="btn-view" data-id="${item.id}">Setujui</button>
                            <button class="btn-view1" data-id="${item.id}">Tolak</button>
                        </td>
                    `;
                });
            } else {
                tbodyPermintaan.innerHTML = '<tr><td colspan="9">Belum ada data permintaan.</td></tr>';
            }
        } catch (error) {
            console.error('Error loading permintaan data:', error);
            tampilkanNotifPermintaan("Gagal memuat data permintaan: " + error.message, 'error');
        }
    }

    document.addEventListener('DOMContentLoaded', loadPermintaanData);

    // Event listener untuk tombol Setujui/Tolak (Event Delegation)
    tbodyPermintaan.addEventListener('click', async (e) => {
        if (e.target.classList.contains('btn-view') || e.target.classList.contains('btn-view1')) {
            const id = e.target.dataset.id;
            const action = e.target.classList.contains('btn-view') ? 'setujui' : 'tolak';
            const confirmationMsg = `Apakah Anda yakin ingin ${action} permintaan ini?`;

            if (confirm(confirmationMsg)) {
                try {
                    const response = await fetch(`${ADMIN_PERMINTAAN_BASE_URL}/${id}/${action}`, {
                        method: 'POST', // Atau PUT jika mengubah status
                        headers: {
                            'X-CSRF-TOKEN': CSRF_TOKEN,
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ status: action === 'setujui' ? 'Disetujui' : 'Ditolak' })
                    });
                    const result = await response.json();
                    if (response.ok) {
                        tampilkanNotifPermintaan(result.message, 'success');
                        loadPermintaanData();
                    } else {
                        tampilkanNotifPermintaan(result.message || `Gagal ${action} permintaan.`, 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    tampilkanNotifPermintaan("Terjadi kesalahan jaringan atau server.", 'error');
                }
            }
        }
    });
</script>
@endpush
