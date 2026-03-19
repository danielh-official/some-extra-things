<?php

use App\Http\Middleware\EnsureApiToken;
use App\Http\Middleware\EnsureLocalhost;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\withoutMiddleware;

uses(RefreshDatabase::class);

test('index returns all items as a JSON collection', function () {
    $itemA = Item::factory()->create(['type' => 'To-Do', 'title' => 'Alpha']);
    $itemB = Item::factory()->create(['type' => 'Project', 'title' => 'Beta']);

    $response = withoutMiddleware([EnsureLocalhost::class, EnsureApiToken::class])
        ->getJson(route('api.items.index'));

    $response->assertOk();
    $response->assertJsonFragment(['id' => $itemA->id]);
    $response->assertJsonFragment(['id' => $itemB->id]);
});

test('index returns empty collection when no items exist', function () {
    $response = withoutMiddleware([EnsureLocalhost::class, EnsureApiToken::class])
        ->getJson(route('api.items.index'));

    $response->assertOk();
    $response->assertJson(['data' => []]);
});
