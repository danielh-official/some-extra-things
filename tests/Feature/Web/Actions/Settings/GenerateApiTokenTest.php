<?php

use Native\Desktop\Facades\Settings;

use function Pest\Laravel\post;

test('generates token, stores its hash, and flashes the plain token once', function () {
    $capturedHash = null;

    Settings::shouldReceive('set')
        ->withArgs(function ($key, $value) use (&$capturedHash) {
            $capturedHash = $value;

            return $key === 'api_token_hash' && strlen($value) === 64;
        })
        ->once();

    post(route('settings.api-token.generate'))
        ->assertRedirect(route('settings.index'))
        ->assertSessionHas('new_api_token', function ($token) use (&$capturedHash) {
            return strlen($token) === 64 && hash('sha256', $token) === $capturedHash;
        });
});

test('falls back to session when settings throws', function () {
    Settings::shouldReceive('set')
        ->andThrow(new Exception('unavailable'));

    post(route('settings.api-token.generate'))
        ->assertRedirect(route('settings.index'))
        ->assertSessionHas('new_api_token');

    $newToken = session('new_api_token');
    $storedHash = session('api_token_hash');

    expect($newToken)->toBeString()->toHaveLength(64);
    expect($storedHash)->toBe(hash('sha256', $newToken));
});
