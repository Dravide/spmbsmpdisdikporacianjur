<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Livewire\Auth\Login;

Route::middleware('guest')->group(function () {
    Route::get('/', Login::class)->name('login');
    Route::get('/login', Login::class);
});

Route::middleware(['auth', 'two-factor'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // 2FA Routes
    Route::get('/two-factor/setup', \App\Livewire\Auth\TwoFactorSetup::class)->name('two-factor.setup');
    Route::get('/two-factor/challenge', \App\Livewire\Auth\TwoFactorChallenge::class)->name('two-factor.challenge');

    // Redirect hub
    Route::get('/dashboard', [AuthController::class, 'redirectToDashboard'])->name('dashboard');
});
