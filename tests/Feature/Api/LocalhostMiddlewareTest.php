<?php

use App\Http\Middleware\EnsureApiToken;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\call;
use function Pest\Laravel\withoutMiddleware;

uses(RefreshDatabase::class);

test('allows requests from 127.0.0.1', function () {
    withoutMiddleware([EnsureApiToken::class]);

    call('GET', route('api.items.index'), [], [], [], ['REMOTE_ADDR' => '127.0.0.1'])
        ->assertOk();
});

test('blocks requests from external IPs with 403', function () {
    withoutMiddleware([EnsureApiToken::class]);

    call('GET', route('api.items.index'), [], [], [], ['REMOTE_ADDR' => '192.168.1.1'])
        ->assertStatus(403)
        ->assertJson(['error' => 'Forbidden']);
});

test('blocks requests from non-localhost with 403', function () {
    withoutMiddleware([EnsureApiToken::class]);

    call('GET', route('api.items.index'), [], [], [], ['REMOTE_ADDR' => '10.0.0.1'])
        ->assertStatus(403)
        ->assertJson(['error' => 'Forbidden']);
});
