<?php

use App\Http\Controllers\Anytime;
use App\Http\Controllers\DeleteAllItems;
use App\Http\Controllers\Inbox;
use App\Http\Controllers\LaterProjects;
use App\Http\Controllers\Logbook;
use App\Http\Controllers\PermanentlyDeleteTrashedItems;
use App\Http\Controllers\ShowItemController;
use App\Http\Controllers\SmartListController;
use App\Http\Controllers\Someday;
use App\Http\Controllers\Tags;
use App\Http\Controllers\Today;
use App\Http\Controllers\Trash;
use App\Http\Controllers\Upcoming;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/inbox');
Route::get('/inbox', Inbox::class)->name('inbox');
Route::get('/today', Today::class)->name('today');
Route::get('/upcoming', Upcoming::class)->name('upcoming');
Route::get('/anytime', Anytime::class)->name('anytime');
Route::get('/someday', Someday::class)->name('someday');
Route::get('/logbook', Logbook::class)->name('logbook');
Route::get('/trash', Trash::class)->name('trash');

Route::get('/tags', Tags::class)->name('tags');
Route::get('/later-projects', LaterProjects::class)->name('later-projects');

Route::get('/settings', function () {
    return view('settings');
})->name('settings');

Route::delete('/settings/items', DeleteAllItems::class)->name('settings.items.destroy');
Route::delete('/trash/items', PermanentlyDeleteTrashedItems::class)->name('trash.items.destroy');

Route::get('/items/{item}', ShowItemController::class)->name('items.show');
Route::get('/smart-lists/{smart_list}/duplicate', [SmartListController::class, 'duplicate'])->name('smart-lists.duplicate');
Route::resource('smart-lists', SmartListController::class);
