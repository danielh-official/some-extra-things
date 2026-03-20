<?php

use App\Http\Controllers\Actions\DeleteAllItems;
use App\Http\Controllers\Actions\GenerateApiToken;
use App\Http\Controllers\Actions\ImportThings;
use App\Http\Controllers\Actions\PermanentlyDeleteTrashedItems;
use App\Http\Controllers\Actions\SaveThemeSetting;
use App\Http\Controllers\Actions\SyncTags;
use App\Http\Controllers\Actions\ToggleTagEdits;
use App\Http\Controllers\Actions\TrashItem;
use App\Http\Controllers\Pages\All;
use App\Http\Controllers\Pages\Item\Area;
use App\Http\Controllers\Pages\Item\Project;
use App\Http\Controllers\Pages\Item\Todo;
use App\Http\Controllers\Pages\Logbook;
use App\Http\Controllers\Pages\Settings;
use App\Http\Controllers\Pages\Trash;
use App\Http\Controllers\SmartListController;
use App\Http\Controllers\TagController;
use Illuminate\Support\Facades\Route;

// MARK: Redirects
Route::redirect('/', '/all');

// MARK: All page routes
Route::name('all.')->prefix('all')->group(function () {
    Route::get('/', All::class)->name('index');
    Route::post('/kanban', [All::class, 'toggleKanban'])->name('kanban');
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
    Route::post('/api-token', GenerateApiToken::class)->name('api-token.generate');
    Route::delete('/items', DeleteAllItems::class)->name('items.destroy');
});

// MARK: Item detail routes
Route::get('/areas/{area}', Area::class)->name('areas.show');
Route::get('/projects/{item}', Project::class)->name('projects.show');
Route::get('/todos/{item}', Todo::class)->name('todos.show');

// MARK: Item import route
Route::post('/items/import', ImportThings::class)->name('items.import');

// MARK: Item soft delete route
Route::delete('/items/{item}/trash', TrashItem::class)->name('items.trash');
