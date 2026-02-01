<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard\OpsdDashboard;

Route::get('/opsd', OpsdDashboard::class)->name('opsd.dashboard');

Route::get('/opsd/siswa', \App\Livewire\Opsd\DataSiswa::class)->name('opsd.siswa');
Route::get('/opsd/cetak-kartu/{id}', [\App\Http\Controllers\PrintController::class, 'cetakKartu'])->name('opsd.cetak-kartu');
Route::get('/opsd/cetak-kartu-massal', [\App\Http\Controllers\PrintController::class, 'cetakKartuMassal'])->name('opsd.cetak-kartu-massal');
