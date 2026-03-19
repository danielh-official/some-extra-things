<?php

use App\Models\Item;
use App\Models\SmartList;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\patch;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

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
    $ordered = get(route('smart-lists.index'))->viewData('lists');

    expect(session('smart_lists_sort'))->toBe('today_desc');
});

test('can create a smart list', function () {
    $payload = [
        'name' => 'My Smart List',
        'criteria' => [
            'type' => 'tag',
            'tag' => Tag::factory()->create()->name,
            'operator' => 'equals',
        ],
    ];

    $response = post(route('smart-lists.store'), $payload);

    $response->assertRedirect(route('smart-lists.index'));

    assertDatabaseHas('smart_lists', [
        'name' => 'My Smart List',
    ]);
});

test('can update a smart list', function () {
    $smartList = SmartList::factory()->create([
        'name' => 'Original',
    ]);

    $payload = [
        'name' => 'Renamed',
        'criteria' => null,
    ];

    $response = put(route('smart-lists.update', $smartList), $payload);

    $response->assertRedirect(route('smart-lists.show', $smartList));

    assertDatabaseHas('smart_lists', [
        'id' => $smartList->id,
        'name' => 'Renamed',
    ]);
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

    $list = SmartList::create([
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

test('can delete a smart list', function () {
    $smartList = SmartList::factory()->create();

    $response = delete(route('smart-lists.destroy', $smartList));

    $response->assertRedirect(route('smart-lists.index'));

    assertDatabaseMissing('smart_lists', [
        'id' => $smartList->id,
    ]);
});

// ─── create ──────────────────────────────────────────

test('create renders the create view with an empty SmartList', function () {
    $response = get(route('smart-lists.create'));

    $response->assertSuccessful();
    $smartList = $response->viewData('smartList');
    expect($smartList)->toBeInstanceOf(SmartList::class);
    expect($smartList->exists)->toBeFalse();
});

// ─── show ────────────────────────────────────────────

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

// ─── edit ────────────────────────────────────────────

test('edit renders the edit view with the smart list', function () {
    $smartList = SmartList::factory()->create();

    $response = get(route('smart-lists.edit', $smartList));

    $response->assertSuccessful();
    expect($response->viewData('smartList')->id)->toBe($smartList->id);
});

// ─── duplicate ───────────────────────────────────────

test('duplicate renders the create view pre-filled with the source smart list data', function () {
    $original = SmartList::factory()->create(['name' => 'Original List']);

    $response = get(route('smart-lists.duplicate', $original));

    $response->assertSuccessful();
    $smartList = $response->viewData('smartList');
    expect($smartList->name)->toBe('Original List');
    expect($smartList->exists)->toBeFalse();
});

test('duplicate passes cancelLink pointing back to the source smart list', function () {
    $original = SmartList::factory()->create();

    $response = get(route('smart-lists.duplicate', $original));

    expect($response->viewData('cancelLink'))->toBe(route('smart-lists.show', $original));
});

// ─── togglePin ───────────────────────────────────────

test('togglePin sets is_pinned_to_sidebar to true when currently false', function () {
    $smartList = SmartList::factory()->create(['is_pinned_to_sidebar' => false]);

    patch(route('smart-lists.pin', $smartList))
        ->assertRedirect(route('smart-lists.show', $smartList));

    assertDatabaseHas('smart_lists', [
        'id' => $smartList->id,
        'is_pinned_to_sidebar' => true,
    ]);
});

test('togglePin sets is_pinned_to_sidebar to false when currently true', function () {
    $smartList = SmartList::factory()->create(['is_pinned_to_sidebar' => true]);

    patch(route('smart-lists.pin', $smartList));

    assertDatabaseHas('smart_lists', [
        'id' => $smartList->id,
        'is_pinned_to_sidebar' => false,
    ]);
});

// ─── toggleKanban ────────────────────────────────────

test('toggleKanban switches from vertical to horizontal', function () {
    $smartList = SmartList::factory()->create(['kanban_view' => 'vertical']);

    patch(route('smart-lists.kanban', $smartList))
        ->assertRedirect(route('smart-lists.show', $smartList));

    assertDatabaseHas('smart_lists', [
        'id' => $smartList->id,
        'kanban_view' => 'horizontal',
    ]);
});

test('toggleKanban switches from horizontal to vertical', function () {
    $smartList = SmartList::factory()->create(['kanban_view' => 'horizontal']);

    patch(route('smart-lists.kanban', $smartList));

    assertDatabaseHas('smart_lists', [
        'id' => $smartList->id,
        'kanban_view' => 'vertical',
    ]);
});
