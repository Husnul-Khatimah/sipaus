    <?php

    use Illuminate\Support\Facades\Route;
    use App\Http\Controllers\AdminController;
    use App\Http\Controllers\PenggunaController;
    use App\Http\Controllers\SuplierController;
    use App\Http\Controllers\AuthController;    // PENTING: Aktifkan ini untuk AuthController kustom Anda

    /*
    |--------------------------------------------------------------------------
    | Web Routes
    |--------------------------------------------------------------------------
    |
    | Here is where you can register web routes for your application. These
    | routes are loaded by the RouteServiceProvider within a group which
    | contains the "web" middleware group. Now create something great!
    |
    */

    // Rute Halaman Umum (jika ada halaman sambutan umum)
    Route::get('/', function () {
        return view('welcome_page_umum');
    })->name('home');

    // Rute Autentikasi (Menggunakan AuthController kustom)
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'authenticate']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // Rute untuk halaman pendaftaran, lupa password, dll. (jika ada)
    Route::get('/forgot-password', [AuthController::class, 'forgotPassword'])->name('forgot_password');
    Route::post('/forgot-password', [AuthController::class, 'sendResetLinkEmail'])->name('forgot_password_submit'); // PENTING: Tambahkan ini
    Route::get('/access-account', [AuthController::class, 'accessAccount'])->name('access_account');
    // Jika Anda memiliki halaman reset password setelah link dikirim (misal: /reset-password/{token})
    // Route::get('/reset-password/{token}', [AuthController::class, 'resetPasswordForm'])->name('password.reset');
    // Route::post('/reset-password', [AuthController::class, 'resetPassword'])->name('password.update');


    // Grup Rute untuk Admin
    Route::prefix('admin')->middleware(['auth'])->group(function () {
        Route::get('/', [AdminController::class, 'home'])->name('admin.home');
        Route::get('/data-atk', [AdminController::class, 'dataAtk'])->name('admin.data_atk');
        Route::post('/data-atk', [AdminController::class, 'storeAtk'])->name('admin.store_atk');
        Route::delete('/data-atk/{id}', [AdminController::class, 'destroyAtk'])->name('admin.destroy_atk');
        Route::get('/pengguna', [AdminController::class, 'pengguna'])->name('admin.pengguna');
        Route::post('/pengguna', [AdminController::class, 'storePengguna'])->name('admin.store_pengguna');
        Route::delete('/pengguna/{id}', [AdminController::class, 'destroyPengguna'])->name('admin.destroy_pengguna');
        Route::get('/permintaan', [AdminController::class, 'permintaan'])->name('admin.permintaan');
        Route::post('/permintaan/{id}/setujui', [AdminController::class, 'approvePermintaan'])->name('admin.approve_permintaan');
        Route::post('/permintaan/{id}/tolak', [AdminController::class, 'rejectPermintaan'])->name('admin.reject_permintaan');
        Route::get('/pesan-atk', [AdminController::class, 'pesanAtk'])->name('admin.pesan_atk');
        Route::post('/pesan-atk', [AdminController::class, 'storePesanAtk'])->name('admin.store_pesan_atk');
        Route::delete('/pesan-atk/{id}', [AdminController::class, 'destroyPesanAtk'])->name('admin.destroy_pesan_atk');
        Route::get('/data-survai', [AdminController::class, 'dataSurvai'])->name('admin.data_survai');
        Route::post('/data-survai/{id}/setujui', [AdminController::class, 'approveSurvai'])->name('admin.approve_survai');
        Route::post('/data-survai/{id}/tolak', [AdminController::class, 'rejectSurvai'])->name('admin.reject_survai');
    });

    // Rute API Admin
    Route::prefix('api/admin')->group(function () {
        Route::get('/data-atk', [AdminController::class, 'getAtkData']);
        Route::get('/pengguna', [AdminController::class, 'getPenggunaData']);
        Route::get('/permintaan', [AdminController::class, 'getPermintaanData']);
        Route::get('/pesan-atk', [AdminController::class, 'getPesanAtkData']);
        Route::get('/data-survai', [AdminController::class, 'getSurvaiData']);
        Route::get('/jenis-atk', [AdminController::class, 'getJenisAtk']);
        Route::get('/atk-items-for-dropdown', [AdminController::class, 'getAtkItemsForDropdown']);
    });

    // Rute Pengguna
    Route::prefix('pengguna')->middleware(['auth'])->group(function () {
        Route::get('/', [PenggunaController::class, 'home'])->name('pengguna.home');
        Route::get('/pengambilan', [PenggunaController::class, 'pengambilan'])->name('pengguna.pengambilan');
        Route::post('/pengambilan', [PenggunaController::class, 'storePengambilan'])->name('pengguna.store_pengambilan');
        Route::delete('/pengambilan/{id}', [PenggunaController::class, 'destroyPengambilan'])->name('pengguna.destroy_pengambilan');
        Route::get('/survei', [PenggunaController::class, 'survei'])->name('pengguna.survei');
        Route::post('/survei', [PenggunaController::class, 'storeSurvei'])->name('pengguna.store_survei');
        Route::delete('/survei/{id}', [PenggunaController::class, 'destroySurvei'])->name('pengguna.destroy_survei');
    });

    // Rute API Pengguna
    Route::prefix('api/pengguna')->middleware(['auth'])->group(function () {
        Route::get('/dashboard-data', [PenggunaController::class, 'getDashboardData']);
        Route::get('/pengambilan', [PenggunaController::class, 'getPengambilanData']);
        Route::get('/survei', [PenggunaController::class, 'getSurveiData']);
    });

    // Rute Supplier
    Route::prefix('suplier')->middleware(['auth'])->group(function () {
        Route::get('/', [SuplierController::class, 'home'])->name('suplier.home');
        Route::get('/katalog', [SuplierController::class, 'katalog'])->name('suplier.katalog');
        Route::post('/katalog', [SuplierController::class, 'storeKatalog'])->name('suplier.store_katalog');
        Route::delete('/katalog/{id}', [SuplierController::class, 'destroyKatalog'])->name('suplier.destroy_katalog');
        Route::get('/permintaan', [SuplierController::class, 'permintaan'])->name('suplier.permintaan');
        Route::post('/permintaan/{id}/update-status', [SuplierController::class, 'updatePermintaanPesananStatus'])->name('suplier.update_permintaan_status');
    });

    // Rute API Supplier
    Route::prefix('api/suplier')->middleware(['auth'])->group(function () {
        Route::get('/katalog', [SuplierController::class, 'getKatalogData']);
        Route::get('/permintaan', [SuplierController::class, 'getPermintaanPesananData']);
    });

    // Rute umum lainnya
    Route::get('/about', function () {
        return view('about');
    })->name('about');

    Route::get('/contact', function () {
        return view('contact');
    })->name('contact');

    // Redirect root ke login
    Route::get('/', function () {
        return redirect()->route('login');
    })->name('root');
    