<?php
use App\Models\Tag;
use Livewire\Component;

new class extends Component {
    public string $logic = 'and';

    /** @var array<int, array<string, mixed>> */
    public array $conditions = [];

    /** @var array<int, string> */
    public array $tags = [];

    public function mount(?string $criteria = null): void
    {
        $this->tags = Tag::orderBy('name')->pluck('name')->all();
        if ($criteria) {
            $decoded = json_decode($criteria, true);

            if (is_array($decoded)) {
                if (($decoded['type'] ?? null) === 'tag') {
                    $this->conditions = [
                        [
                            'type' => 'tag',
                            'tag' => $decoded['tag'] ?? '',
                            'operator' => $decoded['operator'] ?? 'equals',
                        ],
                    ];
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
        return array_values(
            array_map(function (array $condition): array {
                if (($condition['type'] ?? null) === 'group') {
                    $subConditions = array_filter($condition['conditions'] ?? [], fn($c) => is_array($c) && ($c['type'] ?? null) === 'tag');

                    return [
                        'type' => 'group',
                        'logic' => $condition['logic'] ?? 'and',
                        'conditions' => array_values(
                            array_map(
                                fn(array $c) => [
                                    'type' => 'tag',
                                    'tag' => $c['tag'] ?? '',
                                    'operator' => $c['operator'] ?? 'equals',
                                ],
                                $subConditions,
                            ),
                        ),
                    ];
                }

                return [
                    'type' => 'tag',
                    'tag' => $condition['tag'] ?? '',
                    'operator' => $condition['operator'] ?? 'equals',
                ];
            }, $conditions),
        );
    }

    public function addCondition(?int $groupIndex = null): void
    {
        $tagCondition = ['type' => 'tag', 'tag' => '', 'operator' => 'equals'];

        if ($groupIndex !== null && isset($this->conditions[$groupIndex]) && ($this->conditions[$groupIndex]['type'] ?? null) === 'group') {
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
        if ($groupIndex !== null && isset($this->conditions[$groupIndex]) && ($this->conditions[$groupIndex]['type'] ?? null) === 'group') {
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
        if ($groupIndex !== null && isset($this->conditions[$groupIndex]) && ($this->conditions[$groupIndex]['type'] ?? null) === 'group') {
            $this->conditions[$groupIndex]['logic'] = ($this->conditions[$groupIndex]['logic'] ?? 'and') === 'and' ? 'or' : 'and';
        } else {
            $this->logic = $this->logic === 'and' ? 'or' : 'and';
        }
    }

    public function getCriteriaJson(): string
    {
        $filled = array_values(
            array_filter($this->conditions, function (array $condition): bool {
                if (($condition['type'] ?? null) === 'group') {
                    return count(array_filter($condition['conditions'] ?? [], fn(array $c) => isset($c['tag']) && $c['tag'] !== '')) > 0;
                }

                return isset($condition['tag']) && $condition['tag'] !== '';
            }),
        );

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
                $subFilled = array_values(array_filter($condition['conditions'] ?? [], fn(array $c) => isset($c['tag']) && $c['tag'] !== ''));

                return [
                    'type' => 'group',
                    'logic' => $condition['logic'] ?? 'and',
                    'conditions' => array_map(
                        fn(array $c) => [
                            'type' => 'tag',
                            'tag' => $c['tag'],
                            'operator' => $c['operator'] ?? 'equals',
                        ],
                        $subFilled,
                    ),
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
};
?>

<div>
    <input type="hidden" name="criteria" value="{{ $this->getCriteriaJson() }}">

    <div class="flex flex-col gap-1.5">
        {{-- Root logic toggle (only shown when multiple conditions) --}}
        @if (count($conditions) > 1)
            <div class="flex items-center gap-2 mb-1">
                <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">Match</span>
                <button type="button" wire:click="toggleLogic"
                    class="text-xs px-2 py-0.5 rounded border border-[#e3e3e0] dark:border-[#3E3E3A] bg-[#FDFDFC] dark:bg-[#161615] hover:bg-[#f5f5f3] dark:hover:bg-[#1e1e1c]">
                    {{ strtoupper($logic) }}
                </button>
                <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">of the following</span>
            </div>
        @endif

        @foreach ($conditions as $idx => $condition)
            @if (($condition['type'] ?? 'tag') === 'tag')
                {{-- Tag condition row --}}
                <div wire:key="condition-{{ $idx }}" class="flex items-center gap-2">
                    <span class="text-xs text-[#706f6c] dark:text-[#A1A09A] w-6 shrink-0">tag</span>
                    <select wire:model.live="conditions.{{ $idx }}.operator"
                        class="text-xs border border-[#e3e3e0] dark:border-[#3E3E3A] rounded px-1 py-0.5 bg-[#FDFDFC] dark:bg-[#161615]">
                        <option value="equals">equals</option>
                        <option value="not_equals">does not equal</option>
                    </select>
                    <input type="text" wire:model.live="conditions.{{ $idx }}.tag" list="tag-options"
                        placeholder="tag name"
                        class="text-xs border border-[#e3e3e0] dark:border-[#3E3E3A] rounded px-2 py-0.5 bg-[#FDFDFC] dark:bg-[#161615] flex-1 min-w-0">
                    <button type="button" wire:click="removeCondition({{ $idx }})"
                        class="text-xs text-[#706f6c] dark:text-[#A1A09A] hover:text-red-500 shrink-0">×</button>
                </div>
            @else
                {{-- Sub-group --}}
                <div wire:key="group-{{ $idx }}"
                    class="flex flex-col gap-1 pl-3 border-l-2 border-[#e3e3e0] dark:border-[#3E3E3A]">
                    {{-- Sub-group logic toggle --}}
                    @if (count($condition['conditions'] ?? []) > 1)
                        <div class="flex items-center gap-2 mb-0.5">
                            <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">Match</span>
                            <button type="button" wire:click="toggleLogic({{ $idx }})"
                                class="text-xs px-2 py-0.5 rounded border border-[#e3e3e0] dark:border-[#3E3E3A] bg-[#FDFDFC] dark:bg-[#161615] hover:bg-[#f5f5f3] dark:hover:bg-[#1e1e1c]">
                                {{ strtoupper($condition['logic'] ?? 'and') }}
                            </button>
                            <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">of the following</span>
                        </div>
                    @endif

                    @foreach ($condition['conditions'] ?? [] as $subIdx => $subCondition)
                        <div wire:key="group-{{ $idx }}-condition-{{ $subIdx }}"
                            class="flex items-center gap-2">
                            <span class="text-xs text-[#706f6c] dark:text-[#A1A09A] w-6 shrink-0">tag</span>
                            <select
                                wire:model.live="conditions.{{ $idx }}.conditions.{{ $subIdx }}.operator"
                                class="text-xs border border-[#e3e3e0] dark:border-[#3E3E3A] rounded px-1 py-0.5 bg-[#FDFDFC] dark:bg-[#161615]">
                                <option value="equals">equals</option>
                                <option value="not_equals">does not equal</option>
                            </select>
                            <input type="text"
                                wire:model.live="conditions.{{ $idx }}.conditions.{{ $subIdx }}.tag"
                                list="tag-options" placeholder="tag name"
                                class="text-xs border border-[#e3e3e0] dark:border-[#3E3E3A] rounded px-2 py-0.5 bg-[#FDFDFC] dark:bg-[#161615] flex-1 min-w-0">
                            <button type="button"
                                wire:click="removeCondition({{ $subIdx }}, {{ $idx }})"
                                class="text-xs text-[#706f6c] dark:text-[#A1A09A] hover:text-red-500 shrink-0">×</button>
                        </div>
                    @endforeach

                    <div class="flex items-center gap-3 mt-0.5">
                        <button type="button" wire:click="addCondition({{ $idx }})"
                            class="text-xs text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-white">+
                            condition</button>
                        <button type="button" wire:click="removeGroup({{ $idx }})"
                            class="text-xs text-red-400 hover:text-red-600 ml-auto">Remove group</button>
                    </div>
                </div>
            @endif
        @endforeach

        {{-- Add buttons --}}
        <div class="flex items-center gap-3 mt-1">
            <button type="button" wire:click="addCondition"
                class="text-xs text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-white">+ Add
                condition</button>
            <button type="button" wire:click="addGroup"
                class="text-xs text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-white">+ Add
                group</button>
        </div>
    </div>

    <datalist id="tag-options">
        @foreach ($tags as $tag)
            <option value="{{ $tag }}">
        @endforeach
    </datalist>
</div>
