<?php

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\delete;

uses(RefreshDatabase::class);

test('marks item as trashed', function () {
    $item = Item::factory()->create(['type' => 'To-Do', 'is_trashed' => false]);

    delete(route('items.trash', $item));

    assertDatabaseHas('items', [
        'id' => $item->id,
        'is_trashed' => true,
    ]);
});

test('redirects back after trashing', function () {
    $item = Item::factory()->create(['type' => 'To-Do', 'is_trashed' => false]);

    delete(route('items.trash', $item))
        ->assertRedirect();
});
