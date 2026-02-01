<?php

use App\Http\Controllers\AuthController;
use App\Livewire\Auth\Login;
use Illuminate\Support\Facades\Route;

// Redirect home to login
Route::get('/', function () {
    return redirect()->route('login');
});

// Guest Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', Login::class)->name('login');
    Route::get('/register-mandiri', \App\Livewire\Auth\RegisterMandiri::class)->name('register-mandiri');
});

// Auth Routes
Route::middleware(['auth', 'two-factor'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

    // 2FA Routes
    Route::get('/two-factor/setup', \App\Livewire\Auth\TwoFactorSetup::class)->name('two-factor.setup');
    Route::get('/two-factor/challenge', \App\Livewire\Auth\TwoFactorChallenge::class)->name('two-factor.challenge');
    Route::get('/dashboard', [AuthController::class, 'redirectToDashboard'])->name('dashboard');
});
