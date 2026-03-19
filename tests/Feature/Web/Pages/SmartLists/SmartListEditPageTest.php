<?php

use App\Models\SmartList;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\get;
use function Pest\Laravel\put;

uses(RefreshDatabase::class);

test('edit renders the edit view with the smart list', function () {
    $smartList = SmartList::factory()->create();

    $response = get(route('smart-lists.edit', $smartList));

    $response->assertSuccessful();
    expect($response->viewData('smartList')->id)->toBe($smartList->id);
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
