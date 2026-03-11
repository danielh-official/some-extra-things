<?php

use App\Support\ServerState;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('it toggles the server from enabled to disabled', function () {
    ServerState::setEnabled(true);
    expect(ServerState::isEnabled())->toBeTrue();

    $response = $this->from(route('settings'))->post(route('server.toggle'));

    $response->assertRedirect(route('settings'));
    $response->assertSessionHas('server_enabled', false);

    expect(ServerState::isEnabled())->toBeFalse();
});

test('it toggles the server from disabled to enabled', function () {
    ServerState::setEnabled(false);
    expect(ServerState::isEnabled())->toBeFalse();

    $response = $this->from(route('settings'))->post(route('server.toggle'));

    $response->assertRedirect(route('settings'));
    $response->assertSessionHas('server_enabled', true);

    expect(ServerState::isEnabled())->toBeTrue();
});
