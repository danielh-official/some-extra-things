<?php

use App\Models\Tag;
use App\Services\TagService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Native\Desktop\Facades\Settings;

use function Pest\Laravel\mock;
use function Pest\Laravel\patch;

uses(RefreshDatabase::class);

test('update returns 404 when allow_tag_edits is false', function () {
    Settings::shouldReceive('get')->with('allow_tag_edits', false)->andReturn(false);

    $tag = Tag::factory()->create(['name' => 'Work']);

    patch(route('tags.update', $tag->id), ['name' => 'New'])
        ->assertNotFound();
});

test('update validates required name', function () {
    Settings::shouldReceive('get')->with('allow_tag_edits', false)->andReturn(true);

    $tag = Tag::factory()->create(['name' => 'Work']);

    patch(route('tags.update', $tag->id), ['name' => ''])
        ->assertSessionHasErrors('name');
});

test('update validates parent_tag_id exists', function () {
    Settings::shouldReceive('get')->with('allow_tag_edits', false)->andReturn(true);

    $tag = Tag::factory()->create(['name' => 'Work']);

    patch(route('tags.update', $tag->id), [
        'name' => 'Work',
        'parent_tag_id' => 9999,
    ])->assertSessionHasErrors('parent_tag_id');
});

// MARK: Update DB behaviour

test('update clears parent when none selected', function () {
    Settings::shouldReceive('get')->with('allow_tag_edits', false)->andReturn(true);

    $parent = Tag::factory()->create(['name' => 'Parent', 'things_id' => 'parent-uuid']);
    $tag = Tag::factory()->create([
        'name' => 'Work',
        'parent_tag_id' => $parent->id,
        'parent_things_tag_id' => 'parent-uuid',
    ]);

    mock(TagService::class)->shouldReceive('update')->once()->andReturn(null);

    patch(route('tags.update', $tag->id), ['name' => 'Work'])
        ->assertRedirect(route('tags.show', $tag->id));

    $tag->refresh();
    expect($tag->parent_tag_id)->toBeNull();
    expect($tag->parent_things_tag_id)->toBeNull();
});

test('update sets parent_things_tag_id from parent tag things_id', function () {
    Settings::shouldReceive('get')->with('allow_tag_edits', false)->andReturn(true);

    $parent = Tag::factory()->create(['name' => 'Parent', 'things_id' => 'parent-things-uuid']);
    $tag = Tag::factory()->create(['name' => 'Work']);

    mock(TagService::class)->shouldReceive('update')->once()->andReturn(null);

    patch(route('tags.update', $tag->id), [
        'name' => 'Work',
        'parent_tag_id' => $parent->id,
    ])->assertRedirect();

    $tag->refresh();
    expect($tag->parent_things_tag_id)->toBe('parent-things-uuid');
});
