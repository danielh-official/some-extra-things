<x-layouts.app>
    <div class="flex flex-col gap-4 w-full">
        <div>
            <a href="{{ route('tags') }}" class="text-xs text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-white">&larr; All Tags</a>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex flex-col gap-1">
                <h1 class="text-sm font-medium">{{ $tagModel->name }}</h1>
                @if ($tagModel->parent_tag_id)
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $tagModel->ancestryPath() }}</p>
                @endif
            </div>
            @if ($tagModel->things_id)
                <a href="things:///show?id={{ $tagModel->things_id }}"
                    class="inline-block px-3 py-1 bg-transparent text-xs text-[#706f6c] dark:text-[#A1A09A] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm hover:bg-[#f5f5f2] dark:hover:bg-[#161615] transition-all cursor-pointer">
                    Open in Things
                </a>
            @endif
        </div>

        @forelse ($items as $type => $typeItems)
            <div class="flex flex-col gap-2">
                <h2 class="text-xs font-semibold uppercase tracking-wide text-[#a0a09c] dark:text-[#60605c]">{{ Str::plural($type) }}</h2>
                @foreach ($typeItems as $item)
                    <x-item-row :item="$item" />
                @endforeach
            </div>
        @empty
            <p class="text-[#706f6c] dark:text-[#A1A09A]">No items with this tag.</p>
        @endforelse
    </div>
</x-layouts.app>
