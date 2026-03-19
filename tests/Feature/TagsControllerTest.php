<?php

use App\Models\Item;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('renders tags view sorted by name by default', function () {
    Tag::factory()->create(['name' => 'Zebra']);
    Tag::factory()->create(['name' => 'Alpha']);

    $response = get(route('tags'));

    $response->assertSuccessful();
    $tags = $response->viewData('tags');

    expect($tags->first()->name)->toBe('Alpha');
    expect($tags->last()->name)->toBe('Zebra');
});

test('sorts by count descending when sort=count_desc', function () {
    $popular = Tag::factory()->create(['name' => 'Popular']);
    $rare = Tag::factory()->create(['name' => 'Rare']);

    Item::factory()->count(3)->create(['type' => 'To-Do', 'status' => 'Open', 'is_trashed' => false])
        ->each(fn ($item) => $item->tags()->attach($popular->id));

    Item::factory()->create(['type' => 'To-Do', 'status' => 'Open', 'is_trashed' => false])
        ->tags()->attach($rare->id);

    $response = get(route('tags', ['sort' => 'count_desc']));

    $response->assertSuccessful();
    $tags = $response->viewData('tags');

    expect($tags->first()->name)->toBe('Popular');
    expect($tags->last()->name)->toBe('Rare');
});

test('persists sort preference in session', function () {
    get(route('tags', ['sort' => 'count_desc']));

    get(route('tags'));

    expect(session('tags_sort'))->toBe('count_desc');
});

test('only counts open non-trashed items in withCount', function () {
    $tag = Tag::factory()->create();

    Item::factory()->create(['type' => 'To-Do', 'status' => 'Open', 'is_trashed' => false])
        ->tags()->attach($tag->id);

    // Trashed — should not be counted
    Item::factory()->create(['type' => 'To-Do', 'status' => 'Open', 'is_trashed' => true])
        ->tags()->attach($tag->id);

    // Not open — should not be counted
    Item::factory()->create(['type' => 'To-Do', 'status' => 'Completed', 'is_trashed' => false])
        ->tags()->attach($tag->id);

    $response = get(route('tags'));

    $tags = $response->viewData('tags');

    expect($tags->first()->items_count)->toBe(1);
});

test('passes current sort to the view', function () {
    $response = get(route('tags', ['sort' => 'count_desc']));

    expect($response->viewData('sort'))->toBe('count_desc');
});
