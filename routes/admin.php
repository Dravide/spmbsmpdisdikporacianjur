<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard\AdminDashboard;
use App\Livewire\Settings\RoleSettings;
use App\Livewire\Settings\ActiveSessions;
use App\Livewire\Admin\DataSekolah;
use App\Livewire\Admin\DataSekolahSMP;

Route::get('/', AdminDashboard::class)->name('admin.dashboard');

Route::get('/settings', RoleSettings::class)->name('settings');
Route::get('/app-settings', \App\Livewire\Admin\SettingApp::class)->name('admin.settings');
Route::get('/jadwal', \App\Livewire\Admin\Jadwal\JadwalManager::class)->name('admin.jadwal');
Route::get('/sessions', ActiveSessions::class)->name('sessions');
Route::get('/sekolah-sd', DataSekolah::class)->name('admin.sekolah');
Route::get('/sekolah-sd/import', \App\Livewire\Admin\ImportSekolahSD::class)->name('admin.sekolah-sd.import');
Route::get('/sekolah-smp', DataSekolahSMP::class)->name('admin.sekolah-smp');
Route::get('/sekolah-smp/import', \App\Livewire\Admin\ImportSekolahSMP::class)->name('admin.sekolah-smp.import');
Route::get('/peserta-didik', \App\Livewire\Admin\DataPesertaDidik::class)->name('admin.peserta-didik');
Route::get('/berkas', \App\Livewire\Admin\DataBerkas::class)->name('admin.berkas');
Route::get('/berkas/{id}/fields', \App\Livewire\Admin\DataBerkasField::class)->name('admin.berkas.fields');
Route::get('/jalur', \App\Livewire\Admin\DataJalur::class)->name('admin.jalur');
Route::get('/pendaftaran', \App\Livewire\Admin\DataPendaftaran::class)->name('admin.pendaftaran');
Route::get('/pemetaan-domisili', \App\Livewire\Admin\PemetaanDomisili::class)->name('admin.pemetaan-domisili');
Route::get('/eligible-siswa-domisili', \App\Livewire\Admin\EligibleSiswaDomisili::class)->name('admin.eligible-siswa-domisili');
Route::get('/daya-tampung', \App\Livewire\Admin\DataDayaTampung::class)->name('admin.daya-tampung');
Route::get('/data-admin', \App\Livewire\Admin\DataAdmin::class)->name('admin.data-admin');
