<?php

use Illuminate\Support\Facades\Route;

// Guest Routes for Pendaftaran Subdomain
Route::middleware('guest:siswa')->group(function () {
    Route::get('/register', \App\Livewire\Auth\RegisterMandiri::class)->name('register-mandiri');
    Route::get('/register-mandiri', \App\Livewire\Auth\RegisterMandiri::class); // Alias just in case
});

// Protected Routes
Route::middleware('auth:siswa')->group(function () {
    Route::get('/', \App\Livewire\Dashboard\StudentDashboard::class)->name('siswa.dashboard');

    // Legacy/Specific routes
    Route::get('/pendaftaran', \App\Livewire\Student\StudentRegistration::class)->name('siswa.pendaftaran');
    Route::get('/pengumuman', \App\Livewire\Student\Announcement::class)->name('siswa.pengumuman');
    Route::get('/cetak-kartu/{id}', [\App\Http\Controllers\PrintController::class, 'cetakKartu'])->name('print.kartu');
    Route::get('/cetak-bukti/{id}', [\App\Http\Controllers\PrintController::class, 'cetakBukti'])->name('print.bukti');
    Route::get('/cetak-bukti-lulus/{id}', [\App\Http\Controllers\PrintController::class, 'cetakBuktiLulus'])->name('print.bukti-lulus');
});
