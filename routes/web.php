<?php
// routes/web.php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Web\Admin\AuthController;
use App\Http\Controllers\Web\Admin\DashboardController;
use App\Http\Controllers\Web\Admin\MahasiswaController;
use App\Http\Controllers\Web\Admin\DosenController;
use App\Http\Controllers\Web\Admin\InformasiTAController;
use App\Http\Controllers\Web\Admin\MonitoringController;
use App\Http\Controllers\Web\Admin\SupervisorQuotaController;

// =====================================================
// PUBLIC — Halaman Login Admin
// =====================================================
Route::prefix('admin')->name('admin.')->group(function () {

    Route::get('/login',  [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->name('login.post');

    // =====================================================
    // PROTECTED — Butuh session admin
    // =====================================================
    Route::middleware(['admin.auth'])->group(function () {

        Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

        // Dashboard
        Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

        // Manage Profile
        Route::get('/manage-profile', function () {
            return view('admin.manage-profile.index');
        })->name('manage-profile.index');

        // Manajemen Mahasiswa
        Route::prefix('mahasiswa')->name('mahasiswa.')->group(function () {
            Route::get('/',            [MahasiswaController::class, 'index'])->name('index');
            Route::get('/create',      [MahasiswaController::class, 'create'])->name('create');
            Route::post('/',           [MahasiswaController::class, 'store'])->name('store');
            Route::get('/{id}/edit',   [MahasiswaController::class, 'edit'])->name('edit');
            Route::put('/{id}',        [MahasiswaController::class, 'update'])->name('update');
            Route::delete('/{id}',     [MahasiswaController::class, 'destroy'])->name('destroy');
        });

        // Manajemen Dosen
        Route::prefix('dosen')->name('dosen.')->group(function () {
            Route::get('/',              [DosenController::class, 'index'])->name('index');
            Route::get('/create',        [DosenController::class, 'create'])->name('create');
            Route::post('/',             [DosenController::class, 'store'])->name('store');
            Route::get('/{id}/edit',     [DosenController::class, 'edit'])->name('edit');
            Route::put('/{id}',          [DosenController::class, 'update'])->name('update');
            Route::delete('/{id}',       [DosenController::class, 'destroy'])->name('destroy');
            Route::put('/{id}/kuota',    [DosenController::class, 'updateKuota'])->name('kuota');
        });

        // Supervisor Quota
        Route::prefix('supervisor-quota')->name('supervisor-quota.')->group(function () {
            Route::get('/',           [SupervisorQuotaController::class, 'index'])->name('index');
            Route::put('/{id}/kuota', [SupervisorQuotaController::class, 'updateKuota'])->name('update');
        });

        // Informasi TA
        Route::prefix('informasi-ta')->name('informasi-ta.')->group(function () {
            Route::get('/',              [InformasiTAController::class, 'index'])->name('index');
            Route::post('/',             [InformasiTAController::class, 'store'])->name('store');
            Route::put('/{id}',          [InformasiTAController::class, 'update'])->name('update');
            Route::delete('/{id}',       [InformasiTAController::class, 'destroy'])->name('destroy');
            Route::post('/{id}/toggle',  [InformasiTAController::class, 'togglePublish'])->name('toggle');
        });

        // Monitoring
        Route::prefix('monitoring')->name('monitoring.')->group(function () {
            Route::get('/permohonan', [MonitoringController::class, 'permohonan'])->name('permohonan');
            Route::get('/bimbingan',  [MonitoringController::class, 'bimbingan'])->name('bimbingan');
            Route::get('/jadwal',     [MonitoringController::class, 'jadwal'])->name('jadwal');
            Route::get('/dokumen',    [MonitoringController::class, 'dokumen'])->name('dokumen');
            Route::get('/progres',    [MonitoringController::class, 'progres'])->name('progres');
        });
    });
});

// Redirect root ke dashboard atau login
Route::get('/', function () {
    if (session()->has('admin_user')) {
        return redirect()->route('admin.dashboard');
    }
    return redirect()->route('admin.login');
});