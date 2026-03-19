<?php

use App\Models\Item;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Native\Desktop\Facades\Settings;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('tag show lists items with the tag by default', function () {
    Settings::shouldReceive('get')->with('allow_tag_edits', false)->andReturn(false);

    $tag = Tag::factory()->create();
    $tagged = Item::factory()->create(['status' => 'Open']);
    $untagged = Item::factory()->create(['status' => 'Open']);
    $tagged->tags()->attach($tag->id);

    get(route('tags.show', $tag->id))
        ->assertSee($tagged->title)
        ->assertDontSee($untagged->title);
});

test('tag show with invert=1 lists items without the tag', function () {
    Settings::shouldReceive('get')->with('allow_tag_edits', false)->andReturn(false);

    $tag = Tag::factory()->create();
    $tagged = Item::factory()->create(['status' => 'Open']);
    $untagged = Item::factory()->create(['status' => 'Open']);
    $tagged->tags()->attach($tag->id);

    get(route('tags.show', [$tag->id, 'invert' => 1]))
        ->assertSee($untagged->title)
        ->assertDontSee($tagged->title);
});
