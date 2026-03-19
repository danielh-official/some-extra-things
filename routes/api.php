<?php

use App\Http\Controllers\Api\ItemController;
use App\Http\Middleware\EnsureApiToken;
use App\Http\Middleware\EnsureLocalhost;
use Illuminate\Support\Facades\Route;

Route::name('api.')->middleware([EnsureLocalhost::class, EnsureApiToken::class])->group(function () {
    Route::apiResource('items', ItemController::class)->only(['index', 'store']);
});
