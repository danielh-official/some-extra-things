<?php

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Native\Desktop\Facades\Settings;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

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
