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
            <div class="flex flex-col">
                <h1 class="text-sm font-medium">All</h1>
                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                    {{ $grouped->flatten()->count() }} {{ Str::plural('item', $grouped->flatten()->count()) }}
                </p>
            </div>

            <div class="flex items-center gap-2">
                <form method="POST" action="{{ route('all.kanban') }}">
                    @csrf
                    <button type="submit"
                        class="inline-block px-3 py-1 text-xs {{ $kanban === 'horizontal' ? 'bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] border border-black dark:border-[#eeeeec]' : 'bg-transparent text-[#706f6c] dark:text-[#A1A09A] border border-[#e3e3e0] dark:border-[#3E3E3A] hover:bg-[#f5f5f2] dark:hover:bg-[#161615]' }} rounded-sm leading-normal transition-all cursor-pointer">
                        Horizontal
                    </button>
                </form>
            </div>
        </div>

        <div x-show="open" x-cloak>
            <input x-ref="search" x-model="search" type="text" placeholder="Filter items…"
                class="w-full border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm px-2 py-1 text-xs bg-[#FDFDFC] dark:bg-[#161615] outline-none focus:border-[#a0a09c] dark:focus:border-[#60605c] mb-2">
        </div>

        @if ($kanban === 'horizontal')
            <div class="flex gap-4 overflow-x-auto pb-2">
                @forelse ($grouped as $bucket => $items)
                    <div class="flex flex-col gap-2 w-96 shrink-0 min-w-0 border p-4" x-show="matchesGroup($el)">
                        <h2 class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">{{ $bucket }}</h2>
                        @if ($bucket === 'Today')
                            @foreach ($items->where('evening', false) as $item)
                                <div data-item-search data-title="{{ $item->title }}" data-notes="{{ $item->notes ?? '' }}" x-show="matchesItem($el)" class="min-w-0 truncate">
                                    <x-item-row :item="$item" hide-tags />
                                </div>
                            @endforeach
                            @if ($items->where('evening', true)->isNotEmpty())
                                <div x-show="matchesGroup($el)">
                                    <p class="text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] mt-2">Evening</p>
                                    @foreach ($items->where('evening', true) as $item)
                                        <div data-item-search data-title="{{ $item->title }}" data-notes="{{ $item->notes ?? '' }}" x-show="matchesItem($el)" class="min-w-0 truncate">
                                            <x-item-row :item="$item" hide-tags />
                                        </div>
                                    @endforeach
                                </div>
                            @endif
                        @elseif ($bucket === 'Upcoming')
                            @foreach ($items->groupBy(fn ($item) => $item->start_date->format('Y-m-d')) as $date => $dateItems)
                                @php $days = (int) today()->diffInDays(\Carbon\Carbon::parse($date)); @endphp
                                <div x-show="matchesGroup($el)">
                                    <p class="text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] mt-2 first:mt-0">{{ \Carbon\Carbon::parse($date)->format('M j') }} <span class="font-normal">({{ $days === 1 ? 'Tomorrow' : $days.' days' }})</span></p>
                                    @foreach ($dateItems as $item)
                                        <div data-item-search data-title="{{ $item->title }}" data-notes="{{ $item->notes ?? '' }}" x-show="matchesItem($el)" class="min-w-0 truncate">
                                            <x-item-row :item="$item" hide-tags />
                                        </div>
                                    @endforeach
                                </div>
                            @endforeach
                        @else
                            @foreach ($items as $item)
                                <div data-item-search data-title="{{ $item->title }}" data-notes="{{ $item->notes ?? '' }}" x-show="matchesItem($el)" class="min-w-0 truncate">
                                    <x-item-row :item="$item" hide-tags />
                                </div>
                            @endforeach
                        @endif
                    </div>
                @empty
                    <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">No items.</p>
                @endforelse
            </div>
        @else
            @forelse ($grouped as $bucket => $items)
                <div class="flex flex-col gap-2" x-show="matchesGroup($el)">
                    <h2 class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">{{ $bucket }}</h2>
                    @if ($bucket === 'Today')
                        @foreach ($items->where('evening', false) as $item)
                            <div data-item-search data-title="{{ $item->title }}" data-notes="{{ $item->notes ?? '' }}" x-show="matchesItem($el)">
                                <x-item-row :item="$item" />
                            </div>
                        @endforeach
                        @if ($items->where('evening', true)->isNotEmpty())
                            <div x-show="matchesGroup($el)">
                                <p class="text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] mt-2">Evening</p>
                                @foreach ($items->where('evening', true) as $item)
                                    <div data-item-search data-title="{{ $item->title }}" data-notes="{{ $item->notes ?? '' }}" x-show="matchesItem($el)">
                                        <x-item-row :item="$item" />
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    @elseif ($bucket === 'Upcoming')
                        @foreach ($items->groupBy(fn ($item) => $item->start_date->format('Y-m-d')) as $date => $dateItems)
                            @php $days = (int) today()->diffInDays(\Carbon\Carbon::parse($date)); @endphp
                            <div x-show="matchesGroup($el)">
                                <p class="text-xs font-medium text-[#706f6c] dark:text-[#A1A09A] mt-2 first:mt-0">{{ \Carbon\Carbon::parse($date)->format('M j') }} <span class="font-normal">({{ $days === 1 ? 'Tomorrow' : $days.' days' }})</span></p>
                                @foreach ($dateItems as $item)
                                    <div data-item-search data-title="{{ $item->title }}" data-notes="{{ $item->notes ?? '' }}" x-show="matchesItem($el)">
                                        <x-item-row :item="$item" />
                                    </div>
                                @endforeach
                            </div>
                        @endforeach
                    @else
                        @foreach ($items as $item)
                            <div data-item-search data-title="{{ $item->title }}" data-notes="{{ $item->notes ?? '' }}" x-show="matchesItem($el)">
                                <x-item-row :item="$item" />
                            </div>
                        @endforeach
                    @endif
                </div>
            @empty
                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">No items.</p>
            @endforelse
        @endif
    </div>
</x-layouts.app>
