<x-layouts.app>
    <div class="flex flex-col gap-2 w-full max-w-2xl">
        <h1 class="text-sm font-medium mb-2">Someday</h1>
        @forelse ($items as $item)
            <x-item-row :item="$item" />
        @empty
            <p class="text-[#706f6c] dark:text-[#A1A09A]">No items found</p>
        @endforelse
    </div>
</x-layouts.app>
