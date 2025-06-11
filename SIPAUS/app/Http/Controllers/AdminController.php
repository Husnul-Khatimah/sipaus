<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Atk;
use App\Models\User; // PENTING: Pastikan ini diimpor
use App\Models\Permintaan; // PENTING: Pastikan ini diimpor
use App\Models\PesanAtk;   // PENTING: Pastikan ini diimpor
use App\Models\Survai;    // PENTING: Pastikan ini diimpor
use App\Models\JenisAtk; // PENTING: Pastikan ini diimpor
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;
use Illuminate\Support\Carbon; // Untuk timestamp, jika digunakan

class AdminController extends Controller
{
    /**
     * Tampilkan halaman dashboard admin.
     * Data statistik akan dimuat via AJAX/JavaScript di front-end.
     */
    public function home()
    {
        // Metode ini sekarang hanya bertugas menampilkan view.
        // Data statistik dan riwayat akan diambil oleh JavaScript melalui API.
        return view('admin.home_admin');
    }

    /**
     * Tampilkan halaman manajemen Data ATK.
     * Data tabel akan dimuat via AJAX/JavaScript di front-end.
     */
    public function dataAtk()
    {
        return view('admin.data_atk_admin');
    }

    /**
     * Tampilkan halaman manajemen Pengguna.
     * Data tabel akan dimuat via AJAX/JavaScript di front-end.
     */
    public function pengguna()
    {
        return view('admin.pengguna_admin');
    }

    /**
     * Tampilkan halaman manajemen Permintaan.
     * Data tabel akan dimuat via AJAX/JavaScript di front-end.
     */
    public function permintaan()
    {
        return view('admin.permintaan_admin');
    }

    /**
     * Tampilkan halaman manajemen Pesan ATK.
     * Data tabel akan dimuat via AJAX/JavaScript di front-end.
     */
    public function pesanAtk()
    {
        return view('admin.pesan_atk_admin');
    }

    /**
     * Tampilkan halaman manajemen Data Survei.
     * Data tabel akan dimuat via AJAX/JavaScript di front-end.
     */
    public function dataSurvai()
    {
        return view('admin.data_survai_admin');
    }


    /*
    |--------------------------------------------------------------------------
    | API Methods (untuk interaksi data dengan database melalui AJAX/Fetch API)
    |--------------------------------------------------------------------------
    */

