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
        <div class="flex items-center justify-between">
            <h1 class="text-sm font-medium">{{ $area->title }}</h1>
            <a href="things:///show?id={{ $area->id }}"
                class="inline-block px-3 py-1 bg-transparent text-xs text-[#706f6c] dark:text-[#A1A09A] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm hover:bg-[#f5f5f2] dark:hover:bg-[#161615] transition-all cursor-pointer">
                Open in Things
            </a>
        </div>

        <div x-show="open" x-cloak>
            <input x-ref="search" x-model="search" type="text" placeholder="Filter items…" aria-label="Filter items"
                class="w-full border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm px-2 py-1 text-xs bg-[#FDFDFC] dark:bg-[#161615] outline-none focus:border-[#a0a09c] dark:focus:border-[#60605c] mb-2">
        </div>

        @if ($items->isEmpty() && $upcomingItems->isEmpty() && $somedayItems->isEmpty())
            <p class="text-[#706f6c] dark:text-[#A1A09A]">No items in this area.</p>
        @else
            @foreach ($items->groupBy('type') as $type => $typeItems)
                <div class="flex flex-col gap-2" x-show="matchesGroup($el)">
                    <h2 class="text-xs font-semibold uppercase tracking-wide text-[#a0a09c] dark:text-[#60605c]">{{ Str::plural($type) }}</h2>
                    @foreach ($typeItems as $item)
                        <div data-item-search data-title="{{ $item->title }}" data-notes="{{ $item->notes ?? '' }}" x-show="matchesItem($el)">
                            <x-item-row :item="$item" :show-parent="false" />
                        </div>
                    @endforeach
                </div>
            @endforeach

            @if ($upcomingItems->isNotEmpty())
                <div class="border-t border-[#e5e5e5] dark:border-[#2a2a28] pt-4 flex flex-col gap-4" x-show="matchesGroup($el)">
                    <h2 class="text-xs font-semibold uppercase tracking-wide text-[#a0a09c] dark:text-[#60605c]">Upcoming</h2>
                    @foreach ($upcomingItems->groupBy('type') as $type => $typeItems)
                        <div class="flex flex-col gap-2" x-show="matchesGroup($el)">
                            <h3 class="text-xs font-medium text-[#a0a09c] dark:text-[#60605c]">{{ Str::plural($type) }}</h3>
                            @foreach ($typeItems as $item)
                                <div data-item-search data-title="{{ $item->title }}" data-notes="{{ $item->notes ?? '' }}" x-show="matchesItem($el)">
                                    <x-item-row :item="$item" :show-parent="false" />
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endif

            @if ($somedayItems->isNotEmpty())
                <div class="border-t border-[#e5e5e5] dark:border-[#2a2a28] pt-4 flex flex-col gap-4" x-show="matchesGroup($el)">
                    <h2 class="text-xs font-semibold uppercase tracking-wide text-[#a0a09c] dark:text-[#60605c]">Someday</h2>
                    @foreach ($somedayItems->groupBy('type') as $type => $typeItems)
                        <div class="flex flex-col gap-2" x-show="matchesGroup($el)">
                            <h3 class="text-xs font-medium text-[#a0a09c] dark:text-[#60605c]">{{ Str::plural($type) }}</h3>
                            @foreach ($typeItems as $item)
                                <div data-item-search data-title="{{ $item->title }}" data-notes="{{ $item->notes ?? '' }}" x-show="matchesItem($el)">
                                    <x-item-row :item="$item" :show-parent="false" />
                                </div>
                            @endforeach
                        </div>
                    @endforeach
                </div>
            @endif
        @endif
    </div>
</x-layouts.app>
