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

    Route::resource('intrakurikuler', IntrakurikulerController::class)->middleware('role:Guru Mapel|Bagian Akademik');
    Route::prefix('intrakurikuler/{intrakurikuler}')->group(function () {
        Route::resource('lingkup-materi', LingkupMateriController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
        Route::resource('assesment-sumatif', AssesmentSumatifController::class)->only(['index', 'create', 'store', 'edit', 'update', 'destroy']);
    })->middleware('role:Guru Mapel|Bagian Akademik');

    Route::resource('ekstrakurikuler', EkstrakurikulerController::class);
    Route::resource('tujuan_pembelajaran', TujuanPembelajaranController::class);
    Route::get('assesment_sumatif/detail', [AssesmentSumatifController::class, 'detailAssesmentSumatif'])->name('assesment_sumatif.detail');
    Route::resource('assesment_sumatif', AssesmentSumatifController::class);
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
