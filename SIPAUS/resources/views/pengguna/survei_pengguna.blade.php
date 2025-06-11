{{-- File: resources/views/pengguna/survei_pengguna.blade.php --}}

@extends('layouts.pengguna')

@section('title', 'Isi Survei')
@section('header_title', 'Isi Survei')

@push('styles')
    {{-- Memuat CSS spesifik untuk halaman ini. Pastikan public/css/survei_pengguna.css ada. --}}
    <link rel="stylesheet" href="{{ asset('css/survei_pengguna.css') }}" />
@endpush

@section('content')
    <h1>Isi Survei</h1>
    <div><button class="btn-add" id="btn-open-modal"><i class="fas fa-plus"></i> Isi Survei Baru</button></div>
    <div class="dashboard-box">
        <div id="notification"></div>
        <table id="tabel-survei">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>NIP</th>
                    <th>Nama Barang</th>
                    <th>Jumlah</th>
                    <th>Tanggal</th>
                    <th>Keterangan</th>
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tabel-body">
                {{-- Data survei akan dimuat di sini oleh JavaScript dari server --}}
            </tbody>
        </table>
    </div>

    <div id="modal-form" class="modal">
        <div class="modal-content">
            <span class="close" id="btn-close-modal">&times;</span>
            <form class="input-form" id="form-isi-survei"> {{-- ID form diubah --}}
                @csrf {{-- PENTING: Token CSRF --}}
                <label>Nama:</label>
                <input id="nama-input" placeholder="Masukkan Nama Anda" required />

                <label>NIP:</label>
                <input id="nip-input" placeholder="Masukkan NIP Anda" required />

                <label>Nama Barang:</label>
                <input id="nama-barang-input" placeholder="Masukkan Nama Barang yang diambil" required />

                <label>Jumlah:</label>
                <input type="number" id="jumlah-input" placeholder="Masukkan jumlah barang" required min="0" />

                <label>Tanggal Survei:</label>
                <input id="tanggal-input" type="date" required />

                <label>Keterangan/Feedback:</label>
                <textarea id="keterangan-input" placeholder="Berikan feedback Anda" rows="3" class="keterangan-textarea"></textarea>

                <button type="button" class="btn-save" id="save-survei-btn">Simpan Survei</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Mendefinisikan URL dasar sebagai konstanta JavaScript dari Blade
    const API_SURVEI_PENGGUNA_URL = '{{ url('/api/pengguna/survei') }}'; // Asumsi ada API untuk survei pengguna
    const PENGGUNA_SURVEI_URL = '{{ url('/pengguna/survei') }}'; // URL untuk POST/DELETE
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Script khusus untuk halaman Survei Pengguna
    const modal = document.getElementById("modal-form");
    const btnOpen = document.getElementById("btn-open-modal");
    const btnClose = document.getElementById("btn-close-modal");
    const notification = document.getElementById('notification');
    const saveSurveiBtn = document.getElementById('save-survei-btn');
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
        document.getElementById("form-isi-survei").reset();
    };
    btnClose.onclick = () => {
        modal.style.display = "none";
        document.getElementById("form-isi-survei").reset();
    };
    window.onclick = (event) => {
        if (event.target == modal) {
            modal.style.display = "none";
            document.getElementById("form-isi-survei").reset();
        }
    };

    // Fungsi untuk memuat data Survei dari server
    async function loadSurveiData() {
        try {
            const response = await fetch(API_SURVEI_PENGGUNA_URL);
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
                        <td>${item.user ? item.user.name : 'N/A'}</td>
                        <td>${item.user ? item.user.nip : 'N/A'}</td>
                        <td>${item.nama_barang_terkait || 'N/A'}</td> {{-- Asumsi ada kolom ini di DB Survei --}}
                        <td>${item.jumlah_terkait || 'N/A'}</td>
                        <td>${item.tanggal_survei}</td>
                        <td>${item.feedback}</td>
                        <td>${item.status || 'N/A'}</td>
                        <td>
                            <button class="btn-edit" data-id="${item.id}">Edit</button>
                            <button class="btn-hapus" data-id="${item.id}">Hapus</button>
                        </td>
                    `;
                    tbody.appendChild(row);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="9">Belum ada data survei yang diisi.</td></tr>';
            }
        } catch (error) {
            console.error('Error fetching survei data:', error);
            tampilkanNotif("Gagal memuat data survei: " + error.message, 'error');
        }
    }

    // Event listener untuk tombol Simpan Survei
    saveSurveiBtn.addEventListener('click', async () => {
        const nama = document.getElementById('nama-input').value.trim();
        const nip = document.getElementById('nip-input').value.trim();
        const namaBarang = document.getElementById('nama-barang-input').value.trim();
        const jumlah = document.getElementById('jumlah-input').value.trim();
        const tanggal = document.getElementById('tanggal-input').value;
        const keterangan = document.getElementById('keterangan-input').value.trim();

        if (!nama || !nip || !namaBarang || !jumlah || !tanggal || !keterangan) {
            tampilkanNotif("Semua field harus diisi.", 'error');
            return;
        }

        try {
            const response = await fetch(PENGGUNA_SURVEI_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({
                    nama_pengguna: nama, // Jika tidak ada relasi langsung ke user_id
                    nip_pengguna: nip,
                    nama_barang_terkait: namaBarang, // Jika nama barang dikirim sebagai string
                    jumlah_terkait: jumlah,
                    tanggal_survei: tanggal,
                    feedback: keterangan
                })
            });

            const result = await response.json();

            if (response.ok) {
                tampilkanNotif(result.message, 'success');
                modal.style.display = 'none';
                document.getElementById('form-isi-survei').reset();
                loadSurveiData(); // Muat ulang data
            } else {
                tampilkanNotif(result.message || "Gagal mengisi survei.", 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            tampilkanNotif("Terjadi kesalahan jaringan atau server.", 'error');
        }
    });

    // Event Delegation untuk tombol Hapus
    tbody.addEventListener('click', async (e) => {
        if (e.target.classList.contains('btn-hapus')) {
            const id = e.target.dataset.id;
            if (confirm("Apakah Anda yakin ingin menghapus survei ini?")) {
                try {
                    const response = await fetch(`${PENGGUNA_SURVEI_URL}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        }
                    });

                    const result = await response.json();

                    if (response.ok) {
                        tampilkanNotif(result.message, 'success');
                        loadSurveiData(); // Muat ulang data
                    } else {
                        tampilkanNotif(result.message || "Gagal menghapus survei.", 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    tampilkanNotif("Terjadi kesalahan jaringan atau server.", 'error');
                }
            }
        }
    });

    document.addEventListener('DOMContentLoaded', loadSurveiData);
</script>
@endpush
