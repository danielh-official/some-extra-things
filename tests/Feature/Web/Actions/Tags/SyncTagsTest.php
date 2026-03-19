<?php

use App\Models\Tag;
use App\Services\TagService;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\mock;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

test('redirects with error when TagService import returns falsy', function () {
    mock(TagService::class)
        ->shouldReceive('import')
        ->andReturn('');

    post(route('tags.sync'))
        ->assertRedirect(route('tags.index'))
        ->assertSessionHas('error', 'Failed to fetch tags from Things 3.');
});

test('creates tags from the imported output', function () {
    $output = 'abc123|||My Tag|||missing value|||~~~';

    mock(TagService::class)
        ->shouldReceive('import')
        ->andReturn($output);

    post(route('tags.sync'));

    assertDatabaseHas('tags', [
        'name' => 'My Tag',
        'things_id' => 'abc123',
    ]);
});

test('strips missing value keyboard shortcut', function () {
    $output = 'abc123|||My Tag|||missing value|||~~~';

    mock(TagService::class)
        ->shouldReceive('import')
        ->andReturn($output);

    post(route('tags.sync'));

    assertDatabaseHas('tags', [
        'name' => 'My Tag',
        'keyboard_shortcut' => null,
    ]);
});

test('sets keyboard shortcut when provided', function () {
    $output = 'abc123|||My Tag|||k|||~~~';

    mock(TagService::class)
        ->shouldReceive('import')
        ->andReturn($output);

    post(route('tags.sync'));

    assertDatabaseHas('tags', [
        'name' => 'My Tag',
        'keyboard_shortcut' => 'k',
    ]);
});

test('sets parent_tag_id on second pass', function () {
    $output = 'parent123|||Parent Tag|||missing value|||~~~child456|||Child Tag|||missing value|||parent123~~~';

    mock(TagService::class)
        ->shouldReceive('import')
        ->andReturn($output);

    post(route('tags.sync'));

    $parent = Tag::where('things_id', 'parent123')->first();
    $child = Tag::where('things_id', 'child456')->first();

    expect($child->parent_tag_id)->toBe($parent->id);
    expect($child->parent_things_tag_id)->toBe('parent123');
});

test('updates existing tag by name on upsert', function () {
    $existing = Tag::factory()->create(['name' => 'Existing Tag', 'things_id' => null]);

    $output = 'newthingsid|||Existing Tag|||missing value|||~~~';

    mock(TagService::class)
        ->shouldReceive('import')
        ->andReturn($output);

    post(route('tags.sync'));

    assertDatabaseHas('tags', [
        'id' => $existing->id,
        'things_id' => 'newthingsid',
    ]);
});

test('redirects with success status after sync', function () {
    $output = 'abc|||Tag|||missing value|||~~~';

    mock(TagService::class)
        ->shouldReceive('import')
        ->andReturn($output);

    post(route('tags.sync'))
        ->assertRedirect(route('tags.index'))
        ->assertSessionHas('status', 'Tags synced from Things 3.');
});
