<?php

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('renders with a default empty condition when no criteria provided', function () {
    Livewire::test('smart-list-criteria-builder')
        ->assertSet('conditions', [['type' => 'tag', 'tag' => '', 'operator' => 'equals']])
        ->assertSet('logic', 'and');
});

test('mounts from a single tag criteria', function () {
    $criteria = json_encode([
        'type' => 'tag',
        'tag' => 'work',
        'operator' => 'equals',
    ]);

    Livewire::test('smart-list-criteria-builder', ['criteria' => $criteria])
        ->assertSet('conditions', [['type' => 'tag', 'tag' => 'work', 'operator' => 'equals']]);
});

test('mounts from a flat group criteria', function () {
    $criteria = json_encode([
        'type' => 'group',
        'logic' => 'or',
        'conditions' => [
            ['type' => 'tag', 'tag' => 'work', 'operator' => 'equals'],
            ['type' => 'tag', 'tag' => 'home', 'operator' => 'not_equals'],
        ],
    ]);

    Livewire::test('smart-list-criteria-builder', ['criteria' => $criteria])
        ->assertSet('logic', 'or')
        ->assertSet('conditions', [
            ['type' => 'tag', 'tag' => 'work', 'operator' => 'equals'],
            ['type' => 'tag', 'tag' => 'home', 'operator' => 'not_equals'],
        ]);
});

test('mounts from a nested group criteria', function () {
    $criteria = json_encode([
        'type' => 'group',
        'logic' => 'and',
        'conditions' => [
            ['type' => 'tag', 'tag' => 'work', 'operator' => 'equals'],
            [
                'type' => 'group',
                'logic' => 'or',
                'conditions' => [
                    ['type' => 'tag', 'tag' => 'personal', 'operator' => 'equals'],
                    ['type' => 'tag', 'tag' => 'home', 'operator' => 'equals'],
                ],
            ],
        ],
    ]);

    $component = Livewire::test('smart-list-criteria-builder', ['criteria' => $criteria]);
    $component->assertSet('logic', 'and');

    $conditions = $component->get('conditions');
    expect($conditions[0])->toBe(['type' => 'tag', 'tag' => 'work', 'operator' => 'equals']);
    expect($conditions[1]['type'])->toBe('group');
    expect($conditions[1]['logic'])->toBe('or');
    expect($conditions[1]['conditions'])->toBe([
        ['type' => 'tag', 'tag' => 'personal', 'operator' => 'equals'],
        ['type' => 'tag', 'tag' => 'home', 'operator' => 'equals'],
    ]);
});

test('can add a condition', function () {
    Livewire::test('smart-list-criteria-builder')
        ->call('addCondition')
        ->assertCount('conditions', 2);
});

test('can remove a condition', function () {
    Livewire::test('smart-list-criteria-builder')
        ->call('addCondition')
        ->call('removeCondition', 0)
        ->assertCount('conditions', 1);
});

test('removing the only condition resets to a single empty condition', function () {
    Livewire::test('smart-list-criteria-builder')
        ->call('removeCondition', 0)
        ->assertSet('conditions', [['type' => 'tag', 'tag' => '', 'operator' => 'equals']]);
});

test('can add a group', function () {
    $component = Livewire::test('smart-list-criteria-builder')
        ->call('addGroup');

    $conditions = $component->get('conditions');
    expect($conditions)->toHaveCount(2);
    expect($conditions[1]['type'])->toBe('group');
    expect($conditions[1]['logic'])->toBe('and');
    expect($conditions[1]['conditions'])->toHaveCount(1);
});

test('can add a condition inside a group', function () {
    $component = Livewire::test('smart-list-criteria-builder')
        ->call('addGroup')
        ->call('addCondition', 1);

    $conditions = $component->get('conditions');
    expect($conditions[1]['conditions'])->toHaveCount(2);
});

test('can remove a condition from a group', function () {
    $component = Livewire::test('smart-list-criteria-builder')
        ->call('addGroup')
        ->call('addCondition', 1)
        ->call('removeCondition', 0, 1);

    $conditions = $component->get('conditions');
    expect($conditions[1]['conditions'])->toHaveCount(1);
});

test('can remove a group', function () {
    Livewire::test('smart-list-criteria-builder')
        ->call('addGroup')
        ->call('removeGroup', 1)
        ->assertCount('conditions', 1);
});

test('toggleLogic toggles root logic', function () {
    Livewire::test('smart-list-criteria-builder')
        ->assertSet('logic', 'and')
        ->call('toggleLogic')
        ->assertSet('logic', 'or');
});

test('toggleLogic toggles sub-group logic', function () {
    $component = Livewire::test('smart-list-criteria-builder')
        ->call('addGroup')
        ->call('toggleLogic', 1);

    $conditions = $component->get('conditions');
    expect($conditions[1]['logic'])->toBe('or');
});

test('single condition with tag produces single tag json', function () {
    Tag::factory()->create(['name' => 'work']);

    $component = Livewire::test('smart-list-criteria-builder')
        ->set('conditions.0.tag', 'work')
        ->set('conditions.0.operator', 'equals');

    $json = $component->instance()->getCriteriaJson();

    expect(json_decode($json, true))->toBe([
        'type' => 'tag',
        'tag' => 'work',
        'operator' => 'equals',
    ]);
});

test('multiple conditions produce group json', function () {
    Tag::factory()->create(['name' => 'work']);
    Tag::factory()->create(['name' => 'home']);

    $component = Livewire::test('smart-list-criteria-builder')
        ->set('conditions.0.tag', 'work')
        ->set('conditions.0.operator', 'equals')
        ->call('addCondition')
        ->set('conditions.1.tag', 'home')
        ->set('conditions.1.operator', 'not_equals')
        ->set('logic', 'and');

    $decoded = json_decode($component->instance()->getCriteriaJson(), true);

    expect($decoded['type'])->toBe('group');
    expect($decoded['logic'])->toBe('and');
    expect($decoded['conditions'])->toBe([
        ['type' => 'tag', 'tag' => 'work', 'operator' => 'equals'],
        ['type' => 'tag', 'tag' => 'home', 'operator' => 'not_equals'],
    ]);
});

test('nested group produces correct json', function () {
    $component = Livewire::test('smart-list-criteria-builder')
        ->set('conditions.0.tag', 'work')
        ->call('addGroup')
        ->set('conditions.1.logic', 'or')
        ->set('conditions.1.conditions.0.tag', 'personal')
        ->call('addCondition', 1)
        ->set('conditions.1.conditions.1.tag', 'home');

    $decoded = json_decode($component->instance()->getCriteriaJson(), true);

    expect($decoded['type'])->toBe('group');
    expect($decoded['conditions'])->toHaveCount(2);
    expect($decoded['conditions'][0])->toBe(['type' => 'tag', 'tag' => 'work', 'operator' => 'equals']);
    expect($decoded['conditions'][1]['type'])->toBe('group');
    expect($decoded['conditions'][1]['logic'])->toBe('or');
    expect($decoded['conditions'][1]['conditions'])->toHaveCount(2);
});

test('empty tag conditions produce empty json', function () {
    $component = Livewire::test('smart-list-criteria-builder');

    expect($component->instance()->getCriteriaJson())->toBe('');
});
