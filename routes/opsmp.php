<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard\OpsmpDashboard;

Route::get('/', OpsmpDashboard::class)->name('opsmp.dashboard');

Route::get('/pendaftaran', \App\Livewire\Opsmp\DataPendaftaran::class)->name('opsmp.pendaftaran');
Route::get('/verval-berkas/{id}', \App\Livewire\Opsmp\VervalBerkasDetail::class)->name('opsmp.verval-berkas-detail');
Route::get('/jalur-verified', \App\Livewire\Opsmp\JalurVerified::class)->name('opsmp.jalur-verified');
Route::get('/jalur-verified/{id}', \App\Livewire\Opsmp\JalurVerifiedDetail::class)->name('opsmp.jalur-verified.detail');
Route::get('/pemetaan-domisili', \App\Livewire\Opsmp\PemetaanDomisili::class)->name('opsmp.pemetaan-domisili');
Route::get('/daya-tampung', \App\Livewire\Opsmp\DayaTampung::class)->name('opsmp.daya-tampung');
Route::get('/pengumuman', \App\Livewire\Opsmp\ListPengumuman::class)->name('opsmp.pengumuman');
Route::get('/daftar-ulang', \App\Livewire\Opsmp\DaftarUlang::class)->name('opsmp.daftar-ulang');
