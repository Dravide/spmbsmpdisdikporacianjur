<?php

use Illuminate\Support\Facades\Route;

// Main Domain Route - spmbsmpdisdikporacianjur.local
// Main Domain Route - spmbsmpdisdikporacianjur.local
Route::get('/', function () {
    $siteMode = function_exists('get_setting')
        ? get_setting('site_mode', 'normal')
        : 'normal';

    if ($siteMode === 'maintenance') {
        // You can create a dedicated maintenance view or use abort(503)
        // return view('errors.maintenance'); 
        abort(503, 'Situs sedang dalam perbaikan.');
    }

    if ($siteMode === 'coming_soon') {
        return app(\App\Livewire\Publik\LandingPage::class);
    }

    // Normal mode -> Login
    return redirect()->route('login');
})->name('home');

// Landing page route (Livewire component)
Route::get('/landing', \App\Livewire\Publik\LandingPage::class)->name('landing');
