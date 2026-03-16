<x-layouts.app>
    <div class="flex flex-col gap-2 w-full max-w-2xl">
        <div class="flex items-center justify-between mb-2">
            <h1 class="text-sm font-medium">Trash</h1>
            @if ($items->isNotEmpty())
                <form method="POST" action="{{ route('trash.items.destroy') }}"
                    onsubmit="return confirm('Permanently delete all trashed items? This cannot be undone.');">
                    @csrf
                    @method('DELETE')
                    <button type="submit"
                        class="inline-block px-3 py-1 bg-red-600 hover:bg-red-700 text-white rounded-sm text-xs transition-colors cursor-pointer">
                        Permanently Delete All
                    </button>
                </form>
            @endif
        </div>
        @forelse ($items as $item)
            <x-item-row :item="$item" />
        @empty
            <p class="text-[#706f6c] dark:text-[#A1A09A]">No items found</p>
        @endforelse
    </div>
</x-layouts.app>
