<?php

use Illuminate\Support\Facades\Route;

// Main Domain Route - spmbsmpdisdikporacianjur.local
Route::get('/', function () {
    // Redirect to Auth domain login - Laravel will handle the subdomain automatically via the route name
    return redirect()->route('login');
});
