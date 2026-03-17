<x-layouts.app>
    <div class="flex flex-col gap-4 w-full">
        <h1 class="text-sm font-medium mb-2">Anytime</h1>
        @forelse ($grouped as $parent => $items)
            <div class="flex flex-col gap-2">
                @if ($parent !== '')
                    <h2 class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">{{ $parent }}</h2>
                @endif
                @foreach ($items as $item)
                    <x-item-row :item="$item" />
                @endforeach
            </div>
        @empty
            <p class="text-[#706f6c] dark:text-[#A1A09A]">No items found</p>
        @endforelse
    </div>
</x-layouts.app>
