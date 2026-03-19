<?php

use App\Models\SmartList;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseMissing;
use function Pest\Laravel\delete;

uses(RefreshDatabase::class);

test('can delete a smart list', function () {
    $smartList = SmartList::factory()->create();

    $response = delete(route('smart-lists.destroy', $smartList));

    $response->assertRedirect(route('smart-lists.index'));

    assertDatabaseMissing('smart_lists', [
        'id' => $smartList->id,
    ]);
});
