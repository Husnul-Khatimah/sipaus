{{-- File: resources/views/admin/pengguna_admin.blade.php --}}

@extends('layouts.admin')

@section('title', 'Manajemen Pengguna')
@section('header_title', 'Daftar Pengguna')

@push('styles')
    {{-- Memuat CSS spesifik untuk halaman ini. Pastikan public/css/pengguna_admin.css ada. --}}
    <link rel="stylesheet" href="{{ asset('css/admin/home_admin.css') }}" />
@endpush

@section('content')
    <h1>Pengguna</h1>
    <div><button class="btn-add" id="btn-open-modal"><i class="fas fa-plus"></i> Tambah Pengguna</button></div>
    <div class="dashboard-box">
        <div id="notification"></div>
        <table id="tabel-pengguna"> {{-- ID tabel diubah menjadi tabel-pengguna untuk konsistensi --}}
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama</th>
                    <th>Gmail</th>
                    <th>Role</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tabel-body">
                {{-- Data pengguna akan dimuat di sini oleh JavaScript dari server --}}
            </tbody>
        </table>
    </div>

    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" id="close-modal">&times;</span>
            <form id="form-tambah-pengguna" class="input-form"> {{-- ID form diubah --}}
                @csrf {{-- PENTING: Token CSRF --}}
                <label>Nama Pengguna:</label>
                <input type="text" id="nama" placeholder="Nama Pengguna" required />

                <label>Gmail:</label>
                <input type="email" id="Gmail" placeholder="Gmail" required /> {{-- Type email untuk validasi dasar --}}

                <label>Password:</label> {{-- Tambahkan input password --}}
                <input type="password" id="password" placeholder="Password" required />

                <label>Pilih Role:</label>
                <select id="Rool" required>
                    <option value="" disabled selected>Pilih Role</option>
                    <option value="Admin">Admin</option>
                    <option value="Pengguna">Pengguna</option>
                    <option value="Supplier">Supplier</option>
                </select>
                <button type="button" class="btn-save" id="save-pengguna-btn">Simpan</button> {{-- ID tombol diubah --}}
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Mendefinisikan URL dasar sebagai konstanta JavaScript dari Blade
    const API_PENGGUNA_URL = '{{ url('/api/admin/pengguna') }}';
    const ADMIN_PENGGUNA_URL = '{{ url('/admin/pengguna') }}';
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Script khusus untuk halaman Pengguna
    const modal = document.getElementById('modal');
    const btnOpenModal = document.getElementById('btn-open-modal');
    const btnCloseModal = document.getElementById('close-modal');
    const notification = document.getElementById('notification');
    const savePenggunaBtn = document.getElementById('save-pengguna-btn');
    const tbody = document.getElementById('tabel-body');

    function tampilkanNotif(msg, type = 'success') {
        notification.textContent = msg;
        notification.classList.remove('success', 'error');
        notification.classList.add('show', type);
        setTimeout(() => {
            notification.classList.remove('show');
            notification.textContent = '';
        }, 2500);
    }

    btnOpenModal.addEventListener('click', () => {
        modal.style.display = 'flex'; // Menggunakan flex untuk pemusatan CSS modal
        document.getElementById('form-tambah-pengguna').reset();
    });

    btnCloseModal.addEventListener('click', () => {
        modal.style.display = 'none';
        document.getElementById('form-tambah-pengguna').reset();
    });

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
            document.getElementById('form-tambah-pengguna').reset();
        }
    });

    // Fungsi untuk memuat data Pengguna dari server
    async function loadUserData() {
        try {
            const response = await fetch(API_PENGGUNA_URL); // Menggunakan konstanta URL
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
            }
            const data = await response.json();
            tbody.innerHTML = '';

            if (data.length > 0) {
                data.forEach((item, index) => {
                    const tr = document.createElement('tr');
                    tr.innerHTML = `
                        <td>${index + 1}</td>
                        <td>${item.name}</td>
                        <td>${item.email}</td>
                        <td>${item.role}</td>
                        <td>
                            <button class="btn-edit" data-id="${item.id}">Edit</button>
                            <button class="btn-hapus" data-id="${item.id}">Hapus</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="5">Belum ada data pengguna.</td></tr>';
            }
        } catch (error) {
            console.error('Error fetching user data:', error);
            tampilkanNotif("Gagal memuat data pengguna: " + error.message, 'error');
        }
    }

    // Event listener untuk tombol Simpan (Tambah Pengguna)
    savePenggunaBtn.addEventListener('click', async () => {
        const nama = document.getElementById('nama').value.trim();
        const gmail = document.getElementById('Gmail').value.trim();
        const password = document.getElementById('password').value.trim(); // Ambil password
        const role = document.getElementById('Rool').value;

        if (!nama || !gmail || !password || !role) { // Validasi password
            tampilkanNotif("Semua field harus diisi.", 'error');
            return;
        }

        try {
            const response = await fetch(ADMIN_PENGGUNA_URL, { // Menggunakan konstanta URL
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN // Menggunakan konstanta CSRF token
                },
                body: JSON.stringify({
                    nama: nama,
                    gmail: gmail,
                    password: password, // Kirim password ke backend
                    role: role
                })
            });

            const result = await response.json();

            if (response.ok) {
                tampilkanNotif(result.message, 'success');
                modal.style.display = 'none';
                document.getElementById('form-tambah-pengguna').reset();
                loadUserData(); // Muat ulang data setelah penambahan
            } else {
                tampilkanNotif(result.message || "Gagal menambahkan pengguna.", 'error');
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
            if (confirm("Apakah Anda yakin ingin menghapus pengguna ini?")) {
                try {
                    const response = await fetch(`${ADMIN_PENGGUNA_URL}/${id}`, { // Menggunakan konstanta URL
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': CSRF_TOKEN // Menggunakan konstanta CSRF token
                        }
                    });

                    const result = await response.json();

                    if (response.ok) {
                        tampilkanNotif(result.message, 'success');
                        loadUserData(); // Muat ulang data setelah penghapusan
                    } else {
                        tampilkanNotif(result.message || "Gagal menghapus pengguna.", 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    tampilkanNotif("Terjadi kesalahan jaringan atau server.", 'error');
                }
            }
        }
    });

    // Panggil fungsi untuk memuat data saat halaman dimuat
    document.addEventListener('DOMContentLoaded', loadUserData);
</script>
@endpush
