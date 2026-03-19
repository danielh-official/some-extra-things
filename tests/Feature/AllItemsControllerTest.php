<?php

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

test('renders all non-trashed todos and projects', function () {
    $todo = Item::factory()->create(['type' => 'To-Do', 'is_trashed' => false]);
    $project = Item::factory()->create(['type' => 'Project', 'is_trashed' => false]);
    $trashed = Item::factory()->create(['type' => 'To-Do', 'is_trashed' => true]);

    $response = get(route('all'));

    $response->assertSuccessful();
    $response->assertSee($todo->title);
    $response->assertSee($project->title);
    $response->assertDontSee($trashed->title);
});

test('groups items by bucket and passes grouped and kanban to view', function () {
    get(route('all'))
        ->assertSuccessful()
        ->assertViewHas('grouped')
        ->assertViewHas('kanban');
});

// ─── toggleKanban ─────────────────────────────────────

test('toggleKanban switches from vertical to horizontal', function () {
    session(['all_kanban' => 'vertical']);

    post(route('all.kanban'))
        ->assertRedirect(route('all'));

    expect(session('all_kanban'))->toBe('horizontal');
});

test('toggleKanban switches from horizontal to vertical', function () {
    session(['all_kanban' => 'horizontal']);

    post(route('all.kanban'))
        ->assertRedirect(route('all'));

    expect(session('all_kanban'))->toBe('vertical');
});

test('toggleKanban defaults from vertical when session is empty', function () {
    post(route('all.kanban'));

    expect(session('all_kanban'))->toBe('horizontal');
});
