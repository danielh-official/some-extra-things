<?php

use App\Models\Item;
use App\Models\SmartList;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

// MARK: itemsQuery — no criteria

test('itemsQuery with no criteria returns all non-trashed items', function () {
    $active = Item::factory()->create(['is_trashed' => false]);
    $trashed = Item::factory()->create(['is_trashed' => true]);

    $list = SmartList::factory()->create(['criteria' => null]);

    $ids = $list->itemsQuery()->pluck('id');

    expect($ids)->toContain($active->id);
    expect($ids)->not->toContain($trashed->id);
});

test('itemsQuery with empty criteria array returns all non-trashed items', function () {
    $active = Item::factory()->create(['is_trashed' => false]);

    $list = SmartList::factory()->create(['criteria' => []]);

    expect($list->itemsQuery()->pluck('id'))->toContain($active->id);
});

// MARK: itemsQuery — tag equals / not_equals

test('itemsQuery tag equals returns only items with the tag', function () {
    $tag = Tag::factory()->create(['name' => 'Work']);
    $tagged = Item::factory()->create(['is_trashed' => false]);
    $untagged = Item::factory()->create(['is_trashed' => false]);
    $tagged->tags()->attach($tag->id);

    $list = SmartList::factory()->create([
        'criteria' => ['type' => 'tag', 'tag' => 'Work', 'operator' => 'equals'],
    ]);

    $ids = $list->itemsQuery()->pluck('id');

    expect($ids)->toContain($tagged->id);
    expect($ids)->not->toContain($untagged->id);
});

test('itemsQuery tag not_equals returns only items without the tag', function () {
    $tag = Tag::factory()->create(['name' => 'Work']);
    $tagged = Item::factory()->create(['is_trashed' => false]);
    $untagged = Item::factory()->create(['is_trashed' => false]);
    $tagged->tags()->attach($tag->id);

    $list = SmartList::factory()->create([
        'criteria' => ['type' => 'tag', 'tag' => 'Work', 'operator' => 'not_equals'],
    ]);

    $ids = $list->itemsQuery()->pluck('id');

    expect($ids)->toContain($untagged->id);
    expect($ids)->not->toContain($tagged->id);
});

test('itemsQuery tag condition with missing tag or operator does nothing', function () {
    $item = Item::factory()->create(['is_trashed' => false]);

    $list = SmartList::factory()->create([
        'criteria' => ['type' => 'tag'],
    ]);

    expect($list->itemsQuery()->pluck('id'))->toContain($item->id);
});

// MARK: itemsQuery — group logic

test('itemsQuery and-group returns items matching all conditions', function () {
    $tagA = Tag::factory()->create(['name' => 'Alpha']);
    $tagB = Tag::factory()->create(['name' => 'Beta']);

    $both = Item::factory()->create(['is_trashed' => false]);
    $onlyA = Item::factory()->create(['is_trashed' => false]);
    $neither = Item::factory()->create(['is_trashed' => false]);

    $both->tags()->attach([$tagA->id, $tagB->id]);
    $onlyA->tags()->attach($tagA->id);

    $list = SmartList::factory()->create([
        'criteria' => [
            'type' => 'group',
            'logic' => 'and',
            'conditions' => [
                ['type' => 'tag', 'tag' => 'Alpha', 'operator' => 'equals'],
                ['type' => 'tag', 'tag' => 'Beta', 'operator' => 'equals'],
            ],
        ],
    ]);

    $ids = $list->itemsQuery()->pluck('id');

    expect($ids)->toContain($both->id);
    expect($ids)->not->toContain($onlyA->id);
    expect($ids)->not->toContain($neither->id);
});

test('itemsQuery or-group returns items matching any condition', function () {
    $tagA = Tag::factory()->create(['name' => 'Alpha']);
    $tagB = Tag::factory()->create(['name' => 'Beta']);

    $onlyA = Item::factory()->create(['is_trashed' => false]);
    $onlyB = Item::factory()->create(['is_trashed' => false]);
    $neither = Item::factory()->create(['is_trashed' => false]);

    $onlyA->tags()->attach($tagA->id);
    $onlyB->tags()->attach($tagB->id);

    $list = SmartList::factory()->create([
        'criteria' => [
            'type' => 'group',
            'logic' => 'or',
            'conditions' => [
                ['type' => 'tag', 'tag' => 'Alpha', 'operator' => 'equals'],
                ['type' => 'tag', 'tag' => 'Beta', 'operator' => 'equals'],
            ],
        ],
    ]);

    $ids = $list->itemsQuery()->pluck('id');

    expect($ids)->toContain($onlyA->id);
    expect($ids)->toContain($onlyB->id);
    expect($ids)->not->toContain($neither->id);
});

test('itemsQuery group with empty conditions does nothing', function () {
    $item = Item::factory()->create(['is_trashed' => false]);

    $list = SmartList::factory()->create([
        'criteria' => ['type' => 'group', 'logic' => 'and', 'conditions' => []],
    ]);

    expect($list->itemsQuery()->pluck('id'))->toContain($item->id);
});

// MARK: itemsQuery — invert

