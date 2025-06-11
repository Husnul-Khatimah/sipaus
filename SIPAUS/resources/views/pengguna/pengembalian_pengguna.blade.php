{{-- File: resources/views/pengguna/pengambilan_pengguna.blade.php --}}

@extends('layouts.pengguna')

@section('title', 'Pengambilan Barang')
@section('header_title', 'Pengambilan Barang')

@push('styles')
    {{-- Memuat CSS spesifik untuk halaman ini. Pastikan public/css/pengambilan_pengguna.css ada. --}}
    <link rel="stylesheet" href="{{ asset('css/pengambilan_pengguna.css') }}" />
@endpush

@section('content')
    <h1>Pengambilan Barang</h1>
    <div><button class="btn-add" id="btn-open-modal"><i class="fas fa-plus"></i> Ajukan Pengambilan</button></div>
    <div class="dashboard-box">
        <div id="notification"></div>
        <table id="tabel-pengambilan">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                    <th>Tempat</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tabel-body">
                {{-- Data pengambilan akan dimuat di sini oleh JavaScript dari server --}}
            </tbody>
        </table>
    </div>

    <div id="modal-form" class="modal">
        <div class="modal-content">
            <span class="close" id="btn-close-modal">&times;</span>
            <form class="input-form" id="form-ajukan-pengambilan"> {{-- ID form diubah --}}
                @csrf {{-- PENTING: Token CSRF --}}
                <label>Nama:</label>
                <input id="nama-input" placeholder="Masukkan Nama Anda" required /> {{-- ID diubah --}}

                <label>NIP:</label>
                <input id="nip-input" placeholder="Masukkan NIP Anda" required /> {{-- ID diubah --}}

                <label>Nama Barang:</label>
                <input id="nama-barang-input" placeholder="Masukkan Nama Barang" required /> {{-- ID diubah --}}

                <label>Jumlah:</label>
                <input type="number" id="jumlah-input" placeholder="Masukkan jumlah barang" required min="0" /> {{-- ID diubah --}}

                <label>Tanggal Pengambilan:</label>
                <input id="tanggal-input" type="date" required /> {{-- ID diubah --}}

                <label>Tempat Pengambilan:</label>
                <input id="tempat-input" placeholder="Masukkan Tempat Pengambilan" required /> {{-- ID diubah --}}

                <button type="button" class="btn-save" id="save-pengambilan-btn">Ajukan</button> {{-- ID tombol diubah --}}
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>

    
    // Mendefinisikan URL dasar sebagai konstanta JavaScript dari Blade
    const API_PENGAMBILAN_URL = '{{ url('/api/pengguna/pengambilan') }}'; // Asumsi ada API untuk pengambilan
    const PENGGUNA_PENGAMBILAN_URL = '{{ url('/pengguna/pengambilan') }}'; // URL untuk POST/DELETE
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Script khusus untuk halaman Pengambilan Barang Pengguna
    const modal = document.getElementById("modal-form");
    const btnOpen = document.getElementById("btn-open-modal");
    const btnClose = document.getElementById("btn-close-modal");
    const notification = document.getElementById('notification');
    const savePengambilanBtn = document.getElementById('save-pengambilan-btn');
    const tbody = document.getElementById('tabel-body');

    function tampilkanNotif(pesan, type = 'success') {
        notification.textContent = pesan;
        notification.classList.remove('success', 'error');
        notification.classList.add('show', type);
        setTimeout(() => {
            notification.classList.remove('show');
            notification.textContent = '';
        }, 3000);
    }

    btnOpen.onclick = () => {
        modal.style.display = "flex";
        document.getElementById("form-ajukan-pengambilan").reset();
    };
    btnClose.onclick = () => {
        modal.style.display = "none";
        document.getElementById("form-ajukan-pengambilan").reset();
    };
    window.onclick = (event) => {
        if (event.target == modal) {
            modal.style.display = "none";
            document.getElementById("form-ajukan-pengambilan").reset();
        }
    };

    // Fungsi untuk memuat data Pengambilan dari server
    async function loadPengambilanData() {
        try {
            const response = await fetch(API_PENGAMBILAN_URL);
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
            }
            const data = await response.json();
            tbody.innerHTML = '';

            if (data.length > 0) {
                data.forEach((item, index) => {
                    const row = tbody.insertRow();
                    row.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${item.user ? item.user.name : 'N/A'}</td> {{-- Sesuaikan nama kolom dari DB --}}
                        <td>${item.user ? item.user.nip : 'N/A'}</td>
                        <td>${item.atk ? item.atk.nama : item.nama_barang_manual || 'N/A'}</td>
                        <td>${item.jumlah}</td>
                        <td>${item.tanggal_permintaan}</td>
                        <td>${item.tempat}</td>
                        <td>${item.status}</td>
                        <td>
                            <button class="btn-view" data-id="${item.id}">Detail</button>
                            ${item.status === 'pending' ? `<button class="btn-hapus" data-id="${item.id}">Batalkan</button>` : ''}
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="9">Belum ada pengajuan pengambilan.</td></tr>';
            }
        } catch (error) {
            console.error('Error fetching pengambilan data:', error);
            tampilkanNotif("Gagal memuat data pengambilan: " + error.message, 'error');
        }
    }

    // Event listener untuk tombol Ajukan
    savePengambilanBtn.addEventListener('click', async () => {
        const nama = document.getElementById('nama-input').value.trim();
        const nip = document.getElementById('nip-input').value.trim();
        const namaBarang = document.getElementById('nama-barang-input').value.trim();
        const jumlah = document.getElementById('jumlah-input').value.trim();
        const tanggal = document.getElementById('tanggal-input').value;
        const tempat = document.getElementById('tempat-input').value.trim();

        if (!nama || !nip || !namaBarang || !jumlah || !tanggal || !tempat) {
            tampilkanNotif("Semua field harus diisi.", 'error');
            return;
        }

        try {
            const response = await fetch(PENGGUNA_PENGAMBILAN_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({
                    nama_pengguna: nama, // Sesuaikan dengan kolom di backend jika tidak pakai user_id
                    nip_pengguna: nip,
                    nama_barang_manual: namaBarang, // Jika nama barang dikirim sebagai string
                    jumlah: jumlah,
                    tanggal_permintaan: tanggal,
                    tempat: tempat
                })
            });

            const result = await response.json();

            if (response.ok) {
                tampilkanNotif(result.message, 'success');
                modal.style.display = 'none';
                document.getElementById('form-ajukan-pengambilan').reset();
                loadPengambilanData(); // Muat ulang data
            } else {
                tampilkanNotif(result.message || "Gagal mengajukan pengambilan.", 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            tampilkanNotif("Terjadi kesalahan jaringan atau server.", 'error');
        }
    });

    // Event Delegation untuk tombol Batalkan (Hapus)
    tbody.addEventListener('click', async (e) => {
        if (e.target.classList.contains('btn-hapus')) {
            const id = e.target.dataset.id;
            if (confirm("Apakah Anda yakin ingin membatalkan pengajuan ini?")) {
                try {
                    const response = await fetch(`${PENGGUNA_PENGAMBILAN_URL}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        }
                    });

                    const result = await response.json();

                    if (response.ok) {
                        tampilkanNotif(result.message, 'success');
                        loadPengambilanData(); // Muat ulang data
                    } else {
                        tampilkanNotif(result.message || "Gagal membatalkan pengajuan.", 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    tampilkanNotif("Terjadi kesalahan jaringan atau server.", 'error');
                }
            }
        }
    });

    document.addEventListener('DOMContentLoaded', loadPengambilanData);
</script>
@endpush
