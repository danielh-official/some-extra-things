<?php

use App\Http\Controllers\ItemController;
use App\Http\Middleware\EnsureApiServerEnabled;
use Illuminate\Support\Facades\Route;

Route::apiResource('items', ItemController::class)->only(['store']);
