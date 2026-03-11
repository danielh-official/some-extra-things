<?php

use App\Http\Controllers\ItemController;
use App\Http\Middleware\EnsureApiServerEnabled;
use Illuminate\Support\Facades\Route;

Route::middleware(EnsureApiServerEnabled::class)->apiResource('items', ItemController::class)->only(['show', 'store', 'update']);
