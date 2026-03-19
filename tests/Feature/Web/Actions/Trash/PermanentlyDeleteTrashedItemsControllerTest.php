<?php

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\delete;

uses(RefreshDatabase::class);

test('permanently deletes trashed items', function () {
    $trashed = Item::factory()->create(['type' => 'To-Do', 'is_trashed' => true]);

    delete(route('trash.items.destroy'));

    assertDatabaseMissing('items', ['id' => $trashed->id]);
});

test('does not delete non-trashed items', function () {
    $active = Item::factory()->create(['type' => 'To-Do', 'is_trashed' => false]);

    delete(route('trash.items.destroy'));

    assertDatabaseHas('items', ['id' => $active->id]);
});

test('redirects to trash after permanent deletion', function () {
    delete(route('trash.items.destroy'))
        ->assertRedirect(route('trash.index'));
});
