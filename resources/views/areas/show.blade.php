<x-layouts.app>
    <div class="flex flex-col gap-4 w-full">
        <div class="flex items-center justify-between">
            <h1 class="text-sm font-medium">{{ $area->title }}</h1>
            <a href="things:///show?id={{ $area->id }}"
                class="inline-block px-3 py-1 bg-transparent text-xs text-[#706f6c] dark:text-[#A1A09A] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm hover:bg-[#f5f5f2] dark:hover:bg-[#161615] transition-all cursor-pointer">
                Open in Things
            </a>
        </div>

        @if ($items->isEmpty() && $upcomingItems->isEmpty() && $somedayItems->isEmpty())
            <p class="text-[#706f6c] dark:text-[#A1A09A]">No items in this area.</p>
        @else
            @foreach ($items->groupBy('type') as $type => $typeItems)
                <div class="flex flex-col gap-2">
                    <h2 class="text-xs font-semibold uppercase tracking-wide text-[#a0a09c] dark:text-[#60605c]">{{ Str::plural($type) }}</h2>
                    @foreach ($typeItems as $item)
                        <x-item-row :item="$item" :show-parent="false" />
                    @endforeach
                </div>
            @endforeach

            @if ($upcomingItems->isNotEmpty())
                <div class="border-t border-[#e5e5e5] dark:border-[#2a2a28] pt-4 flex flex-col gap-4">
                    <h2 class="text-xs font-semibold uppercase tracking-wide text-[#a0a09c] dark:text-[#60605c]">Upcoming</h2>
                    @foreach ($upcomingItems->groupBy('type') as $type => $typeItems)
                        <div class="flex flex-col gap-2">
                            <h3 class="text-xs font-medium text-[#a0a09c] dark:text-[#60605c]">{{ Str::plural($type) }}</h3>
                            @foreach ($typeItems as $item)
                                <x-item-row :item="$item" :show-parent="false" />
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($somedayItems->isNotEmpty())
                <div class="border-t border-[#e5e5e5] dark:border-[#2a2a28] pt-4 flex flex-col gap-4">
                    <h2 class="text-xs font-semibold uppercase tracking-wide text-[#a0a09c] dark:text-[#60605c]">Someday</h2>
                    @foreach ($somedayItems->groupBy('type') as $type => $typeItems)
                        <div class="flex flex-col gap-2">
                            <h3 class="text-xs font-medium text-[#a0a09c] dark:text-[#60605c]">{{ Str::plural($type) }}</h3>
                            @foreach ($typeItems as $item)
                                <x-item-row :item="$item" :show-parent="false" />
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</x-layouts.app>
