<x-layouts.app>
    <div class="flex flex-col gap-4 w-full max-w-2xl">
        <div class="flex items-center justify-between">
            <h1 class="text-sm font-medium">Smart lists</h1>
            <a href="{{ route('smart-lists.create') }}"
                class="inline-block px-4 py-1.5 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] border border-black dark:border-[#eeeeec] rounded-sm text-xs leading-normal hover:bg-black dark:hover:bg-white hover:border-black dark:hover:border-white transition-all cursor-pointer">
                New smart list
            </a>
        </div>

        @if (session('status'))
            <div class="text-xs text-[#1b1b18] dark:text-[#EDEDEC]">
                {{ session('status') }}
            </div>
        @endif

        @forelse ($lists as $entry)
            @php
                /** @var \App\Models\SmartList $model */
                $model = $entry['model'];
                $count = $entry['count'];
            @endphp
            <a href="{{ route('smart-lists.show', $model) }}"
                class="block bg-white dark:bg-[#161615] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-lg p-4 lg:p-6">
                <div class="flex items-center justify-between">
                    <div class="flex flex-col">
                        <span class="text-sm font-medium">{{ $model->name }}</span>
                        <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                            {{ $count }} {{ Str::plural('item', $count) }}
                        </span>
                    </div>
                </div>
            </a>
        @empty
            <p class="text-[#706f6c] dark:text-[#A1A09A] text-xs">No smart lists yet.</p>
        @endforelse
    </div>
</x-layouts.app>

