{{-- File: resources/views/admin/pesan_atk_admin.blade.php --}}

@extends('layouts.admin')

@section('title', 'Pesan ATK ke Supplier')
@section('header_title', 'Pesan ATK')

@push('styles')
    {{-- Memuat CSS spesifik untuk halaman ini. Pastikan public/css/pesan_admin.css ada. --}}
    <link rel="stylesheet" href="{{ asset('css/admin/home_admin.css') }}" />
@endpush

@section('content')
    <h1>Pesan ATK</h1>
    <div><button class="btn-add" id="btn-open-modal"><i class="fas fa-plus"></i> Tambah Pesanan</button></div>
    <div class="dashboard-box">
        <div id="notification"></div>
        <table id="tabel-pesanan"> {{-- ID tabel diubah --}}
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Supplier</th>
                    <th>Nama Barang</th>
                    <th>Jumlah Pesan</th> {{-- Kolom diubah untuk konsistensi dengan model --}}
                    <th>Tanggal Pesan</th> {{-- Kolom diubah untuk konsistensi dengan model --}}
                    <th>Status</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tabel-body">
                {{-- Data pesanan akan dimuat di sini oleh JavaScript dari server --}}
            </tbody>
        </table>
    </div>

    <div id="modal-form" class="modal">
        <div class="modal-content">
            <span class="close" id="btn-close-modal">&times;</span>
            <form class="input-form" id="form-tambah-pesanan"> {{-- ID form diubah --}}
                @csrf {{-- PENTING: Token CSRF --}}
                <label>Nama Supplier:</label>
                <input id="nama-supplier-input" placeholder="Masukkan Nama Supplier" required /> {{-- ID diubah --}}

                <label>Nama Barang:</label>
                <input id="nama-barang-input" placeholder="Masukkan Nama Barang" required /> {{-- ID diubah --}}

                <label>Jumlah Pesan:</label> {{-- Label diubah --}}
                <input type="number" id="jumlah-pesan-input" placeholder="Masukkan jumlah pesan" required min="0" /> {{-- ID diubah --}}

                <label>Tanggal Pesan:</label> {{-- Label diubah --}}
                <input id="tanggal-pesan-input" type="date" required /> {{-- ID diubah --}}

                <button type="button" class="btn-save" id="save-pesanan-btn">Simpan</button> {{-- ID tombol diubah --}}
            </form>
        </div>
    </div>
@endsection

@push('scripts')
<script>
    // Mendefinisikan URL dasar sebagai konstanta JavaScript dari Blade
    const API_PESAN_ATK_URL = '{{ url('/api/admin/pesan-atk') }}';
    const ADMIN_PESAN_ATK_URL = '{{ url('/admin/pesan-atk') }}';
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    // Script khusus untuk halaman Pesan ATK
    const modal = document.getElementById("modal-form");
    const btnOpen = document.getElementById("btn-open-modal");
    const btnClose = document.getElementById("btn-close-modal");
    const notification = document.getElementById('notification');
    const savePesananBtn = document.getElementById('save-pesanan-btn');
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
        modal.style.display = "flex"; // Menggunakan flex untuk pemusatan CSS modal
        document.getElementById("form-tambah-pesanan").reset();
    };
    btnClose.onclick = () => {
        modal.style.display = "none";
        document.getElementById("form-tambah-pesanan").reset();
    };
    window.onclick = (event) => {
        if (event.target == modal) {
            modal.style.display = "none";
            document.getElementById("form-tambah-pesanan").reset();
        }
    };

    // Fungsi untuk memuat data pesanan dari server
    async function loadPesananData() {
        try {
            const response = await fetch(API_PESAN_ATK_URL); // Menggunakan konstanta URL
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
                        <td>${item.supplier ? item.supplier.name : item.nama_supplier}</td> {{-- Sesuaikan nama kolom dari DB --}}
                        <td>${item.atk ? item.atk.nama : item.nama_barang}</td>
                        <td>${item.jumlah_pesan}</td>
                        <td>${item.tanggal_pesan}</td>
                        <td>${item.status}</td>
                        <td>
                            <button class="btn-edit" data-id="${item.id}">Edit</button>
                            <button class="btn-hapus" data-id="${item.id}">Hapus</button>
                        </td>
                    `;
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="7">Belum ada data pesanan.</td></tr>';
            }
        } catch (error) {
            console.error('Error fetching pesanan data:', error);
            tampilkanNotif("Gagal memuat data pesanan: " + error.message, 'error');
        }
    }

    // Event listener untuk tombol Simpan (Tambah Pesanan)
    savePesananBtn.addEventListener('click', async () => {
        const namaSupplier = document.getElementById('nama-supplier-input').value.trim();
        const namaBarang = document.getElementById('nama-barang-input').value.trim();
        const jumlahPesan = document.getElementById('jumlah-pesan-input').value.trim(); // ID diubah
        const tanggalPesan = document.getElementById('tanggal-pesan-input').value; // ID diubah

        if (!namaSupplier || !namaBarang || !jumlahPesan || !tanggalPesan) {
            tampilkanNotif("Semua field harus diisi.", 'error');
            return;
        }

        try {
            const response = await fetch(ADMIN_PESAN_ATK_URL, { // Menggunakan konstanta URL
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN // Menggunakan konstanta CSRF token
                },
                body: JSON.stringify({
                    nama_supplier: namaSupplier, // Jika tidak ada relasi langsung ke ID supplier
                    nama_barang: namaBarang,   // Jika tidak ada relasi langsung ke ID barang
                    jumlah_pesan: jumlahPesan,
                    tanggal_pesan: tanggalPesan
                })
            });

            const result = await response.json();

            if (response.ok) {
                tampilkanNotif(result.message, 'success');
                modal.style.display = 'none';
                document.getElementById('form-tambah-pesanan').reset();
                loadPesananData(); // Muat ulang data setelah penambahan
            } else {
                tampilkanNotif(result.message || "Gagal menambahkan pesanan.", 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            tampilkanNotif("Terjadi kesalahan jaringan atau server.", 'error');
        }
    });

    // Event Delegation untuk tombol Hapus (dan Edit)
    tbody.addEventListener('click', async (e) => {
        if (e.target.classList.contains('btn-hapus')) {
            const id = e.target.dataset.id;
            if (confirm("Apakah Anda yakin ingin menghapus pesanan ini?")) {
                try {
                    const response = await fetch(`${ADMIN_PESAN_ATK_URL}/${id}`, { // Menggunakan konstanta URL
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': CSRF_TOKEN // Menggunakan konstanta CSRF token
                        }
                    });

                    const result = await response.json();

                    if (response.ok) {
                        tampilkanNotif(result.message, 'success');
                        loadPesananData(); // Muat ulang data setelah penghapusan
                    } else {
                        tampilkanNotif(result.message || "Gagal menghapus pesanan.", 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    tampilkanNotif("Terjadi kesalahan jaringan atau server.", 'error');
                }
            }
        }
    });

    document.addEventListener('DOMContentLoaded', loadPesananData);
</script>
@endpush
