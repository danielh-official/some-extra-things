@php
    /** @var \App\Models\SmartList $smartList */
@endphp

@props([
    'cancelLink' => route('smart-lists.index')
])

<div
    class="w-full max-w-2xl text-[13px] leading-[20px] bg-white dark:bg-[#161615] dark:text-[#EDEDEC] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-lg p-6 lg:p-8 flex flex-col gap-4">
    <div class="flex items-center justify-between">
        <h1 class="font-medium text-sm">
            {{ $heading }}
        </h1>
    </div>

    <div class="flex flex-col gap-3">
        <div class="flex flex-col gap-1">
            <label for="name" class="text-xs font-medium">Name</label>
            <input id="name" name="name" type="text" value="{{ old('name', $smartList->name) }}"
                class="border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm px-2 py-1 text-xs bg-[#FDFDFC] dark:bg-[#161615]" required>
            @error('name')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>

        <div class="flex flex-col gap-1">
            <label class="text-xs font-medium">Criteria</label>
            <p class="text-[11px] text-[#706f6c] dark:text-[#A1A09A]">
                Write JSON that matches this structure. For a single tag condition:
                <code class="block mt-1 px-1.5 py-0.5 rounded-sm bg-[#FDFDFC] dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    {"type": "tag", "tag": "work", "operator": "equals"}
                </code>
                For a group of conditions with AND / OR:
                <code class="block mt-1 px-1.5 py-0.5 rounded-sm bg-[#FDFDFC] dark:bg-[#161615] border border-[#e3e3e0] dark:border-[#3E3E3A]">
                    {"type": "group", "logic": "and", "conditions": [
                    {"type": "tag", "tag": "work", "operator": "equals"},
                    {"type": "tag", "tag": "home", "operator": "not_equals"}
                    ]}
                </code>
            </p>
            <textarea name="criteria" rows="6"
                class="border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm px-2 py-1 text-xs bg-[#FDFDFC] dark:bg-[#161615] font-mono">{{ old('criteria', $smartList->criteria ? json_encode($smartList->criteria, JSON_PRETTY_PRINT) : '') }}</textarea>
            @error('criteria')
                <p class="text-xs text-red-600">{{ $message }}</p>
            @enderror
        </div>
    </div>

    <div class="flex items-center justify-between mt-2">
        <a href="{{ $cancelLink }}" class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
            Cancel
        </a>
        <button type="submit"
            class="inline-block px-4 py-1.5 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] border border-black dark:border-[#eeeeec] rounded-sm text-xs leading-normal hover:bg-black dark:hover:bg-white hover:border-black dark:hover:border-white transition-all cursor-pointer">
            Save smart list
        </button>
    </div>
</div>

