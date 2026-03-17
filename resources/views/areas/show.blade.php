<x-layouts.app>
    <div class="flex flex-col gap-4 w-full">
        <div class="flex items-center justify-between">
            <h1 class="text-sm font-medium">{{ $area->title }}</h1>
            <a href="things:///show?id={{ $area->id }}"
                class="inline-block px-3 py-1 bg-transparent text-xs text-[#706f6c] dark:text-[#A1A09A] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm hover:bg-[#f5f5f2] dark:hover:bg-[#161615] transition-all cursor-pointer">
                Open in Things
            </a>
        </div>

        @if ($projects->isNotEmpty())
            <div class="flex flex-col gap-2">
                <h2 class="text-xs font-semibold uppercase tracking-wide text-[#a0a09c] dark:text-[#60605c]">Projects</h2>
                @foreach ($projects as $project)
                    <x-item-row :item="$project" :show-parent="false" />
                @endforeach
            </div>
        @endif

        @if ($todos->isNotEmpty())
            <div class="flex flex-col gap-2">
                <h2 class="text-xs font-semibold uppercase tracking-wide text-[#a0a09c] dark:text-[#60605c]">To-Dos</h2>
                @foreach ($todos as $todo)
                    <x-item-row :item="$todo" :show-parent="false" />
                @endforeach
            </div>
        @endif

        @if ($projects->isEmpty() && $todos->isEmpty())
            <p class="text-[#706f6c] dark:text-[#A1A09A]">No items in this area.</p>
        @endif
    </div>
</x-layouts.app>
