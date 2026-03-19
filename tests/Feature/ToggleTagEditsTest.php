<?php

use Native\Desktop\Facades\Settings;

use function Pest\Laravel\post;

test('toggles allow_tag_edits from false to true', function () {
    Settings::shouldReceive('get')
        ->with('allow_tag_edits', false)
        ->andReturn(false);

    Settings::shouldReceive('set')
        ->with('allow_tag_edits', true)
        ->once();

    post(route('settings.tag-edits.toggle'))
        ->assertRedirect(route('settings'));
});

test('toggles allow_tag_edits from true to false', function () {
    Settings::shouldReceive('get')
        ->with('allow_tag_edits', false)
        ->andReturn(true);

    Settings::shouldReceive('set')
        ->with('allow_tag_edits', false)
        ->once();

    post(route('settings.tag-edits.toggle'))
        ->assertRedirect(route('settings'));
});

test('falls back to session when settings throws', function () {
    Settings::shouldReceive('get')
        ->with('allow_tag_edits', false)
        ->andThrow(new Exception('unavailable'));

    session()->put('allow_tag_edits', false);

    post(route('settings.tag-edits.toggle'))
        ->assertRedirect(route('settings'));

    expect(session('allow_tag_edits'))->toBeTrue();
});
