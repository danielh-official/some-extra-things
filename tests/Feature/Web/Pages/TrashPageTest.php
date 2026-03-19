<?php

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('returns 200 for /trash', function () {
    get('/trash')->assertSuccessful();
});

it('shows trashed items on /trash', function () {
    $trashedItem = Item::factory()->create(['type' => 'To-Do', 'is_trashed' => true]);
    $openItem = Item::factory()->create(['type' => 'To-Do', 'is_trashed' => false]);

    get('/trash')
        ->assertSee($trashedItem->title)
        ->assertDontSee($openItem->title);
});
