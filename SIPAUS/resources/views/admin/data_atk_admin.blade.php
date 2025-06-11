{{-- File: resources/views/admin/data_atk_admin.blade.php --}}

@extends('layouts.admin')

@section('title', 'Data Barang')
@section('header_title', 'Data ATK')

@push('styles')
    <link rel="stylesheet" href="{{ asset('css/data_atk_admin.css') }}" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
@endpush

@section('content')
    <h1>Data ATK</h1>
    <div><button class="btn-add" id="btn-open-modal"><i class="fas fa-plus"></i> Tambah Barang</button></div>
    <div class="dashboard-box">
        <div id="notification"></div>
        <table id="tabel-barang">
            <thead>
                <tr>
                    <th>No</th>
                    <th>Nama Barang</th>
                    <th>Stok</th>
                    <th>Satuan</th>
                    <th>Kategori</th>
                    <th>Aksi</th>
                </tr>
            </thead>
            <tbody id="tabel-body">
            </tbody>
        </table>
    </div>

    <div id="modal" class="modal">
        <div class="modal-content">
            <span class="close" id="close-modal">&times;</span>
            <form id="form-tambah-barang" class="input-form">
                @csrf
                <label>Pilih Nama Barang:</label>
                <select id="NamaBarangSelect" name="atk_id" class="form-control" required>
                    <option value="" disabled selected>Pilih Barang</option>
                    {{-- Opsi akan dimuat di sini oleh JavaScript --}}
                </select>

                <label>Jumlah:</label>
                <input type="number" id="Stok" name="stok" placeholder="Stok" required min="0" />

                <label>Pilih Satuan:</label>
                <select id="Satuan" name="satuan" required>
                    <option value="" disabled selected>Pilih Satuan</option>
                    <option value="pcs">pcs</option>
                    <option value="rim">rim</option>
                    <option value="box">box</option>
                </select>

                <label>Pilih Kategori:</label>
                <select id="Kategori" name="jenis_atk_id" class="form-control" required>
                    <option value="" disabled selected>Pilih Kategori</option>
                    {{-- Opsi akan dimuat di sini oleh JavaScript --}}
                </select>
                <button type="button" class="btn-save" id="save-item-btn">Simpan</button>
            </form>
        </div>
    </div>
@endsection

@push('scripts')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/select2@4.10-rc.0/dist/js/select2.min.js"></script>

