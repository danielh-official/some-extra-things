<?php

use App\Models\SmartList;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\patch;

uses(RefreshDatabase::class);

test('togglePin sets is_pinned_to_sidebar to true when currently false', function () {
    $smartList = SmartList::factory()->create(['is_pinned_to_sidebar' => false]);

    patch(route('smart-lists.pin', $smartList))
        ->assertRedirect(route('smart-lists.show', $smartList));

    assertDatabaseHas('smart_lists', [
        'id' => $smartList->id,
        'is_pinned_to_sidebar' => true,
    ]);
});

test('togglePin sets is_pinned_to_sidebar to false when currently true', function () {
    $smartList = SmartList::factory()->create(['is_pinned_to_sidebar' => true]);

    patch(route('smart-lists.pin', $smartList));

    assertDatabaseHas('smart_lists', [
        'id' => $smartList->id,
        'is_pinned_to_sidebar' => false,
    ]);
});
