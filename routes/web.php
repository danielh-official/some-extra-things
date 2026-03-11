<?php

use App\Http\Controllers\Home;
use App\Http\Controllers\SmartListController;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');

Route::get('/settings', function () {
    return view('settings');
})->name('settings');

Route::resource('smart-lists', SmartListController::class);
