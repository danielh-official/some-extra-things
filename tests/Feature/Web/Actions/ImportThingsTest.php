<?php

use App\Models\Item;
use App\Services\AppleScriptRunner;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\mock;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

beforeEach(function () {
    Storage::fake();
});

function sampleItem(array $overrides = []): array
{
    return array_merge([
        'id' => 'abc123',
        'type' => 'To-Do',
        'title' => 'Buy milk',
        'parent' => null,
        'parent_id' => null,
        'heading' => null,
        'heading_id' => null,
        'is_inbox' => false,
        'start' => 'Someday',
        'start_date' => null,
        'evening' => false,
        'reminder_date' => null,
        'deadline' => null,
        'tags' => ['Work'],
        'all_matching_tags' => ['Work', 'Software Dev'],
        'status' => 'Open',
        'completion_date' => null,
        'is_logged' => false,
        'notes' => null,
        'checklist' => null,
        'is_trashed' => false,
        'creation_date' => '2024-01-01 00:00:00',
        'modification_date' => '2024-01-01 00:00:00',
    ], $overrides);
}

function mockExport(array $items): void
{
    Storage::put('things.json', json_encode($items));

    mock(AppleScriptRunner::class)
        ->shouldReceive('run')
        ->andReturn([json_encode($items), '', 0]);
}

test('upserts items and redirects with status', function () {
    mockExport([sampleItem()]);

    post(route('items.import'))
        ->assertRedirect()
        ->assertSessionHas('import_status', 'Imported 1 items from Things 3.');

    assertDatabaseHas('items', ['id' => 'abc123', 'title' => 'Buy milk']);
});

test('deduplicates items that appear in multiple lists', function () {
    $item = sampleItem();
    mockExport([$item, $item]); // same ID twice

    post(route('items.import'))
        ->assertSessionHas('import_status', 'Imported 1 items from Things 3.');

    expect(Item::count())->toBe(1);
});

test('syncs tags from tags and all_matching_tags', function () {
    mockExport([sampleItem(['tags' => ['Work'], 'all_matching_tags' => ['Work', 'Software Dev']])]);

    post(route('items.import'));

    $item = Item::find('abc123');
    $tagNames = $item->tags()->get()->pluck('name')->sort()->values()->all();

    expect($tagNames)->toBe(['Software Dev', 'Work']);
});

test('updates an existing item on reimport', function () {
    mockExport([sampleItem(['title' => 'Original title'])]);
    post(route('items.import'));

    mockExport([sampleItem(['title' => 'Updated title'])]);
    post(route('items.import'));

    assertDatabaseHas('items', ['id' => 'abc123', 'title' => 'Updated title']);
    expect(Item::count())->toBe(1);
});

test('redirects with error when things.json is missing', function () {
    // Runner returns non-zero exit code so the export command fails and writes no file
    mock(AppleScriptRunner::class)
        ->shouldReceive('run')
        ->andReturn(['', 'Things 3 is not running.', 1]);

    post(route('items.import'))
        ->assertRedirect()
        ->assertSessionHas('import_error');
});
