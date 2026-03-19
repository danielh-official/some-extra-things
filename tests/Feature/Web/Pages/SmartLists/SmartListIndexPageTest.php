<?php

use App\Models\Item;
use App\Models\SmartList;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('index sorts by total count descending by default', function () {
    $tagA = Tag::factory()->create();
    $tagB = Tag::factory()->create();

    $itemWithBoth = Item::factory()->create([
        'status' => 'Open',
    ]);
    $itemWithOnlyA = Item::factory()->create([
        'status' => 'Open',
    ]);

    $itemWithBoth->tags()->sync([$tagA->id, $tagB->id]);
    $itemWithOnlyA->tags()->sync([$tagA->id]);

    $listA = SmartList::create([
        'name' => 'Has A',
        'criteria' => [
            'type' => 'tag',
            'tag' => $tagA->name,
            'operator' => 'equals',
        ],
    ]);

    $listB = SmartList::create([
        'name' => 'Has B only',
        'criteria' => [
            'type' => 'group',
            'logic' => 'and',
            'conditions' => [
                [
                    'type' => 'tag',
                    'tag' => $tagB->name,
                    'operator' => 'equals',
                ],
                [
                    'type' => 'tag',
                    'tag' => $tagA->name,
                    'operator' => 'not_equals',
                ],
            ],
        ],
    ]);

    $response = get(route('smart-lists.index'));

    $response->assertSuccessful();

    $ordered = $response->viewData('lists');

    expect($ordered[0]['model']->id)->toBe($listA->id);
    expect($ordered[1]['model']->id)->toBe($listB->id);
});

test('index sorts by today count descending when sort=today_desc', function () {
    $tagA = Tag::factory()->create();
    $tagB = Tag::factory()->create();

    // listA has 1 today item, listB has 2 today items — listB should appear first
    $todayA = Item::factory()->create(['status' => 'Open', 'is_inbox' => false, 'is_logged' => false, 'start_date' => today()]);
    $todayB1 = Item::factory()->create(['status' => 'Open', 'is_inbox' => false, 'is_logged' => false, 'start_date' => today()]);
    $todayB2 = Item::factory()->create(['status' => 'Open', 'is_inbox' => false, 'is_logged' => false, 'start_date' => today()]);

    $todayA->tags()->attach($tagA->id);
    $todayB1->tags()->attach($tagB->id);
    $todayB2->tags()->attach($tagB->id);

    $listA = SmartList::create(['name' => 'A', 'criteria' => ['type' => 'tag', 'tag' => $tagA->name, 'operator' => 'equals']]);
    $listB = SmartList::create(['name' => 'B', 'criteria' => ['type' => 'tag', 'tag' => $tagB->name, 'operator' => 'equals']]);

    $ordered = get(route('smart-lists.index', ['sort' => 'today_desc']))->viewData('lists');

    expect($ordered[0]['model']->id)->toBe($listB->id);
    expect($ordered[1]['model']->id)->toBe($listA->id);
});

test('index persists sort preference in session', function () {
    get(route('smart-lists.index', ['sort' => 'today_desc']));

    // Second request without query param should still use today_desc from session
    get(route('smart-lists.index'))->viewData('lists');

    expect(session('smart_lists_sort'))->toBe('today_desc');
});

test('index includes todayCount and anytimeCount in list entries', function () {
    $tag = Tag::factory()->create();

    $todayItem = Item::factory()->create([
        'status' => 'Open',
        'is_inbox' => false,
        'is_logged' => false,
        'start_date' => today(),
        'start' => null,
    ]);
    $anytimeItem = Item::factory()->create([
        'status' => 'Open',
        'is_inbox' => false,
        'is_logged' => false,
        'start_date' => null,
        'start' => null,
    ]);
    $somedayItem = Item::factory()->create([
        'status' => 'Open',
        'is_inbox' => false,
        'is_logged' => false,
        'start_date' => null,
        'start' => 'Someday',
    ]);

    $todayItem->tags()->attach($tag->id);
    $anytimeItem->tags()->attach($tag->id);
    $somedayItem->tags()->attach($tag->id);

    SmartList::create([
        'name' => 'Tagged',
        'criteria' => ['type' => 'tag', 'tag' => $tag->name, 'operator' => 'equals'],
    ]);

    $entries = get(route('smart-lists.index'))->viewData('lists');

    expect($entries[0]['count'])->toBe(3);
    expect($entries[0]['todayCount'])->toBe(1);
    expect($entries[0]['anytimeCount'])->toBe(1);
});

test('index shows today and anytime counts in the view', function () {
    $tag = Tag::factory()->create();

    $todayItem = Item::factory()->create([
        'status' => 'Open',
        'is_inbox' => false,
        'is_logged' => false,
        'start_date' => today(),
        'start' => null,
    ]);
    $anytimeItem = Item::factory()->create([
        'status' => 'Open',
        'is_inbox' => false,
        'is_logged' => false,
        'start_date' => null,
        'start' => null,
    ]);

    $todayItem->tags()->attach($tag->id);
    $anytimeItem->tags()->attach($tag->id);

    SmartList::create([
        'name' => 'Tagged',
        'criteria' => ['type' => 'tag', 'tag' => $tag->name, 'operator' => 'equals'],
    ]);

    get(route('smart-lists.index'))
        ->assertSee('today')
        ->assertSee('anytime');
});
