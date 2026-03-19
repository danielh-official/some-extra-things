<?php

use App\Models\SmartList;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

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
