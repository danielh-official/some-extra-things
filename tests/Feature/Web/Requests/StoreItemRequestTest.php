<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;

use function Pest\Laravel\assertDatabaseHas;
use function Pest\Laravel\postJson;

uses(RefreshDatabase::class);

describe('tags', function () {
    test('string is split on newlines into array', function () {
        $response = postJson(route('api.items.store'), [
            'id' => Str::uuid(),
            'type' => 'To-Do',
            'title' => 'Test',
            'tags' => "work\nhome\nurgent",
        ]);

        $response->assertSuccessful();
        $response->assertJsonFragment(['tags' => ['work', 'home', 'urgent']]);
    });

    test('string is split on carriage return newlines', function () {
        $response = postJson(route('api.items.store'), [
            'id' => Str::uuid(),
            'type' => 'To-Do',
            'title' => 'Test',
            'tags' => "work\r\nhome",
        ]);

        $response->assertSuccessful();
        $response->assertJsonFragment(['tags' => ['work', 'home']]);
    });
});

describe('all_matching_tags', function () {
    test('string is split on newlines into array', function () {
        $response = postJson(route('api.items.store'), [
            'id' => Str::uuid(),
            'type' => 'To-Do',
            'title' => 'Test',
            'all_matching_tags' => "alpha\nbeta",
        ]);

        $response->assertSuccessful();
        $response->assertJsonFragment(['all_matching_tags' => ['alpha', 'beta']]);
    });
});

describe('checklist', function () {
    test('string is split on newlines into array', function () {
        $response = postJson(route('api.items.store'), [
            'id' => Str::uuid(),
            'type' => 'To-Do',
            'title' => 'Test',
            'checklist' => "step one\nstep two\nstep three",
        ]);

        $response->assertSuccessful();
        $response->assertJsonFragment(['checklist' => ['step one', 'step two', 'step three']]);
    });
});

describe('is_inbox', function () {
    test('Yes is converted to boolean true', function () {
        $response = postJson(route('api.items.store'), [
            'id' => Str::uuid(),
            'type' => 'To-Do',
            'title' => 'Test',
            'is_inbox' => 'Yes',
        ]);

        // 'Yes' fails Laravel's 'boolean' rule unless prepareForValidation converts it
        $response->assertSuccessful();
        $response->assertJsonFragment(['is_inbox' => true]);
    });

    test('No is converted to boolean false', function () {
        $response = postJson(route('api.items.store'), [
            'id' => Str::uuid(),
            'type' => 'To-Do',
            'title' => 'Test',
            'is_inbox' => 'No',
        ]);

        $response->assertSuccessful();
        $response->assertJsonFragment(['is_inbox' => false]);
    });
});

describe('evening', function () {
    test('Yes is converted to boolean true', function () {
        $response = postJson(route('api.items.store'), [
            'id' => Str::uuid(),
            'type' => 'To-Do',
            'title' => 'Test',
            'evening' => 'Yes',
        ]);

        $response->assertSuccessful();
        $response->assertJsonFragment(['evening' => true]);
    });

    test('No is converted to boolean false', function () {
        $response = postJson(route('api.items.store'), [
            'id' => Str::uuid(),
            'type' => 'To-Do',
            'title' => 'Test',
            'evening' => 'No',
        ]);

        $response->assertSuccessful();
        $response->assertJsonFragment(['evening' => false]);
    });
});

describe('is_logged', function () {
    test('Yes is converted to boolean true', function () {
        $response = postJson(route('api.items.store'), [
            'id' => Str::uuid(),
            'type' => 'To-Do',
            'title' => 'Test',
            'is_logged' => 'Yes',
        ]);

        $response->assertSuccessful();
        $response->assertJsonFragment(['is_logged' => true]);
    });

    test('No is converted to boolean false', function () {
        $response = postJson(route('api.items.store'), [
            'id' => Str::uuid(),
            'type' => 'To-Do',
            'title' => 'Test',
            'is_logged' => 'No',
        ]);

        $response->assertSuccessful();
        $response->assertJsonFragment(['is_logged' => false]);
    });
});

describe('date fields', function () {
    test('start_date string is parsed and stored', function () {
        $id = Str::uuid();

        postJson(route('api.items.store'), [
            'id' => $id,
            'type' => 'To-Do',
            'title' => 'Test',
            'start' => 'On Date',
            'start_date' => '2024-06-15',
        ])->assertSuccessful();

        assertDatabaseHas('items', ['id' => $id, 'start_date' => '2024-06-15 00:00:00']);
    });

    test('reminder_date string is parsed and stored', function () {
        $id = Str::uuid();

        postJson(route('api.items.store'), [
            'id' => $id,
            'type' => 'To-Do',
            'title' => 'Test',
            'reminder_date' => '2024-07-01 09:00:00',
        ])->assertSuccessful();

        assertDatabaseHas('items', ['id' => $id, 'reminder_date' => '2024-07-01 09:00:00']);
    });

    test('deadline string is parsed and stored', function () {
        $id = Str::uuid();

        postJson(route('api.items.store'), [
            'id' => $id,
            'type' => 'To-Do',
            'title' => 'Test',
            'deadline' => '2024-08-31',
        ])->assertSuccessful();

        assertDatabaseHas('items', ['id' => $id, 'deadline' => '2024-08-31 00:00:00']);
    });

    test('completion_date string is parsed and stored', function () {
        $id = Str::uuid();

        postJson(route('api.items.store'), [
            'id' => $id,
            'type' => 'To-Do',
            'title' => 'Test',
            'status' => 'Completed',
            'completion_date' => '2024-09-10 12:00:00',
        ])->assertSuccessful();

        assertDatabaseHas('items', ['id' => $id, 'completion_date' => '2024-09-10 12:00:00']);
    });

    test('modification_date string is parsed and stored', function () {
        $id = Str::uuid();

        postJson(route('api.items.store'), [
            'id' => $id,
            'type' => 'To-Do',
            'title' => 'Test',
            'modification_date' => '2024-10-05 08:30:00',
        ])->assertSuccessful();

        assertDatabaseHas('items', ['id' => $id, 'modification_date' => '2024-10-05 08:30:00']);
    });

    test('creation_date string is parsed and stored', function () {
        $id = Str::uuid();

        postJson(route('api.items.store'), [
            'id' => $id,
            'type' => 'To-Do',
            'title' => 'Test',
            'creation_date' => '2024-01-20 10:00:00',
        ])->assertSuccessful();

        assertDatabaseHas('items', ['id' => $id, 'creation_date' => '2024-01-20 10:00:00']);
    });
});

test('merge is skipped when no string conversions are needed', function () {
    $response = postJson(route('api.items.store'), [
        'id' => Str::uuid(),
        'type' => 'To-Do',
        'title' => 'No conversions needed',
    ]);

    $response->assertSuccessful();
});

test('failed validation does not dump during tests', function () {
    $response = postJson(route('api.items.store'), []);

    $response->assertUnprocessable();
    $response->assertJsonStructure(['message', 'errors']);
});
