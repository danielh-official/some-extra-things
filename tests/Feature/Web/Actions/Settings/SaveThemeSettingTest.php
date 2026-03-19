<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Native\Desktop\Facades\Settings;

use function Pest\Laravel\post;

uses(RefreshDatabase::class);

test('saves theme via Settings facade and redirects to settings', function () {
    Settings::shouldReceive('set')
        ->with('theme', 'dark')
        ->once();

    post(route('settings.theme.update'), ['theme' => 'dark'])
        ->assertRedirect(route('settings.index'));
});

test('falls back to session when Settings facade throws', function () {
    Settings::shouldReceive('set')
        ->andThrow(new Exception('unavailable'));

    post(route('settings.theme.update'), ['theme' => 'light'])
        ->assertRedirect(route('settings.index'));

    expect(session('theme'))->toBe('light');
});

test('validation fails for invalid theme value', function () {
    post(route('settings.theme.update'), ['theme' => 'rainbow'])
        ->assertSessionHasErrors(['theme']);
});

test('validation fails when theme is missing', function () {
    post(route('settings.theme.update'), [])
        ->assertSessionHasErrors(['theme']);
});

test('accepts system as a valid theme', function () {
    Settings::shouldReceive('set')
        ->with('theme', 'system')
        ->once();

    post(route('settings.theme.update'), ['theme' => 'system'])
        ->assertRedirect(route('settings.index'));
});
