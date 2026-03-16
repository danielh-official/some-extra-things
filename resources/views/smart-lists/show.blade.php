<x-layouts.app>
    <div class="flex flex-col gap-4 w-full">
        <div>
            <a href="{{ route('smart-lists.index') }}">&larr; Go Back</a>
        </div>
        
        <div class="flex items-center justify-between">
            <div class="flex flex-col">
                <h1 class="text-sm font-medium">{{ $smartList->name }}</h1>
                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                    {{ $items->total() }} {{ Str::plural('item', $items->total()) }}
                </p>
            </div>

            <div class="flex items-center gap-2">
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

        @forelse ($items as $item)
            <x-item-row :item="$item" />
        @empty
            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                No matching items.
            </p>
        @endforelse

        {{ $items->links() }}
    </div>
</x-layouts.app>

