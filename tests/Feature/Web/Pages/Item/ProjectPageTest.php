<?php

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('projects.show returns 200 for a Project item', function () {
    $project = Item::factory()->create(['type' => 'Project']);

    get(route('projects.show', $project))->assertSuccessful();
});

test('projects.show returns 404 for a To-Do item', function () {
    $todo = Item::factory()->create(['type' => 'To-Do']);

    get(route('projects.show', $todo))->assertNotFound();
});

test('projects.show redirects a Heading to its parent Project', function () {
    $project = Item::factory()->create(['type' => 'Project']);
    $heading = Item::factory()->create(['type' => 'Heading', 'parent_id' => $project->id]);

    get(route('projects.show', $heading))
        ->assertRedirect(route('projects.show', $project));
});

test('projects.show returns 404 for a Heading with no parent', function () {
    $heading = Item::factory()->create(['type' => 'Heading', 'parent_id' => null]);

    get(route('projects.show', $heading))->assertNotFound();
});

describe('child todos', function () {
    test('passes child todos grouped by heading for a Project', function () {
        $project = Item::factory()->create(['type' => 'Project']);
        $child = Item::factory()->create([
            'type' => 'To-Do',
            'parent_id' => $project->id,
            'status' => 'Open',
            'heading' => null,
            'is_trashed' => false,
        ]);

        $response = get(route('projects.show', $project));

        $childTodos = $response->viewData('childTodos');
        expect($childTodos)->not->toBeNull();
        expect($childTodos->flatten()->pluck('id'))->toContain($child->id);
    });

    test('childTodos is null for a To-Do item', function () {
        $todo = Item::factory()->create(['type' => 'To-Do']);

        $response = get(route('todos.show', $todo));

        expect($response->viewData('childTodos'))->toBeNull();
    });

});