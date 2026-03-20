<?php

use App\Http\Middleware\EnsureLocalhost;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Native\Desktop\Facades\Settings;

use function Pest\Laravel\withoutMiddleware;

uses(RefreshDatabase::class);

test('allows request with valid bearer token', function () {
    $token = bin2hex(random_bytes(32));

    Settings::shouldReceive('get')
        ->with('api_token_hash', null)
        ->andReturn(hash('sha256', $token));

    withoutMiddleware([EnsureLocalhost::class])
        ->getJson(route('api.items.index'), ['Authorization' => "Bearer {$token}"])
        ->assertOk();
});

test('rejects request with missing token header with 401', function () {
    $token = bin2hex(random_bytes(32));

    Settings::shouldReceive('get')
        ->with('api_token_hash', null)
        ->andReturn(hash('sha256', $token));

    withoutMiddleware([EnsureLocalhost::class])
        ->getJson(route('api.items.index'))
        ->assertUnauthorized()
        ->assertJson(['error' => 'Unauthorized']);
});

test('rejects request with wrong token with 401', function () {
    $token = bin2hex(random_bytes(32));

    Settings::shouldReceive('get')
        ->with('api_token_hash', null)
        ->andReturn(hash('sha256', $token));

    withoutMiddleware([EnsureLocalhost::class])
        ->getJson(route('api.items.index'), ['Authorization' => 'Bearer wrongtoken'])
        ->assertUnauthorized()
        ->assertJson(['error' => 'Unauthorized']);
});

test('rejects request when no token has been generated yet with 401', function () {
    Settings::shouldReceive('get')
        ->with('api_token_hash', null)
        ->andReturn(null);

    withoutMiddleware([EnsureLocalhost::class])
        ->getJson(route('api.items.index'), ['Authorization' => 'Bearer sometoken'])
        ->assertUnauthorized()
        ->assertJson(['error' => 'Unauthorized']);
});

test('falls back to session hash when settings throws', function () {
    $token = bin2hex(random_bytes(32));

    Settings::shouldReceive('get')
        ->with('api_token_hash', null)
        ->andThrow(new Exception('unavailable'));

    session()->put('api_token_hash', hash('sha256', $token));

    withoutMiddleware([EnsureLocalhost::class])
        ->getJson(route('api.items.index'), ['Authorization' => "Bearer {$token}"])
        ->assertOk();
});
