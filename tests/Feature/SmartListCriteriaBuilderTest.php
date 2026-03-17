<?php

use App\Models\Tag;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Livewire\Livewire;

uses(RefreshDatabase::class);

test('renders with a default empty condition when no criteria provided', function () {
    Livewire::test('smart-list-criteria-builder')
        ->assertSet('conditions', [['tag' => '', 'operator' => 'equals']])
        ->assertSet('logic', 'and');
});

test('mounts from a single tag criteria', function () {
    $criteria = json_encode([
        'type' => 'tag',
        'tag' => 'work',
        'operator' => 'equals',
    ]);

    Livewire::test('smart-list-criteria-builder', ['criteria' => $criteria])
        ->assertSet('conditions', [['tag' => 'work', 'operator' => 'equals']]);
});

test('mounts from a group criteria', function () {
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
            ['tag' => 'work', 'operator' => 'equals'],
            ['tag' => 'home', 'operator' => 'not_equals'],
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
        ->assertSet('conditions', [['tag' => '', 'operator' => 'equals']]);
});

test('single condition with tag produces single tag json', function () {
    Tag::factory()->create(['name' => 'work']);

    $component = Livewire::test('smart-list-criteria-builder')
        ->set('conditions.0.tag', 'work')
        ->set('conditions.0.operator', 'equals');

    $json = $component->instance()->getCriteriaJson();
    $decoded = json_decode($json, true);

    expect($decoded)->toBe([
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

    $json = $component->instance()->getCriteriaJson();
    $decoded = json_decode($json, true);

    expect($decoded['type'])->toBe('group');
    expect($decoded['logic'])->toBe('and');
    expect($decoded['conditions'])->toBe([
        ['type' => 'tag', 'tag' => 'work', 'operator' => 'equals'],
        ['type' => 'tag', 'tag' => 'home', 'operator' => 'not_equals'],
    ]);
});

test('empty tag conditions produce empty json', function () {
    $component = Livewire::test('smart-list-criteria-builder');

    $json = $component->instance()->getCriteriaJson();

    expect($json)->toBe('');
});