    /**
     * Mengambil data statistik dan riwayat untuk dashboard admin.
     * Dipanggil oleh JavaScript di halaman home_admin.
     */
    public function getDashboardData()
    {
        try {
            $totalBarang = Atk::count();
            $totalPengguna = User::count();
            $totalPermintaanPending = Permintaan::where('status', 'pending')->count();
            $totalPesanAtkMenunggu = PesanAtk::where('status', 'menunggu')->count();
            $totalSurvei = Survai::count();

            // Mengambil riwayat pengambilan terbaru (misalnya 5 terakhir)
            // Asumsi: tabel 'permintaans' merepresentasikan pengambilan, dan memiliki relasi 'user' dan 'atk'
            $riwayatPengambilan = Permintaan::with(['user', 'atk'])
                ->where('status', 'Disetujui') // Hanya yang sudah disetujui sebagai riwayat pengambilan
                ->latest('tanggal_permintaan') // Urutkan berdasarkan tanggal terbaru
                ->limit(5)
                ->get()
                ->map(function ($item) {
                    return [
                        'nama_barang' => $item->atk ? $item->atk->nama : ($item->nama_barang_manual ?? 'N/A'), // Ambil dari relasi atau kolom manual
                        'jumlah' => $item->jumlah,
                        'tanggal_ambil' => Carbon::parse($item->tanggal_permintaan)->format('d M Y'), // Format tanggal
                    ];
                });

            // Data untuk Chart (contoh statis, Anda bisa membuatnya dinamis dari DB)
            $chartData = [
                'barLabels' => ["Kertas A4", "Spidol", "Pulpen", "Penghapus", "Buku Tulis"],
                'barValues' => [20, 15, 10, 8, 12],
                'lineLabels' => ["Jan", "Feb", "Mar", "Apr", "Mei", "Jun"],
                'lineValues' => [50, 60, 55, 70, 65, 80],
            ];

            return response()->json([
                'totalBarang' => $totalBarang,
                'totalPengguna' => $totalPengguna,
                'totalPermintaan' => Permintaan::count(), // Total semua permintaan
                'totalDisetujui' => Permintaan::where('status', 'Disetujui')->count(),
                'totalSurvei' => $totalSurvei,
                'totalPesan' => PesanAtk::count(), // Total semua pesanan
                'riwayatPengambilan' => $riwayatPengambilan,
                'chartData' => $chartData,
            ]);
        } catch (\Exception $e) {
            \Log::error('Error fetching admin dashboard data: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal memuat data dashboard.'], 500);
        }
    }


    public function getJenisAtk()
    {
        try {
            // PENTING: Ambil kolom 'id_jenis_atk' dan 'nama_kategori'
            $jenisAtk = JenisAtk::select('id_jenis_atk', 'nama_kategori')->get();
            return response()->json($jenisAtk);
        } catch (\Exception $e) {
            \Log::error('Error fetching Jenis ATK: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal mengambil jenis ATK.'], 500);
        }
    }

    /**
     * Menyimpan data ATK baru.
     * Dipanggil oleh JavaScript di halaman data_atk_admin saat submit form modal.
     */
    public function storeAtk(Request $request)
    {
        $request->validate([
            'atk_id' => 'required|exists:atk,id_atk', // ID barang yang dipilih
            'stok' => 'required|integer|min:0', // Stok yang akan ditambahkan
            'satuan' => 'required|string|max:50', // Satuan (mungkin perlu di-fetch dari item_id)
            // 'jenis_atk_id' => 'required|exists:jenis_atk,id_jenis_atk', // Kategori mungkin tidak perlu jika hanya update stok
        ]);

        try {
            $atk = Atk::find($request->atk_id);

            if (!$atk) {
                return response()->json(['success' => false, 'message' => 'Barang tidak ditemukan.'], 404);
            }

            // Update stok barang yang sudah ada
            $atk->stok += $request->stok; // Tambahkan stok baru ke stok yang sudah ada
            $atk->satuan = $request->satuan; // Update satuan jika perlu
            // $atk->jenis_atk_id = $request->jenis_atk_id; // Update kategori jika perlu
            $atk->save(); // Simpan perubahan

            return response()->json([
                'success' => true,
                'message' => 'Stok barang berhasil diupdate!',
                'data' => $atk
            ], 200); // Status HTTP 200 OK untuk update
        } catch (\Exception $e) {
            \Log::error('Error updating ATK stock: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal mengupdate stok barang.'], 500);
        }
    }

    /**
     * Mengambil semua data ATK.
     * Dipanggil oleh JavaScript di halaman data_atk_admin.
     * PENTING: Sekarang kita akan memuat relasi jenisAtk untuk mendapatkan nama kategorinya.
     */
    public function getAtkData()
    {
        try {
            $atks = Atk::with('jenisAtk')->get()->map(function($atk) {
                return [
                    'id' => $atk->id_atk, // Gunakan id_atk sebagai ID utama
                    'nama_barang' => $atk->nama_barang,
                    'stok' => $atk->stok,
                    'satuan' => $atk->satuan,
                    'kategori' => $atk->jenisAtk ? $atk->jenisAtk->nama_kategori : 'Tidak Diketahui',
                ];
            });
            return response()->json($atks);
        } catch (\Exception $e) {
            \Log::error('Error fetching ATK data: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal mengambil data ATK.'], 500);
        }
    }

    public function getAtkItemsForDropdown()
    {
        try {
            // Ambil id_atk dan nama_barang dari tabel atk
            $atkItems = Atk::select('id_atk', 'nama_barang')->get(); // Pastikan 'nama_barang' ada di tabel 'atk'
            return response()->json($atkItems);
        } catch (\Exception $e) {
            \Log::error('Error fetching ATK items for dropdown: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal mengambil daftar barang.'], 500);
        }
    }

    // ... (metode destroyAtk, getPenggunaData, storePengguna, destroyPengguna, dll. tetap sama) ...

    /**
     * Menghapus data ATK berdasarkan ID.
     * Dipanggil oleh JavaScript di halaman data_atk_admin saat klik tombol hapus.
     */
    public function destroyAtk($id)
    {
        try {
            $atk = Atk::find($id);
            if (!$atk) {
                return response()->json(['success' => false, 'message' => 'Data barang tidak ditemukan.'], 404);
            }
            $atk->delete();
            return response()->json(['success' => true, 'message' => 'Data barang berhasil dihapus!']);
        } catch (\Exception $e) {
            \Log::error('Error deleting ATK: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus data barang.'], 500);
        }
    }

    // --- Pengguna (Users) ---
    /**
     * Mengambil semua data Pengguna.
     * Dipanggil oleh JavaScript di halaman pengguna_admin.
     */
    public function getPenggunaData()
    {
        try {
            $users = User::select('id', 'name', 'email', 'role', 'nip')->get(); // Tambahkan 'nip'
            return response()->json($users);
        } catch (\Exception $e) {
            \Log::error('Error fetching User data: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal mengambil data pengguna.'], 500);
        }
    }

    /**
     * Menyimpan data Pengguna baru.
     * Dipanggil oleh JavaScript di halaman pengguna_admin saat submit form modal.
     */
    public function storePengguna(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'gmail' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
            'role' => ['required', 'string', Rule::in(['Admin', 'Pengguna', 'Supplier'])],
            'nip' => 'nullable|string|max:50|unique:users,nip', // Tambahkan validasi NIP
        ]);

        try {
            $user = User::create([
                'name' => $request->nama,
                'email' => $request->gmail,
                'password' => Hash::make($request->password),
                'role' => $request->role,
                'nip' => $request->nip, // Simpan NIP
                'email_verified_at' => Carbon::now(), // Otomatis verified jika dibuat admin
            ]);
            return response()->json([
                'success' => true,
                'message' => 'Pengguna berhasil ditambahkan!',
                'data' => $user
            ], 201);
        } catch (\Exception $e) {
            \Log::error('Error storing User: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan pengguna.'], 500);
        }
    }

    /**
     * Menghapus data Pengguna berdasarkan ID.
     * Dipanggil oleh JavaScript di halaman pengguna_admin saat klik tombol hapus.
     */
    public function destroyPengguna($id)
    {
        try {
            $user = User::find($id);
            if (!$user) {
                return response()->json(['success' => false, 'message' => 'Pengguna tidak ditemukan.'], 404);
            }
            $user->delete();
            return response()->json(['success' => true, 'message' => 'Pengguna berhasil dihapus!']);
        } catch (\Exception $e) {
            \Log::error('Error deleting User: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus pengguna.'], 500);
        }
    }

    // --- Permintaan ---
    /**
     * Mengambil semua data Permintaan.
     * Dipanggil oleh JavaScript di halaman permintaan_admin.
     */
    public function getPermintaanData()
    {
        try {
            $permintaan = Permintaan::with(['user', 'atk'])->get();
            return response()->json($permintaan);
        } catch (\Exception $e) {
            \Log::error('Error fetching Permintaan data: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal mengambil data permintaan.'], 500);
        }
    }

    /**
     * Menyetujui permintaan berdasarkan ID.
     * Dipanggil oleh JavaScript di halaman permintaan_admin.
     */
    public function approvePermintaan($id)
    {
        try {
            $permintaan = Permintaan::find($id);
            if (!$permintaan) {
                return response()->json(['success' => false, 'message' => 'Permintaan tidak ditemukan.'], 404);
            }
            $permintaan->update(['status' => 'Disetujui']);
            return response()->json(['success' => true, 'message' => 'Permintaan berhasil disetujui!']);
        } catch (\Exception $e) {
            \Log::error('Error approving Permintaan: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyetujui permintaan.'], 500);
        }
    }

    /**
     * Menolak permintaan berdasarkan ID.
     * Dipanggil oleh JavaScript di halaman permintaan_admin.
     */
    public function rejectPermintaan($id)
    {
        try {
            $permintaan = Permintaan::find($id);
            if (!$permintaan) {
                return response()->json(['success' => false, 'message' => 'Permintaan tidak ditemukan.'], 404);
            }
            $permintaan->update(['status' => 'Ditolak']);
            return response()->json(['success' => true, 'message' => 'Permintaan berhasil ditolak!']);
        } catch (\Exception $e) {
            \Log::error('Error rejecting Permintaan: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menolak permintaan.'], 500);
        }
    }

    // --- Pesan ATK ---
    /**
     * Mengambil semua data Pesan ATK.
     * Dipanggil oleh JavaScript di halaman pesan_atk_admin.
     */
    public function getPesanAtkData()
    {
        try {
            $pesanan = PesanAtk::with(['supplier', 'atk'])->get();
            return response()->json($pesanan);
        } catch (\Exception $e) {
            \Log::error('Error fetching Pesan ATK data: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal mengambil data pesanan ATK.'], 500);
        }
    }

    /**
     * Menyimpan data Pesan ATK baru.
     * Dipanggil oleh JavaScript di halaman pesan_atk_admin saat submit form modal.
     */
    public function storePesanAtk(Request $request)
    {
        $request->validate([
            'nama_supplier' => 'required|string|max:255',
            'nama_barang' => 'required|string|max:255',
            'jumlah_pesan' => 'required|integer|min:0',
            'tanggal_pesan' => 'required|date',
            'status' => 'string|max:50'
        ]);

        try {
            $pesanAtk = PesanAtk::create([
                'nama_supplier' => $request->nama_supplier,
                'nama_barang' => $request->nama_barang,
                'jumlah_pesan' => $request->jumlah_pesan,
                'tanggal_pesan' => $request->tanggal_pesan,
                'status' => $request->status ?? 'menunggu',
            ]);
            return response()->json(['success' => true, 'message' => 'Pesanan berhasil ditambahkan!', 'data' => $pesanAtk], 201);
        } catch (\Exception $e) {
            \Log::error('Error storing Pesan ATK: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyimpan pesanan ATK.'], 500);
        }
    }

    /**
     * Menghapus data Pesan ATK berdasarkan ID.
     * Dipanggil oleh JavaScript di halaman pesan_atk_admin.
     */
    public function destroyPesanAtk($id)
    {
        try {
            $pesanAtk = PesanAtk::find($id);
            if (!$pesanAtk) {
                return response()->json(['success' => false, 'message' => 'Pesanan tidak ditemukan.'], 404);
            }
            $pesanAtk->delete();
            return response()->json(['success' => true, 'message' => 'Pesanan berhasil dihapus!']);
        } catch (\Exception $e) {
            \Log::error('Error deleting Pesan ATK: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menghapus pesanan ATK.'], 500);
        }
    }

    // --- Data Survei ---
    /**
     * Mengambil semua data Survei.
     * Dipanggil oleh JavaScript di halaman data_survai_admin.
     */
    public function getSurvaiData()
    {
        try {
            $survei = Survai::with('user')->get();
            return response()->json($survei);
        } catch (\Exception $e) {
            \Log::error('Error fetching Survei data: ' . $e->getMessage());
            return response()->json(['message' => 'Gagal mengambil data survei.'], 500);
        }
    }

    /**
     * Menyetujui survei berdasarkan ID.
     * Dipanggil oleh JavaScript di halaman data_survai_admin.
     */
    public function approveSurvai($id)
    {
        try {
            $survai = Survai::find($id);
            if (!$survai) {
                return response()->json(['success' => false, 'message' => 'Data survei tidak ditemukan.'], 404);
            }
            $survai->update(['status' => 'Disetujui']);
            return response()->json(['success' => true, 'message' => 'Survei berhasil disetujui!']);
        } catch (\Exception $e) {
            \Log::error('Error approving Survei: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menyetujui survei.'], 500);
        }
    }

    /**
     * Menolak survei berdasarkan ID.
     * Dipanggil oleh JavaScript di halaman data_survai_admin.
     */
    public function rejectSurvai($id)
    {
        try {
            $survai = Survai::find($id);
            if (!$survai) {
                return response()->json(['success' => false, 'message' => 'Data survei tidak ditemukan.'], 404);
            }
            $survai->update(['status' => 'Ditolak']);
            return response()->json(['success' => true, 'message' => 'Survei berhasil ditolak!']);
        } catch (\Exception $e) {
            \Log::error('Error rejecting Survei: ' . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Gagal menolak survei.'], 500);
        }
    }

}