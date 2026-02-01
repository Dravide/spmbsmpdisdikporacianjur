<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard\OpsmpDashboard;

Route::get('/opsmp', OpsmpDashboard::class)->name('opsmp.dashboard');

Route::get('/opsmp/pendaftaran', \App\Livewire\Opsmp\DataPendaftaran::class)->name('opsmp.pendaftaran');
Route::get('/opsmp/verval-berkas/{id}', \App\Livewire\Opsmp\VervalBerkasDetail::class)->name('opsmp.verval-berkas-detail');
Route::get('/opsmp/jalur-verified', \App\Livewire\Opsmp\JalurVerified::class)->name('opsmp.jalur-verified');
Route::get('/opsmp/jalur-verified/{id}', \App\Livewire\Opsmp\JalurVerifiedDetail::class)->name('opsmp.jalur-verified.detail');
Route::get('/opsmp/pemetaan-domisili', \App\Livewire\Opsmp\PemetaanDomisili::class)->name('opsmp.pemetaan-domisili');
Route::get('/opsmp/daya-tampung', \App\Livewire\Opsmp\DayaTampung::class)->name('opsmp.daya-tampung');
