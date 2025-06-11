<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>SiManis Data Barang</title>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" />
      <link rel="stylesheet" href="Katalok.css" />

</head>
<body>
  <div class="sidebar" id="sidebar">
    <div class="profile" id="profile">
      <div class="profile-img"></div>
      <div>Username</div>
    </div>
    <div class="menu">
            <a href="Home supplier copy.html" class="menu-item  "><i class="fas fa-home"></i> <span>HOME</span></a>
            <!-- <a href="data-barang.html" class="menu-item"><i class="fas fa-box"></i> <span>Data Barang</span></a> -->
            <a href="Permintaan.html" class="menu-item  "><i class="fas fa-file-alt"></i>  <span>Permintaan</span></a>
            <a href="#" class="menu-item active "><i class="fas fa-box-open"></i>  <span>Katalok</span></a>
            <a href="#" id="logout-btn" class="menu-item"><i class="fas fa-sign-out-alt"></i> <span>Log out</span></a>
    </div>
  </div>

  <div id="logout-popup">
  <div class="popup-content">
      <p>Apakah Anda yakin ingin logout?</p>
      <a href="#" class="confirm-logout" id="confirm-logout">Logout</a>
      <button class="cancel-logout" id="cancel-logout">Batal</button>
  </div>
  </div>


  <div class="main-content" id="main-content">
    <div class="header">
      <div class="brand">SiManis</div>
      <button class="toggle-btn" id="toggle-btn"><i class="fas fa-bars"></i></button>
      Riwayat
    </div>
    <h1>Katalog</h1>
      <div><button class="btn-add" id="btn-open-modal"><i class="fas fa-plus"></i> Tambah Barang</button></div>    
      <div class="dashboard-box">
      <div id="notification"></div>
      <!-- <button class="btn-add" id="btn-open-modal">Tambah Barang</button> -->
      <table id="tabel-barang">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama </th>
            <th>Gmail</th>
            <th>Rool</th>
            <th>Aksi</th>
          </tr>
        </thead>
        <tbody id="tabel-body">
        </tbody>
      </table>
    </div>
  </div>

  <!-- Modal Tambah Barang -->
<div id="modal" class="modal">
  <div class="modal-content">
    <span class="close" id="close-modal">&times;</span>
    <form id="form-tambah-barang" class="input-form" onsubmit="return false;">
       <label>Nama Barang:</label>
      <input type="text" id="nama" placeholder="Nama Penggun " required />

       <label>Jumblah:</label>
      <input type="Stok" id="Gmail" placeholder="Jumlah" required min="0" />

      <label>Pilih Satuan:</label>
      <select id="Satuan" required>
        <option value="" disabled selected>Pilih Satuan</option>
        <option value="pcs">Buah</option>
        <option value="pcs">Rin</option>
        <option value="pcs">pcs</option>
      </select>

      <label>Tanggal:</label>
      <input id="tanggal" type="date" required />


      <button type="button" class="btn-save" onclick="tambahDataUser()">Simpan</button>
    </form>
  </div>
</div>

<!-- Lanjutan script dari sebelumnya -->
<script>
let no = 1;

const modal = document.getElementById('modal');
const btnOpenModal = document.getElementById('btn-open-modal');
const btnCloseModal = document.getElementById('close-modal');
const notification = document.getElementById('notification');

// Fungsi buka modal
btnOpenModal.addEventListener('click', () => {
  modal.style.display = 'block';
});

// Fungsi tutup modal
btnCloseModal.addEventListener('click', () => {
  modal.style.display = 'none';
});

window.addEventListener('click', (e) => {
  if (e.target === modal) {
    modal.style.display = 'none';
  }
});

// Fungsi notifikasi
function tampilkanNotif(msg) {
  notification.textContent = msg;
  setTimeout(() => {
    notification.textContent = '';
  }, 2500);
}

// Fungsi tambah data ke tabel
function tambahDataUser() {
  const nama = document.getElementById('nama').value.trim();
  const Stok = document.getElementById('Stok').value;
  const Rool = document.getElementById('Satuan').value;
  const tanggal = document.getElementById('tanggal').value;

  const tbody = document.getElementById('tabel-body');

  if (!nama || !Stok || !Rool || tanggal ) {
    tampilkanNotif("Semua field harus diisi.");
    return;
  }

  const tr = document.createElement('tr');

  tr.innerHTML = `
    <td>${no++}</td>
    <td>${nama}</td>
    <td>${Stok}</td>
    <td>${Rool}</td>
    <td>${tanggal}</td>
    <td>
      <button class="btn-edit">Edit</button>
      <button class="btn-hapus" onclick="hapusData(this)">Hapus</button>
    </td>
  `;

  tbody.appendChild(tr);

  modal.style.display = 'none';
  document.getElementById('form-tambah-barang').reset();
  tampilkanNotif("Data berhasil ditambahkan!");
}

// Fungsi hapus data
function hapusData(button) {
  const row = button.parentElement.parentElement;
  row.remove();
  tampilkanNotif("Data berhasil dihapus!");
}

      // Sidebar toggle
      document.addEventListener("DOMContentLoaded", () => {
        const toggleBtn = document.getElementById("toggle-btn");
        const sidebar = document.getElementById("sidebar");
        const mainContent = document.getElementById("main-content");
        const profile = document.getElementById("profile");

        function autoCollapseSidebar() {
          if (window.innerWidth <= 768) {
            sidebar.classList.add("collapsed");
            mainContent.classList.add("expanded");
            profile.classList.add("collapsed");
          } else {
            const isCollapsed = localStorage.getItem("sidebarCollapsed") === "true";
            if (!isCollapsed) {
              sidebar.classList.remove("collapsed");
              mainContent.classList.remove("expanded");
              profile.classList.remove("collapsed");
            }
          }
        }

        autoCollapseSidebar();
        window.addEventListener("resize", autoCollapseSidebar);

        toggleBtn.addEventListener("click", () => {
          const collapsedNow = sidebar.classList.toggle("collapsed");
          mainContent.classList.toggle("expanded");
          profile.classList.toggle("collapsed");
          localStorage.setItem("sidebarCollapsed", collapsedNow);
        });
      });


// Fungsi Logout popup
document.getElementById('logout-btn').addEventListener('click', () => {
  document.getElementById('logout-popup').style.display = 'flex';
});

document.getElementById('cancel-logout').addEventListener('click', () => {
  document.getElementById('logout-popup').style.display = 'none';
});
</script>

</body>
</html>
