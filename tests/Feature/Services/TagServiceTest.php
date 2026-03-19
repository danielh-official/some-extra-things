<?php

use App\Models\Tag;
use App\Services\TagService;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

describe('update', function () {
    test('update throws when osascript returns a non-zero exit code', function () {
        $tag = Tag::factory()->create(['name' => 'MyTag']);
        $service = new TagService;

        // exec() is a PHP built-in; on CI there is no Things3. The command will
        // fail with a non-zero exit code, so throw_if should re-throw.
        expect(fn () => $service->update($tag, 'NewName', null))->toThrow(Throwable::class);
    })->skip('Requires Things3 / osascript — integration test only');

    test('update builds a rename script and passes it to osascript', function () {
        $tag = Tag::factory()->create(['name' => 'OldName']);

        // Spy on buildAppleScript via a partial mock.
        $service = Mockery::mock(TagService::class)->makePartial();
        $service->shouldReceive('update')->passthru();

        // We cannot call the real exec() in CI, so this only verifies that
        // buildAppleScript produces the expected rename statement.
        $script = callMethod($service, 'buildAppleScript', [$tag, 'NewName', null]);

        expect($script)->toContain('set name of tag "OldName" to "NewName"');
        expect($script)->toContain('tell application "Things3"');
        expect($script)->toContain('end tell');
    });

    test('update script clears parent when new parent is null and tag had one', function () {
        $parent = Tag::factory()->create(['name' => 'Work']);
        $tag = Tag::factory()->create(['name' => 'Meeting', 'parent_tag_id' => $parent->id]);
        $service = new TagService;

        $script = callMethod($service, 'buildAppleScript', [$tag, 'Meeting', null]);

        expect($script)->toContain('set parent tag of tag "Meeting" to missing value');
    });

    test('update script assigns new parent when parent id is provided', function () {
        $parent = Tag::factory()->create(['name' => 'Work']);
        $tag = Tag::factory()->create(['name' => 'Meeting', 'parent_tag_id' => null]);
        $service = new TagService;

        $script = callMethod($service, 'buildAppleScript', [$tag, 'Meeting', (string) $parent->id]);

        expect($script)->toContain('set parent tag of tag "Meeting" to tag "Work"');
    });
});

describe('buildAppleScript', function () {
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

});
