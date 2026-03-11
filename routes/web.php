<?php

use App\Http\Controllers\Home;
use App\Http\Controllers\SmartListController;
use App\Http\Controllers\ToggleServerState;
use App\Support\ServerState;
use Illuminate\Support\Facades\Route;

Route::get('/', Home::class)->name('home');

Route::get('/settings', function () {
    return view('settings', [
        'serverEnabled' => ServerState::isEnabled(),
    ]);
})->name('settings');

Route::post('/server/toggle', ToggleServerState::class)
    ->name('server.toggle');

Route::resource('smart-lists', SmartListController::class);
