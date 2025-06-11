// File: public/js/admin_common.js



document.addEventListener("DOMContentLoaded", () => {
    const toggleBtn = document.getElementById("toggle-btn");
    const sidebar = document.getElementById("sidebar");
    const mainContent = document.getElementById("main-content");
    const profile = document.getElementById("profile"); // Perlu ID ini jika ingin class 'collapsed' diterapkan ke profile

    // Fungsi collapse otomatis saat layar kecil atau mempertahankan state dari localStorage
    function autoAdjustSidebar() {
        if (window.innerWidth <= 992) { // Ukuran untuk tablet atau lebih kecil
            sidebar.classList.add("collapsed");
            mainContent.classList.add("expanded");
            if (profile) profile.classList.add("collapsed");
            // Di mobile (<= 768px), sidebar akan tersembunyi sepenuhnya dan muncul dengan kelas active-mobile
            if (window.innerWidth <= 768) {
                sidebar.classList.remove("active-mobile"); // Pastikan tersembunyi dulu
                sidebar.classList.remove("collapsed"); // Hapus collapsed jika di mobile, karena kita pakai active-mobile
                mainContent.classList.remove("expanded");
            }
        } else {
            // Untuk layar besar, cek localStorage
            const isCollapsed = localStorage.getItem("sidebarCollapsed") === "true";
            if (isCollapsed) {
                sidebar.classList.add("collapsed");
                mainContent.classList.add("expanded");
                if (profile) profile.classList.add("collapsed");
            } else {
                sidebar.classList.remove("collapsed");
                mainContent.classList.remove("expanded");
                if (profile) profile.classList.remove("collapsed");
            }
        }
    }

    // Jalankan saat awal dan saat resize
    autoAdjustSidebar();
    window.addEventListener("resize", autoAdjustSidebar);

    // Toggle sidebar manual
    toggleBtn.addEventListener("click", () => {
        // Untuk mobile, gunakan kelas 'active-mobile'
        if (window.innerWidth <= 768) {
            sidebar.classList.toggle("active-mobile");
            // Tidak perlu mengubah mainContent margin-left di mobile
            // Atau Anda bisa menambahkan overlay dan mencegah scroll body
        } else {
            const collapsedNow = sidebar.classList.toggle("collapsed");
            mainContent.classList.toggle("expanded");
            if (profile) profile.classList.toggle("collapsed");
            localStorage.setItem("sidebarCollapsed", collapsedNow);
        }
    });


    // --- Logika Logout Popup ---
    const logoutBtn = document.getElementById('logout-btn');
    const logoutPopup = document.getElementById('logout-popup');
    const confirmLogoutBtn = document.getElementById('confirm-logout-btn'); // ID tombol konfirmasi di popup
    const cancelLogoutBtn = document.getElementById('cancel-logout'); // ID tombol batal di popup

    if (logoutBtn) { // Pastikan elemen ada
        logoutBtn.addEventListener('click', function(e) {
            e.preventDefault();
            logoutPopup.style.display = 'flex'; // Tampilkan popup
        });
    }

    if (cancelLogoutBtn) { // Pastikan elemen ada
        cancelLogoutBtn.addEventListener('click', function() {
            logoutPopup.style.display = 'none'; // Sembunyikan popup jika batal
        });
    }

    if (confirmLogoutBtn) { // Pastikan elemen ada
        confirmLogoutBtn.addEventListener('click', function() {
            // Ini akan mensubmit form logout Laravel
            document.getElementById('logout-form').submit();
        });
    }
    // Menutup popup jika klik di luar area popup content
    if (logoutPopup) {
        logoutPopup.addEventListener('click', function(e) {
            if (e.target === logoutPopup) {
                logoutPopup.style.display = 'none';
            }
        });
    }
});