<?php

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\getJson;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

test('it creates a thing via post', function () {
    $payload = [
        'id' => Str::uuid(),
        'type' => 'To-Do',
        'title' => 'Test Thing',
        'status' => 'Open',
    ];

    $response = postJson(route('api.items.store'), $payload);

    $response->assertCreated();
    $response->assertJsonFragment([
        'type' => 'To-Do',
        'title' => 'Test Thing',
        'status' => 'Open',
    ]);

    assertDatabaseHas('items', [
        'title' => 'Test Thing',
        'type' => 'To-Do',
    ]);
});

test('post with existing id upserts a thing', function () {
    $item = Item::factory()->create([
        'type' => 'To-Do',
        'title' => 'Original',
    ]);

    $payload = [
        'id' => $item->id,
        'type' => 'To-Do',
        'title' => 'Updated',
        'status' => 'Open',
    ];

    $response = postJson(route('api.items.store'), $payload);

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $item->id,
        'title' => 'Updated',
    ]);

    assertDatabaseHas('items', [
        'id' => $item->id,
        'title' => 'Updated',
    ]);
});

test('validation fails for invalid type', function () {
    $payload = [
        'id' => Str::uuid(),
        'type' => 'Invalid',
        'title' => 'Test',
    ];

    $response = postJson(route('api.items.store'), $payload);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['type']);
});

// ─── index ───────────────────────────────────────────

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
