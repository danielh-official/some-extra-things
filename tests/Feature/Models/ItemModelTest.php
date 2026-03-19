<?php

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

test('scopeTopLevel excludes Headings', function () {
    Item::factory()->create(['type' => 'Heading']);
    Item::factory()->create(['type' => 'Project']);

    $results = Item::topLevel()->get();

    expect($results->pluck('id'))->not->toContain($results->firstWhere('type', 'Heading')?->id);
    expect($results->pluck('type')->unique()->toArray())->not->toContain('Heading');
});

test('scopeTopLevel excludes Areas', function () {
    Item::factory()->create(['type' => 'Area']);
    Item::factory()->create(['type' => 'Project']);

    $results = Item::topLevel()->get();

    expect($results->pluck('type')->unique()->toArray())->not->toContain('Area');
});

test('scopeTopLevel excludes todos that have a parent_id', function () {
    $project = Item::factory()->create(['type' => 'Project']);
    $childTodo = Item::factory()->create(['type' => 'To-Do', 'parent_id' => $project->id]);
    $topLevelTodo = Item::factory()->create(['type' => 'To-Do', 'parent_id' => null]);

    $results = Item::topLevel()->get();

    $ids = $results->pluck('id');
    expect($ids)->toContain($topLevelTodo->id);
    expect($ids)->not->toContain($childTodo->id);
});

test('scopeTopLevel includes Projects regardless of parent_id', function () {
    $area = Item::factory()->create(['type' => 'Area']);
    $projectInArea = Item::factory()->create(['type' => 'Project', 'parent_id' => $area->id]);
    $standaloneProject = Item::factory()->create(['type' => 'Project', 'parent_id' => null]);

    $results = Item::topLevel()->get();

    $ids = $results->pluck('id');
    expect($ids)->toContain($projectInArea->id);
    expect($ids)->toContain($standaloneProject->id);
});
