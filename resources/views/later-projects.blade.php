<x-layouts.app>
    <div class="flex flex-col gap-4 w-full max-w-2xl">
        <h1 class="text-sm font-medium mb-2">Later Projects</h1>
        @forelse ($grouped as $parent => $items)
            <div class="flex flex-col gap-2">
                @if ($parent !== '')
                    @php $parentId = $items->first()->parent_id; @endphp
                    <h2 class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">
                        <a href="things:///show?id={{ $parentId }}">{{ $parent }}</a>
                    </h2>
                @endif
                @foreach ($items as $item)
                    <x-item-row :item="$item" :show-parent="false" />
                @endforeach
            </div>
        @empty
            <p class="text-[#706f6c] dark:text-[#A1A09A]">No later projects</p>
        @endforelse
    </div>
</x-layouts.app>
