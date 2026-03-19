<?php

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\getJson;

uses(RefreshDatabase::class);

test('index returns all items as a JSON collection', function () {
    $itemA = Item::factory()->create(['type' => 'To-Do', 'title' => 'Alpha']);
    $itemB = Item::factory()->create(['type' => 'Project', 'title' => 'Beta']);

    $response = getJson(route('api.items.index'));

    $response->assertOk();
    $response->assertJsonFragment(['id' => $itemA->id]);
    $response->assertJsonFragment(['id' => $itemB->id]);
});

test('index returns empty collection when no items exist', function () {
    $response = getJson(route('api.items.index'));

    $response->assertOk();
    $response->assertJson(['data' => []]);
});
