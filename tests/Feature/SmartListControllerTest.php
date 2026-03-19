<?php

use App\Models\Item;
use App\Models\SmartList;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\delete;
use function Pest\Laravel\get;
use function Pest\Laravel\post;
use function Pest\Laravel\put;

uses(RefreshDatabase::class);

test('index shows smart lists sorted by item count', function () {
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

test('can delete a smart list', function () {
    $smartList = SmartList::factory()->create();

    $response = delete(route('smart-lists.destroy', $smartList));

    $response->assertRedirect(route('smart-lists.index'));

    assertDatabaseMissing('smart_lists', [
        'id' => $smartList->id,
    ]);
});
