<?php

use App\Models\Tag;
use App\Services\TagService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('buildAppleScript renames tag', function () {
    $tag = Tag::factory()->create(['name' => 'OldName']);
    $service = new TagService;

    $script = callMethod($service, 'buildAppleScript', [$tag, 'NewName', null]);

    expect($script)->toContain('set name of tag "OldName" to "NewName"');
});

test('buildAppleScript sets parent tag', function () {
    $parent = Tag::factory()->create(['name' => 'Places']);
    $tag = Tag::factory()->create(['name' => 'Home']);
    $service = new TagService;

    $script = callMethod($service, 'buildAppleScript', [$tag, 'Home', (string) $parent->id]);

    expect($script)->toContain('set parent tag of tag "Home" to tag "Places"');
});

test('buildAppleScript clears parent when parent_tag_id is null and tag had a parent', function () {
    $parent = Tag::factory()->create(['name' => 'Places']);
    $tag = Tag::factory()->create(['name' => 'Home', 'parent_tag_id' => $parent->id]);
    $service = new TagService;

    $script = callMethod($service, 'buildAppleScript', [$tag, 'Home', null]);

    expect($script)->toContain('set parent tag of tag "Home" to missing value');
});

test('buildAppleScript handles double quotes in tag names via AppleScript quote constant', function () {
    $tag = Tag::factory()->create(['name' => 'Say "Hello"']);
    $service = new TagService;

    $script = callMethod($service, 'buildAppleScript', [$tag, 'Say "Goodbye"', null]);

    expect($script)->toContain('& quote &');
});