<?php

use App\Models\SmartList;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

test('create renders the create view with an empty SmartList', function () {
    $response = get(route('smart-lists.create'));

    $response->assertSuccessful();
    $smartList = $response->viewData('smartList');
    expect($smartList)->toBeInstanceOf(SmartList::class);
    expect($smartList->exists)->toBeFalse();
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
