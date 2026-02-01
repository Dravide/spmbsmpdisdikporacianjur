<?php

use Illuminate\Support\Facades\Route;

// Main Domain Route - spmbsmpdisdikporacianjur.local
Route::get('/', function () {
    // Redirect to Auth domain login
    return redirect()->to('http://auth.spmbsmpdisdikporacianjur.local/login');
});
