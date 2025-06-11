<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SiManis</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="HOme.css" />
</head>
<body>
    <div class="sidebar" id="sidebar">
        <div class="profile" id="profile">
            <div class="profile-img"></div>
            <div>Username</div>
        </div>
        <div class="menu">
            <a href="#" class="menu-item active "><i class="fas fa-home"></i> <span>HOME</span></a>
            <!-- <a href="data-barang.html" class="menu-item"><i class="fas fa-box"></i> <span>Data Barang</span></a> -->
            <a href="Permintaan.html" class="menu-item "><i class="fas fa-file-alt"></i>  <span>Permintaan</span></a>
            <a href="Katalok.html" class="menu-item "><i class="fas fa-box-open"></i>  <span>Katalok</span></a>
            <a href="#" id="logout-btn" class="menu-item"><i class="fas  fa-sign-out-alt"></i> <span>Log out</span></a>
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
            HOME
        </div>  
          <div class="content">
            <div class="dashboard">
                <div class="stats">
                    <div class="stat-card">
                        <div class="stat-label">Total Barang</div>
                        <div class="stat-value">10</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Permintaan</div>
                        <div class="stat-value">10</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-label">Disetujui</div>
                        <div class="stat-value">10</div>
                    </div>
                </div>
                
            <!-- <div class="stats">
                    <div class="stat-card">
                        <div class="stat-value">10</div>
                        <div class="stat-label">Total Barang</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">10</div>
                        <div class="stat-label">Total Permintaan</div>
                    </div>
                    <div class="stat-card">
                        <div class="stat-value">10</div>
                        <div class="stat-label">Total Disetujui</div>
                    </div>
                </div> -->
            </div>
        </div>
     <div class="dashboard-box">
                <h2>Riwayat Pengambilan</h2>
                <table>
                    <tr>
                        <th>No</th>
                        <th>Nama Barang</th>
                        <th>Jumlah</th>
                        

                    </tr>
                    <tr>
                        <td>1</td>
                        <td>Kertas A4</td>
                        <td>2 Rim</td>
                    </tr>
                    <tr>
                        <td>2</td>
                        <td>Spidol</td>
                        <td>5 Buah</td>
                    </tr>

                </table>
            
        </main>
    </div>
    <script>
    document.addEventListener("DOMContentLoaded", () => {
        const toggleBtn = document.getElementById("toggle-btn");
        const sidebar = document.getElementById("sidebar");
        const mainContent = document.getElementById("main-content");
        const profile = document.getElementById("profile");

        // ✅ MODIFIKASI: Fungsi untuk collapse otomatis saat layar kecil
        function autoCollapseSidebar() {
            if (window.innerWidth <= 768) {
                sidebar.classList.add("collapsed");
                mainContent.classList.add("expanded");
                profile.classList.add("collapsed");
                mainContent.classList.add("expanded");
            } else {
                const isCollapsed = localStorage.getItem("sidebarCollapsed") === "true";
                if (!isCollapsed) {
                    sidebar.classList.remove("collapsed");
                    mainContent.classList.remove("expanded");
                    profile.classList.remove("collapsed");
                    
                }
            }
        }

        // ✅ MODIFIKASI: Jalankan saat awal dan saat resize
        autoCollapseSidebar();
        window.addEventListener("resize", autoCollapseSidebar);

        // Cek localStorage (default behavior jika bukan layar kecil)
        const isCollapsed = localStorage.getItem("sidebarCollapsed") === "true";
        if (isCollapsed && window.innerWidth > 768) {
            sidebar.classList.add("collapsed");
            mainContent.classList.add("expanded");
            profile.classList.add("collapsed");
        }

        // Toggle sidebar manual
        toggleBtn.addEventListener("click", () => {
            const collapsedNow = sidebar.classList.toggle("collapsed");
            mainContent.classList.toggle("expanded");
            profile.classList.toggle("collapsed");
            localStorage.setItem("sidebarCollapsed", collapsedNow);
        });
    });

        const logoutBtn = document.getElementById('logout-btn');
        const logoutPopup = document.getElementById('logout-popup');
        const confirmLogout = document.getElementById('confirm-logout');
        const cancelLogout = document.getElementById('cancel-logout');

        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();      // supaya tidak langsung ke href
            logoutPopup.style.display = 'flex'; // tampilkan popup
        });

        cancelLogout.addEventListener('click', function() {
            logoutPopup.style.display = 'none'; // sembunyikan popup jika batal
        });

        confirmLogout.addEventListener('click', function() {
            window.location.href = 'logout.html'; // arahkan logout saat konfirmasi
        });

    </script>
</body>
</html>
