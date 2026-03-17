<x-layouts.app>
    <div class="flex flex-col gap-4 w-full">
        <h1 class="text-sm font-medium mb-2">Logbook</h1>
        @forelse ($grouped as $date => $items)
            <div class="flex flex-col gap-2">
                <h2 class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">
                    {{ \Carbon\Carbon::parse($date)->format('l, F j') }}
                </h2>
                @foreach ($items as $item)
                    <x-item-row :item="$item" :show-parent="false" />
                @endforeach
            </div>
        @empty
            <p class="text-[#706f6c] dark:text-[#A1A09A]">No items found</p>
        @endforelse
    </div>
</x-layouts.app>
