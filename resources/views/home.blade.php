<x-layouts.app>
    <div class="flex flex-col gap-4">
        @forelse ($items as $item)
            <div
                class="bg-white dark:bg-[#161615] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-lg p-6 lg:p-8">
                <h2>{{ $item->title }}</h2>
            </div>
        @empty
            <div>
                <p class="text-[#706f6c] dark:text-[#A1A09A]">No items found</p>
            </div>
        @endforelse
    </div>
</x-layouts.app>