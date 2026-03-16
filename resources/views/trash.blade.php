<x-layouts.app>
    <div class="flex flex-col gap-2 w-full max-w-2xl">
        <h1 class="text-sm font-medium mb-2">Trash</h1>
        @forelse ($items as $item)
            <h3 class="text-sm font-medium">
                <a href="things:///show?id={{ $item->id }}">{{ $item->title }}</a>
            </h3>
        @empty
            <p class="text-[#706f6c] dark:text-[#A1A09A]">No items found</p>
        @endforelse
    </div>
</x-layouts.app>
