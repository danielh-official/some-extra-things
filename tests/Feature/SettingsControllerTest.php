<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Native\Desktop\Facades\Settings;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('renders settings view with theme and allowTagEdits from Settings facade', function () {
    Settings::shouldReceive('get')
        ->with('theme', 'system')
        ->andReturn('dark');

    Settings::shouldReceive('get')
        ->with('allow_tag_edits', false)
        ->andReturn(true);

    $response = get(route('settings.index'));

    $response->assertSuccessful();
    expect($response->viewData('theme'))->toBe('dark');
    expect($response->viewData('allowTagEdits'))->toBeTrue();
});

test('falls back to session when Settings facade throws', function () {
    Settings::shouldReceive('get')
        ->andThrow(new Exception('unavailable'));

    session()->put('theme', 'light');
    session()->put('allow_tag_edits', true);

    $response = get(route('settings.index'));

    $response->assertSuccessful();
    expect($response->viewData('theme'))->toBe('light');
    expect($response->viewData('allowTagEdits'))->toBeTrue();
});

test('falls back to defaults when session is empty and Settings facade throws', function () {
    Settings::shouldReceive('get')
        ->andThrow(new Exception('unavailable'));

    $response = get(route('settings.index'));

    $response->assertSuccessful();
    expect($response->viewData('theme'))->toBe('system');
    expect($response->viewData('allowTagEdits'))->toBeFalse();
});
