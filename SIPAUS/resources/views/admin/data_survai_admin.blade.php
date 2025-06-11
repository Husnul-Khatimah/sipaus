{{-- File: resources/views/admin/data_survei_admin.blade.php --}}

@extends('layouts.admin') {{-- Menggunakan master layout admin --}}

@section('title', 'Data Survei') {{-- Judul halaman untuk browser tab --}}
@section('header_title', 'Data Survei Pengguna') {{-- Judul yang muncul di header konten --}}

@push('styles')
    {{-- Memuat CSS spesifik untuk halaman ini. Pastikan public/css/data_survei_admin.css ada. --}}
    <link rel="stylesheet" href="{{ asset('css/admin/home_admin.css') }}" />
@endpush

@section('content')
    {{-- Konten unik dari halaman Data Survei --}}
    <h1>Data Survei</h1>

    <div class="dashboard-box">
        <div id="notification-survei"></div> {{-- ID notifikasi khusus untuk survei --}}
        <table id="tabel-survei">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Nama Barang</th> {{-- Asumsi ini masih relevan untuk survei --}}
                    <th>Jumlah</th> {{-- Asumsi ini masih relevan untuk survei --}}
                    <th>Tanggal </th>
                    <th>Keterangan </th>
                    <th>Status </th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tabel-survei-body">
                {{-- Data survei akan dimuat dari database secara dinamis di sini --}}
            </tbody>
        </table>
    </div>
@endsection

@push('scripts')
<script>
    // Mendefinisikan URL dasar sebagai konstanta JavaScript dari Blade
    const API_SURVEI_URL = '{{ url('/api/admin/data-survai') }}';
    const ADMIN_SURVEI_BASE_URL = '{{ url('/admin/data-survai') }}';
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Script khusus untuk halaman Data Survei
    const notificationSurvei = document.getElementById('notification-survei');
    const tbodySurvei = document.getElementById('tabel-survei-body');

    // Fungsi notifikasi untuk survei
    function tampilkanNotifSurvei(msg, type = 'success') {
        notificationSurvei.textContent = msg;
        notificationSurvei.classList.remove('success', 'error');
        notificationSurvei.classList.add('show', type);
        setTimeout(() => {
            notificationSurvei.classList.remove('show');
            notificationSurvei.textContent = '';
        }, 2500);
    }

    // Fungsi untuk memuat data survei dari server
    async function loadSurveyData() {
        try {
            const response = await fetch(API_SURVEI_URL); // Menggunakan konstanta URL
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
            }
            const data = await response.json();
            tbodySurvei.innerHTML = ''; // Clear existing rows

            if (data.length > 0) {
                data.forEach((item, index) => {
                    const row = tbodySurvei.insertRow();
                    row.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${item.user ? item.user.name : 'N/A'}</td> {{-- Ambil nama dari relasi user --}}
                        <td>${item.user ? item.user.nip : 'N/A'}</td> {{-- Asumsi NIP ada di model User --}}
                        <td>${item.nama_barang || 'N/A'}</td> {{-- Jika survei terkait barang --}}
                        <td>${item.jumlah || 'N/A'}</td> {{-- Jika survei terkait jumlah --}}
                        <td>${item.tanggal_survei}</td>
                        <td>${item.feedback}</td>
                        <td>${item.status || 'N/A'}</td> {{-- Asumsi ada status survei --}}
                        <td>
                            <button class="btn-view" data-id="${item.id}">Setujui</button>
                            <button class="btn-view1" data-id="${item.id}">Tolak</button>
                        </td>
                    `;
                });
            } else {
                tbodySurvei.innerHTML = '<tr><td colspan="9">Belum ada data survei.</td></tr>';
            }
        } catch (error) {
            console.error('Error loading survey data:', error);
            tampilkanNotifSurvei("Gagal memuat data survei: " + error.message, 'error');
        }
    }

    // Event Delegation untuk tombol Setujui/Tolak (jika ada aksi)
    tbodySurvei.addEventListener('click', async (e) => {
        if (e.target.classList.contains('btn-view') || e.target.classList.contains('btn-view1')) {
            const id = e.target.dataset.id;
            const action = e.target.classList.contains('btn-view') ? 'setujui' : 'tolak';
            const confirmationMsg = `Apakah Anda yakin ingin ${action} permintaan survei ini?`;

            if (confirm(confirmationMsg)) {
                try {
                    const response = await fetch(`${ADMIN_SURVEI_BASE_URL}/${id}/${action}`, {
                        method: 'POST', // Atau PUT jika mengubah status
                        headers: {
                            'X-CSRF-TOKEN': CSRF_TOKEN, // Menggunakan konstanta CSRF token
                            'Content-Type': 'application/json'
                        },
                        body: JSON.stringify({ status: action === 'setujui' ? 'Disetujui' : 'Ditolak' })
                    });
                    const result = await response.json();
                    if (response.ok) {
                        tampilkanNotifSurvei(result.message, 'success');
                        loadSurveyData();
                    } else {
                        tampilkanNotifSurvei(result.message || `Gagal ${action} permintaan.`, 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    tampilkanNotifSurvei("Terjadi kesalahan jaringan atau server.", 'error');
                }
            }
        }
    });

    // Panggil fungsi saat halaman dimuat
    document.addEventListener('DOMContentLoaded', loadSurveyData);
</script>
@endpush
