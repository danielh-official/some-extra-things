<x-layouts.app>
    @forelse ($items as $item)
        <div>
            <h2>{{ $item->title }}</h2>
        </div>
    @empty
        <div>
            <p class="text-[#706f6c] dark:text-[#A1A09A]">No items found</p>
        </div>
    @endforelse
</x-layouts.app>