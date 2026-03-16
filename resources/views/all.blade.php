<x-layouts.app>
    <div class="flex flex-col gap-4">
        @forelse ($grouped as $key => $item)
            <div class="px-4 py-2">
                <h2>
                    <a href="things:///show?id={{ $item->first()->parent_id }}">{{ $key }}</a>
                </h2>
            </div>
            <hr class="border-[#e5e5e5] dark:border-[#2a2a28]" />
            <div style="margin-left: 1rem; margin-top: 1rem;" class="flex flex-col gap-2">
                @foreach ($item as $child)
                    <div>
                        <h3 class="text-sm font-medium">
                            <a href="things:///show?id={{ $child->id }}">
                                {{ $child->title }}
                            </a>
                        </h3>
                    </div>
                @endforeach
            </div>
        @empty
            <div>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">No items found</p>
            </div>
        @endforelse
    </div>
</x-layouts.app>
