<?php

use App\Models\Item;
use App\Models\SmartList;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('show renders items grouped by bucket', function () {
    $tag = Tag::factory()->create();

    $item = Item::factory()->create([
        'type' => 'To-Do',
        'status' => 'Open',
        'is_trashed' => false,
        'is_inbox' => false,
        'is_logged' => false,
        'start_date' => null,
        'start' => null,
    ]);
    $item->tags()->attach($tag->id);

    $smartList = SmartList::create([
        'name' => 'Tagged',
        'criteria' => ['type' => 'tag', 'tag' => $tag->name, 'operator' => 'equals'],
    ]);

    $response = get(route('smart-lists.show', $smartList));

    $response->assertSuccessful();
    $grouped = $response->viewData('grouped');
    $allItems = $grouped->flatten();
    expect($allItems->pluck('id'))->toContain($item->id);
});

test('show excludes trashed items', function () {
    $tag = Tag::factory()->create();

    $trashed = Item::factory()->create([
        'type' => 'To-Do',
        'is_trashed' => true,
    ]);
    $trashed->tags()->attach($tag->id);

    $smartList = SmartList::create([
        'name' => 'Tagged',
        'criteria' => ['type' => 'tag', 'tag' => $tag->name, 'operator' => 'equals'],
    ]);

    $response = get(route('smart-lists.show', $smartList));

    $grouped = $response->viewData('grouped');
    expect($grouped->flatten()->pluck('id'))->not->toContain($trashed->id);
});
