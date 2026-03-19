<?php

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

it('returns 200 for /all', function () {
    get('/all')->assertSuccessful();
});

test('renders all non-trashed todos and projects', function () {
    $todo = Item::factory()->create(['type' => 'To-Do', 'is_trashed' => false]);
    $project = Item::factory()->create(['type' => 'Project', 'is_trashed' => false]);
    $trashed = Item::factory()->create(['type' => 'To-Do', 'is_trashed' => true]);

    $response = get(route('all.index'));

    $response->assertSuccessful();
    $response->assertSee($todo->title);
    $response->assertSee($project->title);
    $response->assertDontSee($trashed->title);
});

test('groups items by bucket and passes grouped and kanban to view', function () {
    get(route('all.index'))
        ->assertSuccessful()
        ->assertViewHas('grouped')
        ->assertViewHas('kanban');
});