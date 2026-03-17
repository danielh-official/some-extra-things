<?php

namespace App\Livewire;

use App\Models\Tag;
use Illuminate\View\View;
use Livewire\Component;

class SmartListCriteriaBuilder extends Component
{
    public string $logic = 'and';

    /** @var array<int, array<string, mixed>> */
    public array $conditions = [];

    public function mount(?string $criteria = null): void
    {
        if ($criteria) {
            $decoded = json_decode($criteria, true);

            if (is_array($decoded)) {
                if (($decoded['type'] ?? null) === 'tag') {
                    $this->conditions = [[
                        'type' => 'tag',
                        'tag' => $decoded['tag'] ?? '',
                        'operator' => $decoded['operator'] ?? 'equals',
                    ]];
                } elseif (($decoded['type'] ?? null) === 'group') {
                    $this->logic = $decoded['logic'] ?? 'and';
                    $this->conditions = $this->normalizeConditions($decoded['conditions'] ?? []);
                }
            }
        }

        if (empty($this->conditions)) {
            $this->conditions = [['type' => 'tag', 'tag' => '', 'operator' => 'equals']];
        }
    }

    /**
     * Normalize raw criteria conditions into a consistent shape.
     *
     * @param  array<int, array<string, mixed>>  $conditions
     * @return array<int, array<string, mixed>>
     */
    protected function normalizeConditions(array $conditions): array
    {
        return array_values(array_map(function (array $condition): array {
            if (($condition['type'] ?? null) === 'group') {
                $subConditions = array_filter(
                    $condition['conditions'] ?? [],
                    fn ($c) => is_array($c) && ($c['type'] ?? null) === 'tag'
                );

                return [
                    'type' => 'group',
                    'logic' => $condition['logic'] ?? 'and',
                    'conditions' => array_values(array_map(fn (array $c) => [
                        'type' => 'tag',
                        'tag' => $c['tag'] ?? '',
                        'operator' => $c['operator'] ?? 'equals',
                    ], $subConditions)),
                ];
            }

            return [
                'type' => 'tag',
                'tag' => $condition['tag'] ?? '',
                'operator' => $condition['operator'] ?? 'equals',
            ];
        }, $conditions));
    }

    public function addCondition(?int $groupIndex = null): void
    {
        $tagCondition = ['type' => 'tag', 'tag' => '', 'operator' => 'equals'];

        if ($groupIndex !== null
            && isset($this->conditions[$groupIndex])
            && ($this->conditions[$groupIndex]['type'] ?? null) === 'group'
        ) {
            $this->conditions[$groupIndex]['conditions'][] = $tagCondition;
        } else {
            $this->conditions[] = $tagCondition;
        }
    }

    public function addGroup(): void
    {
        $this->conditions[] = [
            'type' => 'group',
            'logic' => 'and',
            'conditions' => [['type' => 'tag', 'tag' => '', 'operator' => 'equals']],
        ];
    }

    public function removeCondition(int $index, ?int $groupIndex = null): void
    {
        if ($groupIndex !== null
            && isset($this->conditions[$groupIndex])
            && ($this->conditions[$groupIndex]['type'] ?? null) === 'group'
        ) {
            array_splice($this->conditions[$groupIndex]['conditions'], $index, 1);

            if (empty($this->conditions[$groupIndex]['conditions'])) {
                $this->conditions[$groupIndex]['conditions'] = [['type' => 'tag', 'tag' => '', 'operator' => 'equals']];
            }
        } else {
            array_splice($this->conditions, $index, 1);

            if (empty($this->conditions)) {
                $this->conditions = [['type' => 'tag', 'tag' => '', 'operator' => 'equals']];
            }
        }
    }

    public function removeGroup(int $index): void
    {
        array_splice($this->conditions, $index, 1);

        if (empty($this->conditions)) {
            $this->conditions = [['type' => 'tag', 'tag' => '', 'operator' => 'equals']];
        }
    }

    public function toggleLogic(?int $groupIndex = null): void
    {
        if ($groupIndex !== null
            && isset($this->conditions[$groupIndex])
            && ($this->conditions[$groupIndex]['type'] ?? null) === 'group'
        ) {
            $this->conditions[$groupIndex]['logic'] = ($this->conditions[$groupIndex]['logic'] ?? 'and') === 'and' ? 'or' : 'and';
        } else {
            $this->logic = $this->logic === 'and' ? 'or' : 'and';
        }
    }

    public function getCriteriaJson(): string
    {
        $filled = array_values(array_filter($this->conditions, function (array $condition): bool {
            if (($condition['type'] ?? null) === 'group') {
                return count(array_filter(
                    $condition['conditions'] ?? [],
                    fn (array $c) => isset($c['tag']) && $c['tag'] !== ''
                )) > 0;
            }

            return isset($condition['tag']) && $condition['tag'] !== '';
        }));

        if (empty($filled)) {
            return '';
        }

        if (count($filled) === 1 && ($filled[0]['type'] ?? null) === 'tag') {
            return json_encode([
                'type' => 'tag',
                'tag' => $filled[0]['tag'],
                'operator' => $filled[0]['operator'] ?? 'equals',
            ]);
        }

        $serialized = array_map(function (array $condition): array {
            if (($condition['type'] ?? null) === 'group') {
                $subFilled = array_values(array_filter(
                    $condition['conditions'] ?? [],
                    fn (array $c) => isset($c['tag']) && $c['tag'] !== ''
                ));

                return [
                    'type' => 'group',
                    'logic' => $condition['logic'] ?? 'and',
                    'conditions' => array_map(fn (array $c) => [
                        'type' => 'tag',
                        'tag' => $c['tag'],
                        'operator' => $c['operator'] ?? 'equals',
                    ], $subFilled),
                ];
            }

            return [
                'type' => 'tag',
                'tag' => $condition['tag'],
                'operator' => $condition['operator'] ?? 'equals',
            ];
        }, $filled);

        return json_encode([
            'type' => 'group',
            'logic' => $this->logic,
            'conditions' => $serialized,
        ]);
    }

    public function render(): View
    {
        return view('livewire.smart-list-criteria-builder', [
            'tags' => Tag::orderBy('name')->pluck('name'),
            'criteriaJson' => $this->getCriteriaJson(),
        ]);
    }
}
