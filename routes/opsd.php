<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard\OpsdDashboard;

Route::get('/', OpsdDashboard::class)->name('opsd.dashboard');

Route::get('/siswa', \App\Livewire\Opsd\DataSiswa::class)->name('opsd.siswa');
Route::get('/cetak-kartu/{id}', [\App\Http\Controllers\PrintController::class, 'cetakKartu'])->name('opsd.cetak-kartu');
Route::get('/cetak-kartu-massal', [\App\Http\Controllers\PrintController::class, 'cetakKartuMassal'])->name('opsd.cetak-kartu-massal');
