<?php

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('todos.show returns 200 for a To-Do item', function () {
    $todo = Item::factory()->create(['type' => 'To-Do']);

    get(route('todos.show', $todo))->assertSuccessful();
});

test('todos.show returns 404 for a non-To-Do item', function () {
    $project = Item::factory()->create(['type' => 'Project']);

    get(route('todos.show', $project))->assertNotFound();
});

describe('note rendering', function () {
    test('converts markdown notes to HTML', function () {
        $todo = Item::factory()->create(['type' => 'To-Do', 'notes' => '**bold text**']);

        $response = get(route('todos.show', $todo));

        $response->assertSee('<strong>bold text</strong>', false);
    });

    test('notesHtml is null when item has no notes', function () {
        $todo = Item::factory()->create(['type' => 'To-Do', 'notes' => null]);

        $response = get(route('todos.show', $todo));

        expect($response->viewData('notesHtml'))->toBeNull();
    });
});
