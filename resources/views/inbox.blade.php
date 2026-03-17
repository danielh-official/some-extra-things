<x-layouts.app>
    <div class="flex flex-col gap-2 w-full"
        x-data="{
            search: '',
            open: false,
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
            matchesItem(el) {
                if (!this.search) return true;
                const q = this.search.toLowerCase();
                return (el.dataset.title || '').toLowerCase().includes(q) || (el.dataset.notes || '').toLowerCase().includes(q);
            },
            matchesGroup(el) {
                if (!this.search) return true;
                return Array.from(el.querySelectorAll('[data-item-search]')).some(i => this.matchesItem(i));
            }
        }">
        <h1 class="text-sm font-medium mb-2">Inbox</h1>
        <div x-show="open" x-cloak>
            <input x-ref="search" x-model="search" type="text" placeholder="Filter items…"
                class="w-full border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm px-2 py-1 text-xs bg-[#FDFDFC] dark:bg-[#161615] outline-none focus:border-[#a0a09c] dark:focus:border-[#60605c] mb-2">
        </div>
        @forelse ($items as $item)
            <div data-item-search data-title="{{ $item->title }}" data-notes="{{ $item->notes ?? '' }}" x-show="matchesItem($el)">
                <x-item-row :item="$item" />
            </div>
        @empty
            <p class="text-[#706f6c] dark:text-[#A1A09A]">No items found</p>
        @endforelse
    </div>
</x-layouts.app>
