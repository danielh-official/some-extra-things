<?php

use App\Http\Middleware\EnsureApiToken;
use App\Http\Middleware\EnsureLocalhost;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\withoutMiddleware;

uses(RefreshDatabase::class);

test('it creates a thing via post', function () {
    $payload = [
        'id' => Str::uuid(),
        'type' => 'To-Do',
        'title' => 'Test Thing',
        'status' => 'Open',
    ];

    $response = withoutMiddleware([EnsureLocalhost::class, EnsureApiToken::class])
        ->postJson(route('api.items.store'), $payload);

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

    $response = withoutMiddleware([EnsureLocalhost::class, EnsureApiToken::class])
        ->postJson(route('api.items.store'), $payload);

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

    $response = withoutMiddleware([EnsureLocalhost::class, EnsureApiToken::class])
        ->postJson(route('api.items.store'), $payload);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['type']);
});
