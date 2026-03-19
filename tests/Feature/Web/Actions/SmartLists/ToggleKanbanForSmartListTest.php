<?php

use App\Models\SmartList;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\patch;

uses(RefreshDatabase::class);

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