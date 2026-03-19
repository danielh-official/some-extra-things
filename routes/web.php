<?php

use App\Http\Controllers\AllItems;
use App\Http\Controllers\AreaController;
use App\Http\Controllers\DeleteAllItems;
use App\Http\Controllers\Logbook;
use App\Http\Controllers\PermanentlyDeleteTrashedItems;
use App\Http\Controllers\SaveThemeSetting;
use App\Http\Controllers\Settings;
use App\Http\Controllers\ShowItem;
use App\Http\Controllers\ShowTag;
use App\Http\Controllers\SmartListController;
use App\Http\Controllers\SyncTags;
use App\Http\Controllers\TagController;
use App\Http\Controllers\Tags;
use App\Http\Controllers\Trash;
use App\Http\Controllers\TrashItem;
use Illuminate\Support\Facades\Route;

Route::redirect('/', '/all');
Route::get('/all', AllItems::class)->name('all');
Route::post('/all/kanban', [AllItems::class, 'toggleKanban'])->name('all.kanban');
Route::get('/logbook', Logbook::class)->name('logbook');
Route::get('/trash', Trash::class)->name('trash');

Route::get('/tags', Tags::class)->name('tags');
Route::post('/tags/sync', SyncTags::class)->name('tags.sync');
Route::get('/tags/{tag}/edit', [TagController::class, 'edit'])->name('tags.edit');
Route::patch('/tags/{tag}', [TagController::class, 'update'])->name('tags.update');
Route::get('/tags/{tag}', ShowTag::class)->name('tags.show');
Route::get('/settings', Settings::class)->name('settings');

Route::post('/settings/theme', SaveThemeSetting::class)->name('settings.theme.update');
Route::delete('/settings/items', DeleteAllItems::class)->name('settings.items.destroy');
Route::delete('/trash/items', PermanentlyDeleteTrashedItems::class)->name('trash.items.destroy');

Route::get('/areas/{area}', [AreaController::class, 'show'])->name('areas.show');
Route::get('/projects/{item}', ShowItem::class)->name('projects.show');
Route::get('/todos/{item}', ShowItem::class)->name('todos.show');
Route::delete('/items/{item}/trash', TrashItem::class)->name('items.trash');
Route::get('/smart-lists/{smart_list}/duplicate', [SmartListController::class, 'duplicate'])->name('smart-lists.duplicate');
Route::post('/smart-lists/{smart_list}/kanban', [SmartListController::class, 'toggleKanban'])->name('smart-lists.kanban');
Route::post('/smart-lists/{smart_list}/pin', [SmartListController::class, 'togglePin'])->name('smart-lists.pin');
Route::resource('smart-lists', SmartListController::class);
