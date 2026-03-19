<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\post;

uses(RefreshDatabase::class);

test('toggleKanban switches from vertical to horizontal', function () {
    session(['all_kanban' => 'vertical']);

    post(route('all.kanban'))
        ->assertRedirect(route('all.index'));

    expect(session('all_kanban'))->toBe('horizontal');
});

test('toggleKanban switches from horizontal to vertical', function () {
    session(['all_kanban' => 'horizontal']);

    post(route('all.kanban'))
        ->assertRedirect(route('all.index'));

    expect(session('all_kanban'))->toBe('vertical');
});

test('toggleKanban defaults from vertical when session is empty', function () {
    post(route('all.kanban'));

    expect(session('all_kanban'))->toBe('horizontal');
});
