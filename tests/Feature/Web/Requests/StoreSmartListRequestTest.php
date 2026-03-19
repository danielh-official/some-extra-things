<?php

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\post;

uses(RefreshDatabase::class);

describe('criteria', function () {
    test('JSON string is decoded into an array before validation', function () {
        $criteria = json_encode([
            'type' => 'tag',
            'tag' => Tag::factory()->create()->name,
            'operator' => 'equals',
        ]);

        $response = post(route('smart-lists.store'), [
            'name' => 'My Smart List',
            'criteria' => $criteria,
        ]);

        $response->assertRedirect(route('smart-lists.index'));
        assertDatabaseHas('smart_lists', ['name' => 'My Smart List']);
    });

    test('JSON string with group type is decoded and passes validation', function () {
        $criteria = json_encode([
            'type' => 'group',
            'logic' => 'and',
            'conditions' => [
                ['type' => 'tag', 'tag' => Tag::factory()->create()->name, 'operator' => 'equals'],
            ],
        ]);

        $response = post(route('smart-lists.store'), [
            'name' => 'Group Smart List',
            'criteria' => $criteria,
        ]);

        $response->assertRedirect(route('smart-lists.index'));
        assertDatabaseHas('smart_lists', ['name' => 'Group Smart List']);
    });

    test('list-wrapped JSON array is unwrapped before validation', function () {
        $tag = Tag::factory()->create();
        $criteria = json_encode([[
            'type' => 'tag',
            'tag' => $tag->name,
            'operator' => 'equals',
        ]]);

        $response = post(route('smart-lists.store'), [
            'name' => 'Unwrapped Smart List',
            'criteria' => $criteria,
        ]);

        $response->assertRedirect(route('smart-lists.index'));
        assertDatabaseHas('smart_lists', ['name' => 'Unwrapped Smart List']);
    });

    test('can be omitted', function () {
        $response = post(route('smart-lists.store'), [
            'name' => 'No Criteria List',
        ]);

        $response->assertRedirect(route('smart-lists.index'));
        assertDatabaseHas('smart_lists', ['name' => 'No Criteria List']);
    });
});
