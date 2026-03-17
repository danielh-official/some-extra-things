<x-layouts.app>
    <div class="flex flex-col gap-4 w-full">
        <div>
            <a href="{{ route('smart-lists.index') }}">&larr; Go Back</a>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex flex-col">
                <h1 class="text-sm font-medium">{{ $smartList->name }}</h1>
                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                    {{ $grouped->flatten()->count() }} {{ Str::plural('item', $grouped->flatten()->count()) }}
                    @if ($invert)
                        <span class="italic">(inverted)</span>
                    @endif
                </p>
            </div>

            <div class="flex items-center gap-2">
                @if ($grouped->flatten()->count() > 0)
                    <a href="{{ $thingsLink }}"
                        class="inline-block px-3 py-1 bg-transparent text-xs text-[#706f6c] dark:text-[#A1A09A] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm hover:bg-[#f5f5f2] dark:hover:bg-[#161615] transition-all cursor-pointer">
                        Open in Things
                    </a>
                @endif

                <a href="{{ route('smart-lists.show', [$smartList, 'invert' => $invert ? 0 : 1]) }}"
                    class="inline-block px-3 py-1 text-xs {{ $invert ? 'bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] border border-black dark:border-[#eeeeec]' : 'bg-transparent text-[#706f6c] dark:text-[#A1A09A] border border-[#e3e3e0] dark:border-[#3E3E3A] hover:bg-[#f5f5f2] dark:hover:bg-[#161615]' }} rounded-sm leading-normal transition-all cursor-pointer">
                    Invert
                </a>

                <form method="POST" action="{{ route('smart-lists.kanban', $smartList) }}">
                    @csrf
                    <button type="submit"
                        class="inline-block px-3 py-1 text-xs {{ $kanban === 'horizontal' ? 'bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] border border-black dark:border-[#eeeeec]' : 'bg-transparent text-[#706f6c] dark:text-[#A1A09A] border border-[#e3e3e0] dark:border-[#3E3E3A] hover:bg-[#f5f5f2] dark:hover:bg-[#161615]' }} rounded-sm leading-normal transition-all cursor-pointer">
                        Horizontal
                    </button>
                </form>

                <a href="{{ route('smart-lists.duplicate', $smartList) }}"
                    class="inline-block px-3 py-1 bg-transparent text-xs text-[#706f6c] dark:text-[#A1A09A] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm hover:bg-[#f5f5f2] dark:hover:bg-[#161615] transition-all cursor-pointer">
                    Duplicate
                </a>

                <a href="{{ route('smart-lists.edit', $smartList) }}"
                    class="inline-block px-3 py-1 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] border border-black dark:border-[#eeeeec] rounded-sm text-xs leading-normal hover:bg:black dark:hover:bg-white hover:border-black dark:hover:border:white transition-all cursor-pointer">
                    Edit
                </a>

                <form method="POST" action="{{ route('smart-lists.destroy', $smartList) }}"
                    onsubmit="return confirm('Delete this smart list?');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-block px-3 py-1 bg-transparent text-xs text-[#706f6c] dark:text-[#A1A09A] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm hover:bg-[#f5f5f2] dark:hover:bg-[#161615] transition-all cursor-pointer">
                        Delete
                    </button>
                </form>
            </div>
        </div>

        @if ($kanban === 'horizontal')
            <div class="flex gap-4 overflow-x-auto pb-2">
                @forelse ($grouped as $bucket => $items)
                    <div class="flex flex-col gap-2 w-64 shrink-0 min-w-0">
                        <h2 class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">{{ $bucket }}</h2>
                        @foreach ($items as $item)
                            <div class="min-w-0 truncate">
                                <x-item-row :item="$item" />
                            </div>
                        @endforeach
                    </div>
                @empty
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">No matching items.</p>
                @endforelse
            </div>
        @else
            @forelse ($grouped as $bucket => $items)
                <div class="flex flex-col gap-2">
                    <h2 class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">{{ $bucket }}</h2>
                    @foreach ($items as $item)
                        <x-item-row :item="$item" />
                    @endforeach
                </div>
            @empty
                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                    No matching items.
                </p>
            @endforelse
        @endif
    </div>
</x-layouts.app>
