<?php

use App\Services\AppleScriptRunner;
use Illuminate\Support\Facades\Storage;

use function Pest\Laravel\artisan;
use function Pest\Laravel\mock;

beforeEach(function () {
    Storage::fake();
});

test('writes valid json to things.json on success', function () {
    $items = [
        ['id' => 'abc123', 'type' => 'To-Do', 'title' => 'Buy milk'],
    ];

    mock(AppleScriptRunner::class)
        ->shouldReceive('run')
        ->once()
        ->andReturn([json_encode($items), '', 0]);

    artisan('things:export')->assertSuccessful();

    Storage::assertExists('things.json');
    $stored = json_decode(Storage::get('things.json'), true);
    expect($stored)->toHaveCount(1)
        ->and($stored[0]['title'])->toBe('Buy milk');
});

test('outputs the item count on success', function () {
    $items = [
        ['id' => 'a', 'type' => 'To-Do', 'title' => 'One'],
        ['id' => 'b', 'type' => 'Project', 'title' => 'Two'],
    ];

    mock(AppleScriptRunner::class)
        ->shouldReceive('run')
        ->andReturn([json_encode($items), '', 0]);

    artisan('things:export')->expectsOutputToContain('Exported 2 items');
});

test('fails when applescript exits non-zero', function () {
    mock(AppleScriptRunner::class)
        ->shouldReceive('run')
        ->once()
        ->andReturn(['', 'Things 3 is not running.', 1]);

    artisan('things:export')->assertFailed();

    Storage::assertMissing('things.json');
});

test('fails when applescript returns invalid json', function () {
    mock(AppleScriptRunner::class)
        ->shouldReceive('run')
        ->once()
        ->andReturn(['not valid json', '', 0]);

    artisan('things:export')->assertFailed();

    Storage::assertMissing('things.json');
});
