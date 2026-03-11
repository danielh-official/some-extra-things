<?php

use App\Models\Item;
use App\Support\ServerState;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

uses(RefreshDatabase::class);

beforeEach(function () {
    ServerState::setEnabled(true);
});

test('it creates a thing via post', function () {
    $payload = [
        'id' => Str::uuid(),
        'type' => 'To-Do',
        'title' => 'Test Thing',
        'status' => 'Open',
    ];

    $response = $this->postJson(route('api.items.store'), $payload);

    $response->assertCreated();
    $response->assertJsonFragment([
        'type' => 'To-Do',
        'title' => 'Test Thing',
        'status' => 'Open',
    ]);

    $this->assertDatabaseHas('items', [
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

    $response = $this->postJson(route('api.items.store'), $payload);

    $response->assertOk();
    $response->assertJsonFragment([
        'id' => $item->id,
        'title' => 'Updated',
    ]);

    $this->assertDatabaseHas('items', [
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

    $response = $this->postJson(route('api.items.store'), $payload);

    $response->assertUnprocessable();
    $response->assertJsonValidationErrors(['type']);
});

test('api returns service unavailable when server is disabled', function () {
    ServerState::setEnabled(false);

    $payload = [
        'type' => 'To-Do',
        'title' => 'Won’t be created',
    ];

    $response = $this->postJson(route('api.items.store'), $payload);

    $response->assertStatus(503);
    $response->assertJsonFragment([
        'message' => 'Local Things API server is disabled.',
    ]);
});

