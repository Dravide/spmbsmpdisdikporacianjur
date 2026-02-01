<?php

use Illuminate\Support\Facades\Route;
use App\Livewire\Dashboard\AdminDashboard;
use App\Livewire\Settings\RoleSettings;
use App\Livewire\Settings\ActiveSessions;
use App\Livewire\Admin\DataSekolah;
use App\Livewire\Admin\DataSekolahSMP;

Route::get('/admin', AdminDashboard::class)->name('admin.dashboard');

Route::get('/settings', RoleSettings::class)->name('settings');
Route::get('/admin/app-settings', \App\Livewire\Admin\SettingApp::class)->name('admin.settings');
Route::get('/sessions', ActiveSessions::class)->name('sessions');
Route::get('/admin/sekolah-sd', DataSekolah::class)->name('admin.sekolah');
Route::get('/admin/sekolah-sd/import', \App\Livewire\Admin\ImportSekolahSD::class)->name('admin.sekolah-sd.import');
Route::get('/admin/sekolah-smp', DataSekolahSMP::class)->name('admin.sekolah-smp');
Route::get('/admin/sekolah-smp/import', \App\Livewire\Admin\ImportSekolahSMP::class)->name('admin.sekolah-smp.import');
Route::get('/admin/peserta-didik', \App\Livewire\Admin\DataPesertaDidik::class)->name('admin.peserta-didik');
Route::get('/admin/berkas', \App\Livewire\Admin\DataBerkas::class)->name('admin.berkas');
Route::get('/admin/berkas/{id}/fields', \App\Livewire\Admin\DataBerkasField::class)->name('admin.berkas.fields');
Route::get('/admin/jalur', \App\Livewire\Admin\DataJalur::class)->name('admin.jalur');
Route::get('/admin/pendaftaran', \App\Livewire\Admin\DataPendaftaran::class)->name('admin.pendaftaran');
Route::get('/admin/pemetaan-domisili', \App\Livewire\Admin\PemetaanDomisili::class)->name('admin.pemetaan-domisili');
Route::get('/admin/daya-tampung', \App\Livewire\Admin\DataDayaTampung::class)->name('admin.daya-tampung');
Route::get('/admin/data-admin', \App\Livewire\Admin\DataAdmin::class)->name('admin.data-admin');
