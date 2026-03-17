<x-layouts.app>
    <div class="flex flex-col gap-4 w-full max-w-2xl">
        <h1 class="text-sm font-medium mb-2">Tags</h1>
        @forelse ($tags as $tag)
            <div class="flex items-center justify-between">
                <span class="shrink-0 whitespace-nowrap rounded-full bg-[#f0f0ec] dark:bg-[#1e1e1c] px-2 py-0.5 text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $tag->name }}</span>
                <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $tag->items_count }} {{ Str::plural('item', $tag->items_count) }}</span>
            </div>
        @empty
            <p class="text-[#706f6c] dark:text-[#A1A09A]">No tags found</p>
        @endforelse
    </div>
</x-layouts.app>
