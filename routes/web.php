<?php

use App\Http\Controllers\AuthController;
use App\Livewire\Auth\Login;
use App\Livewire\Dashboard\AdminDashboard;
use App\Livewire\Dashboard\CmbDashboard;
use App\Livewire\Dashboard\OpsdDashboard;
use App\Livewire\Dashboard\OpsmpDashboard;
use App\Livewire\Settings\ActiveSessions;
use App\Livewire\Settings\RoleSettings;
use App\Livewire\Admin\DataSekolah;
use App\Livewire\Admin\DataSekolahSMP;
use Illuminate\Support\Facades\Route;

// Redirect home to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
});

// Auth Routes
Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/dashboard', [AuthController::class, 'redirectToDashboard'])->name('dashboard');

    // Admin Dashboard
    Route::get('/admin', AdminDashboard::class)
        ->middleware('role:admin')
        ->name('admin.dashboard');

    // Operator SD Dashboard
    Route::get('/opsd', OpsdDashboard::class)
        ->middleware('role:opsd')
        ->name('opsd.dashboard');

    // Operator SMP Dashboard
    Route::get('/opsmp', OpsmpDashboard::class)
        ->middleware('role:opsmp')
        ->name('opsmp.dashboard');



    // Admin Tools (Admin Only)
    Route::middleware('role:admin')->group(function () {
        Route::get('/settings', RoleSettings::class)->name('settings');
        Route::get('/admin/app-settings', App\Livewire\Admin\SettingApp::class)->name('admin.settings');
        Route::get('/sessions', ActiveSessions::class)->name('sessions');
        Route::get('/admin/sekolah-sd', DataSekolah::class)->name('admin.sekolah');
        Route::get('/admin/sekolah-sd/import', App\Livewire\Admin\ImportSekolahSD::class)->name('admin.sekolah-sd.import');
        Route::get('/admin/sekolah-smp', DataSekolahSMP::class)->name('admin.sekolah-smp');
        Route::get('/admin/sekolah-smp/import', App\Livewire\Admin\ImportSekolahSMP::class)->name('admin.sekolah-smp.import');
        Route::get('/admin/peserta-didik', \App\Livewire\Admin\DataPesertaDidik::class)->name('admin.peserta-didik');
        Route::get('/admin/berkas', \App\Livewire\Admin\DataBerkas::class)->name('admin.berkas');
        Route::get('/admin/berkas/{id}/fields', \App\Livewire\Admin\DataBerkasField::class)->name('admin.berkas.fields');
        Route::get('/admin/jalur', \App\Livewire\Admin\DataJalur::class)->name('admin.jalur');
        Route::get('/admin/pendaftaran', \App\Livewire\Admin\DataPendaftaran::class)->name('admin.pendaftaran');
        Route::get('/admin/pemetaan-domisili', \App\Livewire\Admin\PemetaanDomisili::class)->name('admin.pemetaan-domisili');
    });

    // OPSD Tools
    Route::middleware('role:opsd')->group(function () {
        Route::get('/opsd/siswa', \App\Livewire\Opsd\DataSiswa::class)->name('opsd.siswa');
        Route::get('/opsd/cetak-kartu/{id}', [App\Http\Controllers\PrintController::class, 'cetakKartu'])->name('opsd.cetak-kartu');
        Route::get('/opsd/cetak-kartu-massal', [App\Http\Controllers\PrintController::class, 'cetakKartuMassal'])->name('opsd.cetak-kartu-massal');
    });

    // OPSMP Tools
    Route::middleware('role:opsmp')->group(function () {
        Route::get('/opsmp/pendaftaran', \App\Livewire\Opsmp\DataPendaftaran::class)->name('opsmp.pendaftaran');
        Route::get('/opsmp/verval-berkas/{id}', \App\Livewire\Opsmp\VervalBerkasDetail::class)->name('opsmp.verval-berkas-detail');
    });
});

// Student Dashboard Routes
Route::middleware('auth:siswa')->group(function () {
    Route::get('/siswa', \App\Livewire\Dashboard\StudentDashboard::class)->name('siswa.dashboard');
    Route::get('/siswa/pendaftaran', \App\Livewire\Student\StudentRegistration::class)->name('siswa.pendaftaran');
    Route::get('/siswa/cetak-kartu/{id}', [App\Http\Controllers\PrintController::class, 'cetakKartu'])->name('print.kartu');
});
