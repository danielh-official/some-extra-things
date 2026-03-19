<?php

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\delete;

uses(RefreshDatabase::class);

test('marks all items as trashed', function () {
    $itemA = Item::factory()->create(['type' => 'To-Do', 'is_trashed' => false]);
    $itemB = Item::factory()->create(['type' => 'Project', 'is_trashed' => false]);

    delete(route('settings.items.destroy'));

    assertDatabaseHas('items', ['id' => $itemA->id, 'is_trashed' => true]);
    assertDatabaseHas('items', ['id' => $itemB->id, 'is_trashed' => true]);
});

test('redirects to settings after deleting all items', function () {
    delete(route('settings.items.destroy'))
        ->assertRedirect(route('settings'));
});
