<x-layouts.app>
    <div class="flex flex-col gap-4 w-full max-w-2xl">
        <h1 class="text-sm font-medium mb-2">Upcoming</h1>
        @forelse ($grouped as $date => $items)
            <div class="flex flex-col gap-2">
                <hr class="border-[#e5e5e5] dark:border-[#2a2a28]" />
                <h2 class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">
                    @php $parsedDate = \Carbon\Carbon::parse($date); @endphp
                    {{ $parsedDate->format('l, F j') }}
                    ({{ $parsedDate->isTomorrow() ? 'Tomorrow' : 'in ' . today()->diffInDays($parsedDate) . ' days' }})
                </h2>
                @foreach ($items as $item)
                    <x-item-row :item="$item" />
                @endforeach
            </div>
        @empty
            <p class="text-[#706f6c] dark:text-[#A1A09A]">No items found</p>
        @endforelse
    </div>
</x-layouts.app>