test('itemsQuery invert returns items not matching the criteria', function () {
    $tag = Tag::factory()->create(['name' => 'Work']);
    $tagged = Item::factory()->create(['is_trashed' => false]);
    $untagged = Item::factory()->create(['is_trashed' => false]);
    $tagged->tags()->attach($tag->id);

    $list = SmartList::factory()->create([
        'criteria' => ['type' => 'tag', 'tag' => 'Work', 'operator' => 'equals'],
    ]);

    $ids = $list->itemsQuery(invert: true)->pluck('id');

    expect($ids)->toContain($untagged->id);
    expect($ids)->not->toContain($tagged->id);
});

test('itemsQuery invert with no criteria returns all non-trashed items', function () {
    $active = Item::factory()->create(['is_trashed' => false]);
    $trashed = Item::factory()->create(['is_trashed' => true]);

    $list = SmartList::factory()->create(['criteria' => null]);

    $ids = $list->itemsQuery(invert: true)->pluck('id');

    expect($ids)->toContain($active->id);
    expect($ids)->not->toContain($trashed->id);
});

// MARK: itemsCount

test('itemsCount returns the number of matching items', function () {
    $tag = Tag::factory()->create(['name' => 'Work']);

    Item::factory()->count(3)->create(['is_trashed' => false])
        ->each(fn ($item) => $item->tags()->attach($tag->id));

    Item::factory()->create(['is_trashed' => false]);

    $list = SmartList::factory()->create([
        'criteria' => ['type' => 'tag', 'tag' => 'Work', 'operator' => 'equals'],
    ]);

    expect($list->itemsCount())->toBe(3);
});

// MARK: todayCount

test('todayCount counts matching items with start_date of today', function () {
    $tag = Tag::factory()->create(['name' => 'Work']);

    $today = Item::factory()->create([
        'is_trashed' => false,
        'is_inbox' => false,
        'is_logged' => false,
        'start_date' => today(),
    ]);
    $future = Item::factory()->create([
        'is_trashed' => false,
        'is_inbox' => false,
        'is_logged' => false,
        'start_date' => today()->addDay(),
    ]);
    $noDate = Item::factory()->create([
        'is_trashed' => false,
        'is_inbox' => false,
        'is_logged' => false,
        'start_date' => null,
    ]);

    $today->tags()->attach($tag->id);
    $future->tags()->attach($tag->id);
    $noDate->tags()->attach($tag->id);

    $list = SmartList::factory()->create([
        'criteria' => ['type' => 'tag', 'tag' => 'Work', 'operator' => 'equals'],
    ]);

    expect($list->todayCount())->toBe(1);
});

test('todayCount excludes inbox items', function () {
    $tag = Tag::factory()->create(['name' => 'Work']);

    $inbox = Item::factory()->create([
        'is_trashed' => false,
        'is_inbox' => true,
        'is_logged' => false,
        'start_date' => today(),
    ]);
    $inbox->tags()->attach($tag->id);

    $list = SmartList::factory()->create([
        'criteria' => ['type' => 'tag', 'tag' => 'Work', 'operator' => 'equals'],
    ]);

    expect($list->todayCount())->toBe(0);
});

test('todayCount excludes logged items', function () {
    $tag = Tag::factory()->create(['name' => 'Work']);

    $logged = Item::factory()->create([
        'is_trashed' => false,
        'is_inbox' => false,
        'is_logged' => true,
        'start_date' => today(),
    ]);
    $logged->tags()->attach($tag->id);

    $list = SmartList::factory()->create([
        'criteria' => ['type' => 'tag', 'tag' => 'Work', 'operator' => 'equals'],
    ]);

    expect($list->todayCount())->toBe(0);
});

// MARK: anytimeCount

test('anytimeCount counts matching items with no start_date and not someday', function () {
    $tag = Tag::factory()->create(['name' => 'Work']);

    $anytime = Item::factory()->create([
        'is_trashed' => false,
        'is_inbox' => false,
        'is_logged' => false,
        'start_date' => null,
        'start' => null,
    ]);
    $someday = Item::factory()->create([
        'is_trashed' => false,
        'is_inbox' => false,
        'is_logged' => false,
        'start_date' => null,
        'start' => 'Someday',
    ]);
    $withDate = Item::factory()->create([
        'is_trashed' => false,
        'is_inbox' => false,
        'is_logged' => false,
        'start_date' => today(),
        'start' => null,
    ]);

    $anytime->tags()->attach($tag->id);
    $someday->tags()->attach($tag->id);
    $withDate->tags()->attach($tag->id);

    $list = SmartList::factory()->create([
        'criteria' => ['type' => 'tag', 'tag' => 'Work', 'operator' => 'equals'],
    ]);

    expect($list->anytimeCount())->toBe(1);
});

test('anytimeCount excludes inbox items', function () {
    $tag = Tag::factory()->create(['name' => 'Work']);

    $inbox = Item::factory()->create([
        'is_trashed' => false,
        'is_inbox' => true,
        'is_logged' => false,
        'start_date' => null,
        'start' => null,
    ]);
    $inbox->tags()->attach($tag->id);

    $list = SmartList::factory()->create([
        'criteria' => ['type' => 'tag', 'tag' => 'Work', 'operator' => 'equals'],
    ]);

    expect($list->anytimeCount())->toBe(0);
});
