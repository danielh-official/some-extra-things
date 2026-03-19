<?php

use App\Http\Controllers\AllItems;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\DeleteAllItems;
use App\Http\Controllers\Logbook;
use App\Http\Controllers\PermanentlyDeleteTrashedItems;
use App\Http\Controllers\SaveThemeSetting;
use App\Http\Controllers\Settings;
use App\Http\Controllers\ShowItem;
use App\Http\Controllers\SmartListController;
use App\Http\Controllers\SyncTags;
use App\Http\Controllers\TagController;
use App\Http\Controllers\ToggleTagEdits;
use App\Http\Controllers\Trash;
use App\Http\Controllers\TrashItem;
use Illuminate\Support\Facades\Route;

// MARK: Redirects
Route::redirect('/', '/all');

// MARK: All page routes
Route::name('all.')->prefix('all')->group(function () {
    Route::get('/', AllItems::class)->name('index');
    Route::post('/kanban', [AllItems::class, 'toggleKanban'])->name('kanban');
});

// MARK: Smart list routes
Route::resource('smart-lists', SmartListController::class);
Route::name('smart-lists.')->prefix('smart-lists/{smart_list}')->group(function () {
    Route::get('/duplicate', [SmartListController::class, 'duplicate'])->name('duplicate');
    Route::patch('/kanban', [SmartListController::class, 'toggleKanban'])->name('kanban');
    Route::patch('/pin', [SmartListController::class, 'togglePin'])->name('pin');
});

// MARK: Logbook route
Route::get('/logbook', Logbook::class)->name('logbook');

// MARK: Trash routes
Route::name('trash.')->prefix('trash')->group(function () {
    Route::get('/', Trash::class)->name('index');
    Route::delete('/items', PermanentlyDeleteTrashedItems::class)->name('items.destroy');
});

// MARK: Tag routes
Route::post('/tags/sync', SyncTags::class)->name('tags.sync');
Route::resource('tags', TagController::class)->only('index', 'show', 'edit', 'update');

// MARK: Settings routes
Route::name('settings.')->prefix('settings')->group(function () {
    Route::get('/', Settings::class)->name('index');
    Route::post('/theme', SaveThemeSetting::class)->name('theme.update');
    Route::post('/tag-edits', ToggleTagEdits::class)->name('tag-edits.toggle');
    Route::delete('/items', DeleteAllItems::class)->name('items.destroy');
});

// MARK: Item detail routes
Route::get('/areas/{area}', [AreaController::class, 'show'])->name('areas.show');
Route::get('/projects/{item}', ShowItem::class)->name('projects.show');
Route::get('/todos/{item}', ShowItem::class)->name('todos.show');

// MARK: Item soft delete route
Route::delete('/items/{item}/trash', TrashItem::class)->name('items.trash');
