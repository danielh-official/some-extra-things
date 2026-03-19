<?php

use App\Models\Tag;
use App\Services\TagService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Native\Desktop\Facades\Settings;

use function Pest\Laravel\get;
use function Pest\Laravel\mock;
use function Pest\Laravel\patch;

uses(RefreshDatabase::class);

// ─── Edit guard ──────────────────────────────────────────────────────────────

test('edit page renders with tag data and all other tags as parent options', function () {
    Settings::shouldReceive('get')->with('allow_tag_edits', false)->andReturn(true);

    $parent = Tag::factory()->create(['name' => 'Parent']);
    $tag = Tag::factory()->create(['name' => 'Work', 'parent_tag_id' => $parent->id]);
    Tag::factory()->create(['name' => 'Other']);

    $response = get(route('tags.edit', $tag->id));
    $response->assertSuccessful()
        ->assertSee('Work')
        ->assertSee('Parent')
        ->assertSee('Other');

    expect($response->getContent())->not->toContain('value="'.$tag->id.'"');
});

test('edit resolves tag by things_id', function () {
    Settings::shouldReceive('get')->with('allow_tag_edits', false)->andReturn(true);

    Tag::factory()->create(['name' => 'Work', 'things_id' => 'things-uuid-123']);

    get(route('tags.edit', 'things-uuid-123'))
        ->assertSuccessful();
});

test('edit returns 404 when allow_tag_edits is false', function () {
    Settings::shouldReceive('get')->with('allow_tag_edits', false)->andReturn(false);

    $tag = Tag::factory()->create(['name' => 'Work']);

    get(route('tags.edit', $tag->id))
        ->assertNotFound();
});

// ─── Update guard & validation ───────────────────────────────────────────────

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

// ─── Update DB behaviour ──────────────────────────────────────────────────────

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

// ─── Tag show invert ─────────────────────────────────────────────────────────

test('tag show lists items with the tag by default', function () {
    Settings::shouldReceive('get')->with('allow_tag_edits', false)->andReturn(false);

    $tag = Tag::factory()->create();
    $tagged = \App\Models\Item::factory()->create(['status' => 'Open']);
    $untagged = \App\Models\Item::factory()->create(['status' => 'Open']);
    $tagged->tags()->attach($tag->id);

    get(route('tags.show', $tag->id))
        ->assertSee($tagged->title)
        ->assertDontSee($untagged->title);
});

test('tag show with invert=1 lists items without the tag', function () {
    Settings::shouldReceive('get')->with('allow_tag_edits', false)->andReturn(false);

    $tag = Tag::factory()->create();
    $tagged = \App\Models\Item::factory()->create(['status' => 'Open']);
    $untagged = \App\Models\Item::factory()->create(['status' => 'Open']);
    $tagged->tags()->attach($tag->id);

    get(route('tags.show', [$tag->id, 'invert' => 1]))
        ->assertSee($untagged->title)
        ->assertDontSee($tagged->title);
});