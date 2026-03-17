<x-layouts.app>
    <div class="flex flex-col gap-4 w-full"
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
        <h1 class="text-sm font-medium mb-2">Upcoming</h1>
        <div x-show="open" x-cloak>
            <input x-ref="search" x-model="search" type="text" placeholder="Filter items…"
                class="w-full border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm px-2 py-1 text-xs bg-[#FDFDFC] dark:bg-[#161615] outline-none focus:border-[#a0a09c] dark:focus:border-[#60605c] mb-2">
        </div>
        @forelse ($grouped as $date => $items)
            <div class="flex flex-col gap-2" x-show="matchesGroup($el)">
                <hr class="border-[#e5e5e5] dark:border-[#2a2a28]" />
                <h2 class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">
                    @php $parsedDate = \Carbon\Carbon::parse($date); @endphp
                    {{ $parsedDate->format('l, F j') }}
                    ({{ $parsedDate->isTomorrow() ? 'Tomorrow' : 'in ' . today()->diffInDays($parsedDate) . ' days' }})
                </h2>
                @foreach ($items as $item)
                    <div data-item-search data-title="{{ $item->title }}" data-notes="{{ $item->notes ?? '' }}" x-show="matchesItem($el)">
                        <x-item-row :item="$item" />
                    </div>
                @endforeach
            </div>
        @empty
            <p class="text-[#706f6c] dark:text-[#A1A09A]">No items found</p>
        @endforelse
    </div>
</x-layouts.app>
