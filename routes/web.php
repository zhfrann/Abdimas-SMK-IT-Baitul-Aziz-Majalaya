<?php

use App\Http\Controllers\AbsensiController;
use App\Http\Controllers\AssesmentFormatifController;
use App\Http\Controllers\AssesmentSumatifController;
use App\Http\Controllers\Auth\AuthController;
use App\Http\Controllers\DummyExcelController;
use App\Http\Controllers\EkstrakurikulerController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\IntrakurikulerController;
use App\Http\Controllers\LingkupMateriController;
use App\Http\Controllers\PenilaianEkstrakurikulerController;
use App\Http\Controllers\SuperAdmin\UserController;
use App\Http\Controllers\TujuanPembelajaranController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

// Auth::routes();
Route::get('login', [AuthController::class, 'showLoginForm'])->name('login')->middleware('guest');
Route::post('login', [AuthController::class, 'login'])->middleware('guest');
Route::post('logout', [AuthController::class, 'logout'])->name('logout')->middleware('auth');

// Route untuk manajemen user oleh super admin
Route::prefix('superadmin')->middleware(['auth', 'role:Super Admin'])->name('superadmin.')->group(function () {
    // List semua user
    Route::get('users', [UserController::class, 'index'])->name('users.index');
    // Form tambah user
    Route::get('users/create', [UserController::class, 'create'])->name('users.create');
    // Simpan user baru
    Route::post('users', [UserController::class, 'store'])->name('users.store');

    // Detail user (opsional, jika ingin detail user)
    // Route::get('users/{user}', [UserController::class, 'show'])->name('users.show');

    // Form edit user
    // Route::get('users/{user}/edit', [UserController::class, 'edit'])->name('users.edit');

    // Update user
    // Route::put('users/{user}', [UserController::class, 'update'])->name('users.update');

    // Hapus user
    // Route::delete('users/{user}', [UserController::class, 'destroy'])->name('users.destroy');
});

// Define a group of routes with 'auth' middleware applied
Route::middleware(['auth'])->group(function () {
    // Define a GET route for the root URL ('/')
    Route::get('/', function () {
        // Return a view named 'index' when accessing the root URL
        return view('dashboard.index');
    });

    Route::resource('intrakurikuler', IntrakurikulerController::class);
    Route::resource('ekstrakurikuler', EkstrakurikulerController::class);
    Route::resource('lingkup_materi', LingkupMateriController::class);
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
