<?php

use Native\Desktop\Facades\Settings;

use function Pest\Laravel\post;

test('generates and stores a new api token', function () {
    Settings::shouldReceive('set')
        ->withArgs(fn ($key, $value) => $key === 'api_token' && strlen($value) === 64)
        ->once();

    post(route('settings.api-token.generate'))
        ->assertRedirect(route('settings.index'));
});

test('falls back to session when settings throws', function () {
    Settings::shouldReceive('set')
        ->andThrow(new Exception('unavailable'));

    post(route('settings.api-token.generate'))
        ->assertRedirect(route('settings.index'));

    expect(session('api_token'))->toBeString()->toHaveLength(64);
});
