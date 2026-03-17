<x-layouts.app>
    <div class="flex flex-col gap-4 w-full">
        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <h1 class="text-sm font-medium">Tags</h1>
                <a href="{{ route('tags', ['sort' => $sort === 'count_desc' ? 'name' : 'count_desc']) }}"
                    class="text-xs {{ $sort === 'count_desc' ? 'text-[#1b1b18] dark:text-white font-medium' : 'text-[#706f6c] dark:text-[#A1A09A]' }} hover:underline">
                    {{ $sort === 'count_desc' ? 'Sort: Count ↓' : 'Sort: Name' }}
                </a>
            </div>
            <form method="POST" action="{{ route('tags.sync') }}">
                @csrf
                <button type="submit"
                    class="inline-block px-3 py-1 bg-transparent text-xs text-[#706f6c] dark:text-[#A1A09A] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm hover:bg-[#f5f5f2] dark:hover:bg-[#161615] transition-all cursor-pointer">
                    Sync from Things
                </button>
            </form>
        </div>

        @if (session('status'))
            <p class="text-xs text-green-600 dark:text-green-400">{{ session('status') }}</p>
        @endif
        @if (session('error'))
            <p class="text-xs text-red-600 dark:text-red-400">{{ session('error') }}</p>
        @endif

        @forelse ($tags as $tag)
            <div class="flex items-center justify-between">
                <div class="flex flex-col gap-0.5">
                    <a href="{{ route('tags.show', $tag->things_id ?? $tag->id) }}"
                        class="shrink-0 whitespace-nowrap rounded-full bg-[#f0f0ec] dark:bg-[#1e1e1c] px-2 py-0.5 text-xs text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#e8e8e4] dark:hover:bg-[#2a2a28] transition-colors">{{ $tag->name }}</a>
                    @if ($tag->parent_tag_id)
                        <p class="text-xs text-[#a0a09c] dark:text-[#60605c] px-2">{{ $tag->ancestryPath() }}</p>
                    @endif
                </div>
                <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $tag->items_count }} {{ Str::plural('item', $tag->items_count) }}</span>
            </div>
        @empty
            <p class="text-[#706f6c] dark:text-[#A1A09A]">No tags found</p>
        @endforelse
    </div>
</x-layouts.app>