<script>
    const API_DATA_ATK_URL = '{{ url('/api/admin/data-atk') }}';
    const ADMIN_DATA_ATK_URL = '{{ url('/admin/data-atk') }}';
    const API_JENIS_ATK_URL = '{{ url('/api/admin/jenis-atk') }}';
    const API_ATK_ITEMS_DROPDOWN_URL = '{{ url('/api/admin/atk-items-for-dropdown') }}'; // URL untuk daftar barang
    const CSRF_TOKEN = document.querySelector('meta[name="csrf-token"]').getAttribute('content');

    const modal = document.getElementById('modal');
    const btnOpenModal = document.getElementById('btn-open-modal');
    const btnCloseModal = document.getElementById('close-modal');
    const notification = document.getElementById('notification');
    const saveItemBtn = document.getElementById('save-item-btn');
    const tbody = document.getElementById('tabel-body');
    const selectKategori = $('#Kategori');
    const selectNamaBarang = $('#NamaBarangSelect'); // Pastikan ini terdefinisi

    // Fungsi notifikasi
    function tampilkanNotif(msg, type = 'success') {
        notification.textContent = msg;
        notification.classList.remove('success', 'error');
        notification.classList.add('show', type);
        setTimeout(() => {
            notification.classList.remove('show');
            notification.textContent = '';
        }, 2500);
    }

    // Fungsi untuk memuat data kategori dari API dan menginisialisasi Select2
    async function loadKategoriOptions() {
        try {
            const response = await fetch(API_JENIS_ATK_URL);
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
            }
            const data = await response.json();

            selectKategori.empty();
            selectKategori.append('<option value="" disabled selected>Pilih Kategori</option>');

            data.forEach(item => {
                selectKategori.append(new Option(item.nama_kategori, item.id_jenis_atk));
            });

            selectKategori.select2({
                placeholder: "Pilih Kategori",
                allowClear: true,
                width: '100%'
            });

        } catch (error) {
            console.error('Error loading Kategori options:', error);
            tampilkanNotif("Gagal memuat opsi kategori: " + error.message, 'error');
        }
    }

    // Fungsi untuk memuat daftar barang yang sudah ada (dari tabel atk)
    async function loadAtkItemsForDropdown() {
        try {
            const response = await fetch(API_ATK_ITEMS_DROPDOWN_URL);
            if (!response.ok) {
                const errorText = await response.text();
                throw new Error(`HTTP error! status: ${response.status} - ${errorText}`);
            }
            const data = await response.json();

            selectNamaBarang.empty();
            selectNamaBarang.append('<option value="" disabled selected>Pilih Barang</option>');

            data.forEach(item => {
                // Pastikan item.id_atk dan item.nama_barang sesuai dengan respons API
                selectNamaBarang.append(new Option(item.nama_barang, item.id_atk));
            });

            selectNamaBarang.select2({
                placeholder: "Pilih Barang",
                allowClear: true,
                width: '100%'
            });

        } catch (error) {
            console.error('Error loading ATK items for dropdown:', error);
            tampilkanNotif("Gagal memuat daftar barang: " + error.message, 'error');
        }
    }

    // Buka Modal
    btnOpenModal.addEventListener('click', () => {
        modal.style.display = 'flex';
        document.getElementById('form-tambah-barang').reset();
        selectKategori.val(null).trigger('change');
        selectNamaBarang.val(null).trigger('change'); // Reset Nama Barang Select2
        loadKategoriOptions(); // Muat opsi kategori
        loadAtkItemsForDropdown(); // Muat opsi nama barang
    });

    // Tutup Modal
    btnCloseModal.addEventListener('click', () => {
        modal.style.display = 'none';
        document.getElementById('form-tambah-barang').reset();
        selectKategori.val(null).trigger('change');
        selectNamaBarang.val(null).trigger('change');
    });

    window.addEventListener('click', (e) => {
        if (e.target === modal) {
            modal.style.display = 'none';
            document.getElementById('form-tambah-barang').reset();
            selectKategori.val(null).trigger('change');
            selectNamaBarang.val(null).trigger('change');
        }
    });

    async function loadAtkData() {
        try {
            const response = await fetch(API_DATA_ATK_URL);
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
                        <td>${item.nama_barang}</td>
                        <td>${item.stok}</td>
                        <td>${item.satuan}</td>
                        <td>${item.kategori}</td>
                        <td>
                            <button class="btn-edit" data-id="${item.id}">Edit</button>
                            <button class="btn-hapus" data-id="${item.id}">Hapus</button>
                        </td>
                    `;
                    tbody.appendChild(tr);
                });
            } else {
                tbody.innerHTML = '<tr><td colspan="6">Belum ada data barang.</td></tr>';
            }
        } catch (error) {
            console.error('Error fetching data:', error);
            tampilkanNotif("Gagal memuat data barang: " + error.message, 'error');
        }
    }

    saveItemBtn.addEventListener('click', async () => {
        const atkId = selectNamaBarang.val(); // Ambil ID barang yang dipilih
        const stok = document.getElementById('Stok').value.trim();
        const satuan = document.getElementById('Satuan').value; // Ambil dari input Satuan
        const jenisAtkId = selectKategori.val(); // Ambil ID kategori dari Select2

        // Validasi semua field
        if (!atkId || !stok || !satuan || !jenisAtkId) {
            tampilkanNotif("Semua field harus diisi.", 'error');
            return;
        }

        try {
            const response = await fetch(ADMIN_DATA_ATK_URL, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': CSRF_TOKEN
                },
                body: JSON.stringify({
                    atk_id: atkId,
                    stok: parseInt(stok),
                    satuan: satuan,
                    jenis_atk_id: jenisAtkId // Kirim jenis_atk_id juga
                })
            });

            const result = await response.json();

            if (response.ok) {
                tampilkanNotif(result.message, 'success');
                modal.style.display = 'none';
                document.getElementById('form-tambah-barang').reset();
                selectKategori.val(null).trigger('change');
                selectNamaBarang.val(null).trigger('change');
                loadAtkData();
            } else {
                tampilkanNotif(result.message || "Gagal menambahkan data.", 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            tampilkanNotif("Terjadi kesalahan jaringan atau server.", 'error');
        }
    });

    tbody.addEventListener('click', async (e) => {
        if (e.target.classList.contains('btn-hapus')) {
            const id = e.target.dataset.id;

            if (confirm("Apakah Anda yakin ingin menghapus data ini?")) {
                try {
                    const response = await fetch(`${ADMIN_DATA_ATK_URL}/${id}`, {
                        method: 'DELETE',
                        headers: {
                            'X-CSRF-TOKEN': CSRF_TOKEN
                        }
                    });

                    const result = await response.json();

                    if (response.ok) {
                        tampilkanNotif(result.message, 'success');
                        loadAtkData();
                    } else {
                        tampilkanNotif(result.message || "Gagal menghapus data.", 'error');
                    }
                } catch (error) {
                    console.error('Error:', error);
                    tampilkanNotif("Terjadi kesalahan jaringan atau server.", 'error');
                }
            }
        }
    });

    document.addEventListener('DOMContentLoaded', loadAtkData);
    document.addEventListener('DOMContentLoaded', loadKategoriOptions);
    document.addEventListener('DOMContentLoaded', loadAtkItemsForDropdown); // Panggil ini untuk mengisi dropdown saat halaman pertama kali dimuat

</script>
@endpush
