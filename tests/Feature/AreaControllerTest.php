<?php

use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\get;

uses(RefreshDatabase::class);

test('returns 404 when item is not an Area', function () {
    $todo = Item::factory()->create(['type' => 'To-Do']);

    get(route('areas.show', $todo))->assertNotFound();
});

test('shows open projects and todos belonging to the area', function () {
    $area = Item::factory()->create(['type' => 'Area']);
    $project = Item::factory()->create(['type' => 'Project', 'parent_id' => $area->id, 'status' => 'Open', 'start' => null, 'start_date' => null]);
    $todo = Item::factory()->create(['type' => 'To-Do', 'parent_id' => $area->id, 'status' => 'Open', 'start' => null, 'start_date' => null]);

    $response = get(route('areas.show', $area));

    $response->assertSuccessful();
    $response->assertSee($project->title);
    $response->assertSee($todo->title);
});

test('separates upcoming items with a future start_date', function () {
    $area = Item::factory()->create(['type' => 'Area']);
    $upcoming = Item::factory()->create([
        'type' => 'To-Do',
        'parent_id' => $area->id,
        'status' => 'Open',
        'start' => null,
        'start_date' => today()->addDay(),
    ]);
    $current = Item::factory()->create([
        'type' => 'To-Do',
        'parent_id' => $area->id,
        'status' => 'Open',
        'start' => null,
        'start_date' => null,
    ]);

    $response = get(route('areas.show', $area));

    $items = $response->viewData('items');
    $upcomingItems = $response->viewData('upcomingItems');

    expect($items->pluck('id'))->toContain($current->id);
    expect($upcomingItems->pluck('id'))->toContain($upcoming->id);
    expect($items->pluck('id'))->not->toContain($upcoming->id);
});

test('separates someday items', function () {
    $area = Item::factory()->create(['type' => 'Area']);
    $someday = Item::factory()->create([
        'type' => 'To-Do',
        'parent_id' => $area->id,
        'status' => 'Open',
        'start' => 'Someday',
        'start_date' => null,
    ]);
    $current = Item::factory()->create([
        'type' => 'To-Do',
        'parent_id' => $area->id,
        'status' => 'Open',
        'start' => null,
        'start_date' => null,
    ]);

    $response = get(route('areas.show', $area));

    $items = $response->viewData('items');
    $somedayItems = $response->viewData('somedayItems');

    expect($somedayItems->pluck('id'))->toContain($someday->id);
    expect($items->pluck('id'))->not->toContain($someday->id);
    expect($items->pluck('id'))->toContain($current->id);
});

test('does not show items from other areas', function () {
    $area = Item::factory()->create(['type' => 'Area']);
    $otherArea = Item::factory()->create(['type' => 'Area']);
    $ownItem = Item::factory()->create(['type' => 'To-Do', 'parent_id' => $area->id, 'status' => 'Open', 'start' => null, 'start_date' => null]);
    $otherItem = Item::factory()->create(['type' => 'To-Do', 'parent_id' => $otherArea->id, 'status' => 'Open', 'start' => null, 'start_date' => null]);

    $response = get(route('areas.show', $area));

    $items = $response->viewData('items');
    expect($items->pluck('id'))->toContain($ownItem->id);
    expect($items->pluck('id'))->not->toContain($otherItem->id);
});
