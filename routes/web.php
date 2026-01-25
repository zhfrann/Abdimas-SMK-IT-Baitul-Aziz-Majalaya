<?php

use App\Http\Controllers\AbsensiControllerIntrakurikuler;
use App\Http\Controllers\Akademik\KelasController;
use App\Http\Controllers\Akademik\StaffController;
use App\Http\Controllers\Akademik\TahunAjaranController;
use App\Http\Controllers\AssesmentFormatifController;
use App\Http\Controllers\AssesmentSumatifController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\CetakDokumenController;
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

        Route::get('load-siswa', [SiswaController::class, 'showLoadSiswaForm'])->name('kelas.show-load-siswa');
        Route::post('load-siswa', [SiswaController::class, 'loadSiswaFromKelas'])->name('kelas.load-siswa');
        // Route::get('ajax/kelas/search', [SiswaController::class, 'ajaxSearchKelas'])->name('ajax.kelas.search');
    });

    Route::get('kelas/ajax/kelas/search', [SiswaController::class, 'ajaxSearchKelas'])->name('ajax.kelas.search');
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


            Route::resource('tujuan-pembelajaran', TujuanPembelajaranController::class)
                ->except(['show']);

            Route::resource('assesment-formatif', AssesmentFormatifController::class);

            Route::get('assesment-formatif/{riwayatKelas}/detail', [AssesmentFormatifController::class, 'detailAssesmentFormatif'])
                ->name('assesment-formatif.detail');
            Route::post('assesment-formatif/{riwayatKelas}/save-detail', [AssesmentFormatifController::class, 'saveDetail'])
                ->name('assesment-formatif.save-detail');
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

        Route::resource('penilaian_ekstrakurikuler', PenilaianEkstrakurikulerController::class);
    })->middleware('role:Guru Mapel|Bagian Akademik');

    Route::prefix('absensi')->name('absensi.')->group(function () {
        Route::get('intrakurikuler', [AbsensiControllerIntrakurikuler::class, 'listIntrakurikuler'])
            ->name('intrakurikuler.list')
            ->middleware('role:Guru Mapel|Bagian Akademik|Super Admin');

        Route::get('intrakurikuler/{intrakurikuler}/harian', [AbsensiControllerIntrakurikuler::class, 'harian'])
            ->name('intrakurikuler.harian');

        Route::post('intrakurikuler/{intrakurikuler}/harian', [AbsensiControllerIntrakurikuler::class, 'storeHarian'])
            ->name('intrakurikuler.harian.store');

        Route::get('intrakurikuler/{intrakurikuler}/rekap', [AbsensiControllerIntrakurikuler::class, 'rekap'])
            ->name('intrakurikuler.rekap');
    });

    // Define a GET route with dynamic placeholders for route parameters
    Route::get('/template-assesmen-formatif-excel', [DummyExcelController::class, 'downloadFormatif']);
    Route::get('/template-assesmen-sumatif-excel', [DummyExcelController::class, 'downloadSumatif']);

    Route::prefix('dokumen')->name('dokumen.')->group(function () {
        Route::get('kelas', [CetakDokumenController::class, 'kelas'])->name('kelas');
        Route::get('kelas/{kelas_ajar}/pilih', [CetakDokumenController::class, 'pilihCetak'])->name('kelas.pilih');
        Route::post('cetak-sampul', [CetakDokumenController::class, 'cetakSampul'])->name('cetak.sampul');
        Route::post('cetak-rapor', [CetakDokumenController::class, 'cetakRapor'])->name('cetak.rapor');
        Route::post('cetak-buku-induk', [CetakDokumenController::class, 'cetakBukuInduk'])->name('cetak.buku_induk');
        Route::get('mutasi', [CetakDokumenController::class, 'mutasi'])->name('mutasi');
        Route::post('cetak-mutasi', [CetakDokumenController::class, 'cetakMutasi'])->name('cetak.mutasi');
    });

    // Route::get('{routeName}/{name?}', [HomeController::class, 'pageView']);
});

Route::get('/wilayah/provinsi', [WilayahController::class, 'provinsi']);
Route::get('/wilayah/kabupaten/{provinsi_id}', [WilayahController::class, 'kabupaten']);
Route::get('/wilayah/kecamatan/{kabupaten_id}', [WilayahController::class, 'kecamatan']);
Route::get('/wilayah/kelurahan/{kecamatan_id}', [WilayahController::class, 'kelurahan']);
Route::get('ajax/tempat-lahir/kabupaten', [WilayahController::class, 'searchKabupaten'])->name('ajax.tempat_lahir.kabupaten');
Route::get('ajax/domisili/kelurahan', [WilayahController::class, 'searchKelurahan'])->name('ajax.domisili.kelurahan');
