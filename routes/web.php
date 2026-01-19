<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\Akademik\KelasController;
use App\Http\Controllers\Akademik\StaffController;
use App\Http\Controllers\Akademik\TahunAjaranController;
use App\Http\Controllers\AssesmentFormatifController;
use App\Http\Controllers\AssesmentSumatifController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DummyExcelController;
use App\Http\Controllers\EkstrakurikulerController;
use App\Http\Controllers\EkstrakurikulerSiswaController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IntrakurikulerController;
use App\Http\Controllers\LingkupMateriController;
use App\Http\Controllers\PenilaianEkstrakurikulerController;
use App\Http\Controllers\SiswaController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\TujuanPembelajaranController;
use App\Http\Controllers\WilayahController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    // return view('welcome');
    return redirect('/login');
});

// Auth::routes();
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('login', [AuthController::class, 'login'])->middleware('guest');
Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Route untuk manajemen user oleh super admin
Route::prefix('superadmin')->middleware(['auth', 'role:Super Admin'])->name('superadmin.')->group(function () {
    Route::resource('users', UserController::class);
});

// Route khusus bagian akademik
Route::middleware(['auth', 'role:Bagian Akademik'])->prefix('akademik')->name('akademik.')->group(function () {
    Route::resource('tahun_ajaran', TahunAjaranController::class);
    Route::resource('kelas', KelasController::class);
    Route::prefix('kelas/{kelas_ajar}')->group(function () {
        Route::resource('siswa', SiswaController::class);
        Route::get('ajax/search-siswa', [SiswaController::class, 'ajaxSearchSiswa'])->name('kelas.ajax.search-siswa');
        Route::post('add-existing-siswa', [SiswaController::class, 'addExistingSiswa'])->name('kelas.add-existing-siswa');
    });
    Route::resource('staff', StaffController::class);
});

// Define a group of routes with 'auth' middleware applied
Route::middleware(['auth'])->group(function () {
    // Define a GET route for the root URL ('/')
    Route::get('/', function () {
        // Return a view named 'index' when accessing the root URL
        return view('dashboard.index');
    });

    Route::resource('intrakurikuler', IntrakurikulerController::class)
        ->middleware('role:Guru Mapel|Bagian Akademik');

    Route::prefix('intrakurikuler/{intrakurikuler}')
        ->middleware('role:Guru Mapel|Bagian Akademik')
        ->group(function () {

            Route::resource('lingkup-materi', LingkupMateriController::class)
                ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

            Route::resource('assesment-sumatif', AssesmentSumatifController::class)
                ->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);

            // ✅ DETAIL (GET)
            Route::get('assesment-sumatif/{riwayatKelas}/detail', [AssesmentSumatifController::class, 'detailAssesmentSumatif'])
                ->name('assesment_sumatif.detail');

            // ✅ SIMPAN (POST kalau belum ada skor sama sekali)
            Route::post('assesment-sumatif/{riwayatKelas}/detail', [AssesmentSumatifController::class, 'storeDetailAssesmentSumatif'])
                ->name('assesment_sumatif.detail.store');

            // ✅ UPDATE (PUT kalau sudah ada skor)
            Route::put('assesment-sumatif/{riwayatKelas}/detail', [AssesmentSumatifController::class, 'updateDetailAssesmentSumatif'])
                ->name('assesment_sumatif.detail.update');
        });

    Route::resource('ekstrakurikuler', EkstrakurikulerController::class)->middleware('role:Guru Mapel|Bagian Akademik');
    Route::prefix('ekstrakurikuler/{ekstrakurikuler}')->group(function () {
        Route::get('manage-siswa/create', [EkstrakurikulerSiswaController::class, 'create'])
            ->name('ekstrakurikuler.manage-siswa.create');
        Route::post('manage-siswa', [EkstrakurikulerSiswaController::class, 'store'])
            ->name('ekstrakurikuler.manage-siswa.store');
        Route::post('manage-siswa/add-existing', [EkstrakurikulerSiswaController::class, 'addExistingSiswa'])
            ->name('ekstrakurikuler.manage-siswa.add-existing');
        Route::resource('manage-siswa', EkstrakurikulerSiswaController::class)
            ->only(['index', 'destroy'])
            ->names('ekstrakurikuler.manage-siswa');

        Route::get('ajax/search-siswa', [EkstrakurikulerSiswaController::class, 'ajaxSearchSiswa'])
            ->name('ekstrakurikuler.ajax.search-siswa');
    })->middleware('role:Guru Mapel|Bagian Akademik');

    Route::resource('tujuan_pembelajaran', TujuanPembelajaranController::class);
    // Route::get('assesment_sumatif/detail', [AssesmentSumatifController::class, 'detailAssesmentSumatif'])->name('assesment_sumatif.detail');
    // Route::resource('assesment_sumatif', AssesmentSumatifController::class);
    Route::get('assesment_formatif/detail', [AssesmentFormatifController::class, 'detailAssesmentFormatif'])->name('assesment_formatif.detail');
    Route::resource('assesment_formatif', AssesmentFormatifController::class);
    Route::resource('penilaian_ekstrakurikuler', PenilaianEkstrakurikulerController::class);

    Route::get('absensi/daily', [AbsensiController::class, 'absensiHarian'])->name('absensi.harian');
    Route::resource('absensi', AbsensiController::class);

    // Define a GET route with dynamic placeholders for route parameters
    Route::get('/template-assesmen-formatif-excel', [DummyExcelController::class, 'downloadFormatif']);
    Route::get('/template-assesmen-sumatif-excel', [DummyExcelController::class, 'downloadSumatif']);

    // Route::get('{routeName}/{name?}', [HomeController::class, 'pageView']);
});

Route::get('/wilayah/provinsi', [WilayahController::class, 'provinsi']);
Route::get('/wilayah/kabupaten/{provinsi_id}', [WilayahController::class, 'kabupaten']);
Route::get('/wilayah/kecamatan/{kabupaten_id}', [WilayahController::class, 'kecamatan']);
Route::get('/wilayah/kelurahan/{kecamatan_id}', [WilayahController::class, 'kelurahan']);
Route::get('ajax/tempat-lahir/kabupaten', [WilayahController::class, 'searchKabupaten'])->name('ajax.tempat_lahir.kabupaten');
Route::get('ajax/domisili/kelurahan', [WilayahController::class, 'searchKelurahan'])->name('ajax.domisili.kelurahan');
