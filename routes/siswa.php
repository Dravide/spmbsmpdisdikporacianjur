<?php

use Illuminate\Support\Facades\Route;

Route::get('/siswa', \App\Livewire\Dashboard\StudentDashboard::class)->name('siswa.dashboard');
Route::get('/siswa/pendaftaran', \App\Livewire\Student\StudentRegistration::class)->name('siswa.pendaftaran');
Route::get('/siswa/cetak-kartu/{id}', [\App\Http\Controllers\PrintController::class, 'cetakKartu'])->name('print.kartu');
