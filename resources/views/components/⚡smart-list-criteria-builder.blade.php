<?php

use App\Models\Tag;
use Livewire\Component;

new class extends Component
{
    public string $logic = 'and';

    /** @var array<int, array{tag: string, operator: string}> */
    public array $conditions = [];

    public function mount(mixed $criteria = null): void
    {
        if (is_string($criteria) && $criteria !== '') {
            $criteria = json_decode($criteria, true);
        }

        if (! is_array($criteria) || empty($criteria)) {
            $this->conditions = [['tag' => '', 'operator' => 'equals']];

            return;
        }

        if (($criteria['type'] ?? null) === 'tag') {
            $this->conditions = [[
                'tag' => $criteria['tag'] ?? '',
                'operator' => $criteria['operator'] ?? 'equals',
            ]];

            return;
        }

        if (($criteria['type'] ?? null) === 'group') {
            $this->logic = $criteria['logic'] ?? 'and';
            $this->conditions = array_values(array_map(
                fn ($c) => ['tag' => $c['tag'] ?? '', 'operator' => $c['operator'] ?? 'equals'],
                $criteria['conditions'] ?? []
            ));
        }
    }

    public function addCondition(): void
    {
        $this->conditions[] = ['tag' => '', 'operator' => 'equals'];
    }

    public function removeCondition(int $index): void
    {
        array_splice($this->conditions, $index, 1);

        if ($this->conditions === []) {
            $this->conditions = [['tag' => '', 'operator' => 'equals']];
        }
    }

    /**
     * Build the criteria array from current state.
     *
     * @return array<string, mixed>
     */
    public function getCriteriaJson(): string
    {
        $validConditions = array_values(array_filter(
            $this->conditions,
            fn ($c) => isset($c['tag']) && $c['tag'] !== ''
        ));

        if (empty($validConditions)) {
            return '';
        }

        if (count($validConditions) === 1) {
            $criteria = [
                'type' => 'tag',
                'tag' => $validConditions[0]['tag'],
                'operator' => $validConditions[0]['operator'],
            ];
        } else {
            $criteria = [
                'type' => 'group',
                'logic' => $this->logic,
                'conditions' => array_map(
                    fn ($c) => ['type' => 'tag', 'tag' => $c['tag'], 'operator' => $c['operator']],
                    $validConditions
                ),
            ];
        }

        return json_encode($criteria, JSON_PRETTY_PRINT);
    }

    public function with(): array
    {
        return [
            'availableTags' => Tag::orderBy('name')->get(),
            'criteriaJson' => $this->getCriteriaJson(),
        ];
    }
};
?>

<div class="flex flex-col gap-3">
    {{-- Logic toggle (only shown when more than one condition) --}}
    @if(count($conditions) > 1)
        <div class="flex items-center gap-2">
            <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">Match</span>
            <div class="flex rounded-sm border border-[#e3e3e0] dark:border-[#3E3E3A] overflow-hidden text-xs">
                <button
                    type="button"
                    wire:click="$set('logic', 'and')"
                    class="px-2.5 py-1 transition-colors {{ $logic === 'and' ? 'bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A]' : 'bg-[#FDFDFC] dark:bg-[#161615] text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f3] dark:hover:bg-[#1e1e1c]' }}"
                >
                    ALL
                </button>
                <button
                    type="button"
                    wire:click="$set('logic', 'or')"
                    class="px-2.5 py-1 transition-colors border-l border-[#e3e3e0] dark:border-[#3E3E3A] {{ $logic === 'or' ? 'bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A]' : 'bg-[#FDFDFC] dark:bg-[#161615] text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#f5f5f3] dark:hover:bg-[#1e1e1c]' }}"
                >
                    ANY
                </button>
            </div>
            <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">of the following conditions</span>
        </div>
    @endif

    {{-- Conditions list --}}
    <div class="flex flex-col gap-2">
        @foreach($conditions as $index => $condition)
            <div wire:key="condition-{{ $index }}" class="flex items-center gap-2">
                <span class="text-xs text-[#706f6c] dark:text-[#A1A09A] w-6 shrink-0">Tag</span>

                {{-- Operator select --}}
                <select
                    wire:model.live="conditions.{{ $index }}.operator"
                    class="border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm px-2 py-1 text-xs bg-[#FDFDFC] dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC]"
                >
                    <option value="equals">is</option>
                    <option value="not_equals">is not</option>
                </select>

                {{-- Tag select --}}
                <select
                    wire:model.live="conditions.{{ $index }}.tag"
                    class="flex-1 border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm px-2 py-1 text-xs bg-[#FDFDFC] dark:bg-[#161615] text-[#1b1b18] dark:text-[#EDEDEC]"
                >
                    <option value="">— select a tag —</option>
                    @foreach($availableTags as $tag)
                        <option value="{{ $tag->name }}" {{ ($condition['tag'] ?? '') === $tag->name ? 'selected' : '' }}>
                            {{ $tag->name }}
                        </option>
                    @endforeach
                </select>

                {{-- Remove condition button --}}
                <button
                    type="button"
                    wire:click="removeCondition({{ $index }})"
                    class="text-[#706f6c] dark:text-[#A1A09A] hover:text-red-500 dark:hover:text-red-400 transition-colors text-sm leading-none shrink-0"
                    title="Remove condition"
                >
                    &times;
                </button>
            </div>
        @endforeach
    </div>

    {{-- Add condition button --}}
    <div>
        <button
            type="button"
            wire:click="addCondition"
            class="text-xs text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-[#EDEDEC] transition-colors"
        >
            + Add condition
        </button>
    </div>

    {{-- Hidden input carrying the JSON for the parent form --}}
    <input type="hidden" name="criteria" value="{{ $criteriaJson }}">

    {{-- Read-only preview --}}
    <div class="flex flex-col gap-1 mt-1">
        <label class="text-[11px] text-[#706f6c] dark:text-[#A1A09A]">Resolves to</label>
        <textarea
            rows="4"
            readonly
            class="border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm px-2 py-1 text-xs bg-[#f5f5f3] dark:bg-[#0f0f0e] font-mono text-[#706f6c] dark:text-[#A1A09A] cursor-default select-all"
        >{{ $criteriaJson }}</textarea>
    </div>
</div>
