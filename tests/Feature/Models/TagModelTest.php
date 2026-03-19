<?php

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('ancestryPath returns empty string for a root tag', function () {
    $tag = Tag::factory()->create(['name' => 'Root', 'parent_tag_id' => null]);

    expect($tag->ancestryPath())->toBe('');
});

test('ancestryPath returns parent name for a child tag', function () {
    $parent = Tag::factory()->create(['name' => 'Places']);
    $child = Tag::factory()->create(['name' => 'Home', 'parent_tag_id' => $parent->id]);

    expect($child->ancestryPath())->toBe('Places');
});

test('ancestryPath returns full chain for a deeply nested tag', function () {
    $grandparent = Tag::factory()->create(['name' => 'World']);
    $parent = Tag::factory()->create(['name' => 'Europe', 'parent_tag_id' => $grandparent->id]);
    $child = Tag::factory()->create(['name' => 'France', 'parent_tag_id' => $parent->id]);

    expect($child->ancestryPath())->toBe('World > Europe');
});

test('tag has parent', function () {
    $parent = Tag::factory()->create(['name' => 'Hello']);
    $child = Tag::factory()->create(['name' => 'World', 'parent_tag_id' => $parent]);

    expect($child->parentTag->id)->toBe($parent->id);
});

test('parent has children', function () {
    $parent = Tag::factory()->create(['name' => 'Hello']);
    $child = Tag::factory()->create(['name' => 'World', 'parent_tag_id' => $parent]);

    expect($parent->childTags->pluck('id')->toArray())->toBe([$child->id]);
});