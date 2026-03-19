<x-layouts.app>
    <div class="flex flex-col gap-4 w-full"
        x-data="{
            search: '',
            open: false,
            syncing: false,
            init() {
                window.addEventListener('keydown', (e) => {
                    if (e.metaKey && e.key === 'f') {
                        e.preventDefault();
                        this.open = true;
                        this.$nextTick(() => this.$refs.search.focus());
                    }
                    if (e.key === 'Escape' && this.open) {
                        this.open = false;
                        this.search = '';
                    }
                });
            },
            matches(name, ancestry) {
                if (!this.search) return true;
                const q = this.search.toLowerCase();
                return name.toLowerCase().includes(q) || ancestry.toLowerCase().includes(q);
            }
        }">

        <div x-show="syncing" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/20 dark:bg-black/40">
            <div class="flex items-center gap-3 rounded-lg bg-white dark:bg-[#1c1c1a] px-5 py-3 shadow-lg">
                <svg class="size-4 animate-spin text-[#706f6c] dark:text-[#A1A09A]" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
                <span class="text-xs text-[#1b1b18] dark:text-[#EDEDEC]">Syncing from Things… Please wait for the import to finish.</span>
            </div>
        </div>

        <div class="flex items-center justify-between">
            <div class="flex items-center gap-2">
                <h1 class="text-sm font-medium">Tags</h1>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('tags.index', ['sort' => $sort === 'count_desc' ? 'name' : 'count_desc']) }}"
                    class="inline-block px-3 py-1 bg-transparent text-xs text-[#706f6c] dark:text-[#A1A09A] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm hover:bg-[#f5f5f2] dark:hover:bg-[#161615] transition-all cursor-pointer">
                    {{ $sort === 'count_desc' ? 'Sort: Count ↓' : 'Sort: Name' }}
                </a>
                <form method="POST" action="{{ route('tags.sync') }}" @submit="syncing = true">
                    @csrf
                    <button type="submit"
                        class="inline-block px-3 py-1 bg-transparent text-xs text-[#706f6c] dark:text-[#A1A09A] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm hover:bg-[#f5f5f2] dark:hover:bg-[#161615] transition-all cursor-pointer">
                        Sync from Things
                    </button>
                </form>
            </div>
        </div>

        <div x-show="open" x-cloak>
            <input x-ref="search" x-model="search" type="text" placeholder="Filter tags…"
                class="w-full border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm px-2 py-1 text-xs bg-[#FDFDFC] dark:bg-[#161615] outline-none focus:border-[#a0a09c] dark:focus:border-[#60605c]">
        </div>

        @if (session('status'))
            <p class="text-xs text-green-600 dark:text-green-400">{{ session('status') }}</p>
        @endif
        @if (session('error'))
            <p class="text-xs text-red-600 dark:text-red-400">{{ session('error') }}</p>
        @endif

        @forelse ($tags as $tag)
            <div class="flex items-center justify-between"
                x-show="matches('{{ addslashes($tag->name) }}', '{{ addslashes($tag->ancestryPath()) }}')">
                <div class="flex flex-col gap-0.5">
                    <a href="{{ route('tags.show', $tag->things_id ?? $tag->id) }}"
                        class="shrink-0 whitespace-nowrap rounded-full bg-[#f0f0ec] dark:bg-[#1e1e1c] px-2 py-0.5 text-xs text-[#706f6c] dark:text-[#A1A09A] hover:bg-[#e8e8e4] dark:hover:bg-[#2a2a28] transition-colors">{{ $tag->name }}</a>
                    @if ($tag->parent_tag_id)
                        <p class="text-xs text-[#a0a09c] dark:text-[#60605c] px-2">{{ $tag->ancestryPath() }}</p>
                    @endif
                </div>
                <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $tag->items_count }}
                    {{ Str::plural('item', $tag->items_count) }}</span>
            </div>
        @empty
            <p class="text-[#706f6c] dark:text-[#A1A09A]">No tags found</p>
        @endforelse
    </div>
</x-layouts.app>
