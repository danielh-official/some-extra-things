<?php

use App\Models\SmartList;
use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\patch;

uses(RefreshDatabase::class);

describe('criteria', function () {
    test('JSON string is decoded into an array before validation', function () {
        $smartList = SmartList::factory()->create();
        $tag = Tag::factory()->create();

        $criteria = json_encode([
            'type' => 'tag',
            'tag' => $tag->name,
            'operator' => 'equals',
        ]);

        $response = patch(route('smart-lists.update', $smartList), [
            'name' => 'Updated Smart List',
            'criteria' => $criteria,
        ]);

        $response->assertRedirect();
        assertDatabaseHas('smart_lists', ['id' => $smartList->id, 'name' => 'Updated Smart List']);
    });

    test('JSON string with group type is decoded', function () {
        $smartList = SmartList::factory()->create();

        $criteria = json_encode([
            'type' => 'group',
            'logic' => 'or',
            'conditions' => [
                ['type' => 'tag', 'tag' => Tag::factory()->create()->name, 'operator' => 'equals'],
            ],
        ]);

        $response = patch(route('smart-lists.update', $smartList), [
            'name' => 'Updated Group List',
            'criteria' => $criteria,
        ]);

        $response->assertRedirect();
        assertDatabaseHas('smart_lists', ['id' => $smartList->id, 'name' => 'Updated Group List']);
    });

    test('list-wrapped JSON array is unwrapped', function () {
        $smartList = SmartList::factory()->create();
        $tag = Tag::factory()->create();

        $criteria = json_encode([[
            'type' => 'tag',
            'tag' => $tag->name,
            'operator' => 'equals',
        ]]);

        $response = patch(route('smart-lists.update', $smartList), [
            'name' => 'Unwrapped Update',
            'criteria' => $criteria,
        ]);

        $response->assertRedirect();
        assertDatabaseHas('smart_lists', ['id' => $smartList->id, 'name' => 'Unwrapped Update']);
    });

    test('can be omitted', function () {
        $smartList = SmartList::factory()->create();

        $response = patch(route('smart-lists.update', $smartList), [
            'name' => 'Renamed List',
        ]);

        $response->assertRedirect();
        assertDatabaseHas('smart_lists', ['id' => $smartList->id, 'name' => 'Renamed List']);
    });
});
