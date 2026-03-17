<x-layouts.app>
    <div class="flex flex-col gap-2 w-full">
        <h1 class="text-sm font-medium mb-2">Today</h1>
        @forelse ($todayItems as $item)
            <x-item-row :item="$item" />
        @empty
            <p class="text-[#706f6c] dark:text-[#A1A09A]">No items found</p>
        @endforelse

        @if ($eveningItems->isNotEmpty())
            <h2 class="text-sm font-medium mt-4 mb-1 text-[#706f6c] dark:text-[#A1A09A]">This Evening</h2>
            @foreach ($eveningItems as $item)
                <x-item-row :item="$item" />
            @endforeach
        @endif
    </div>
</x-layouts.app>
