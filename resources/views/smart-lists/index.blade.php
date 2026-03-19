<x-layouts.app>
    <div class="flex flex-col gap-4 w-full" x-data="{
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
            return (el.dataset.name || '').toLowerCase().includes(this.search.toLowerCase());
        }
    }">
        <div class="flex items-center justify-between">
            <h1 class="text-sm font-medium">Smart lists</h1>
            <div class="flex gap-2">
                <a href="{{ route('smart-lists.index', ['sort' => $sort === 'today_desc' ? 'count_desc' : 'today_desc']) }}"
                    class="inline-block px-3 py-3 bg-transparent text-xs text-[#706f6c] dark:text-[#A1A09A] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm hover:bg-[#f5f5f2] dark:hover:bg-[#161615] transition-all cursor-pointer">
                    {{ $sort === 'today_desc' ? 'Sort: Today ↓' : 'Sort: Count ↓' }}
                </a>
                <a href="{{ route('smart-lists.create') }}"
                    class="inline-block px-3 py-3 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] border border-black dark:border-[#eeeeec] rounded-sm text-xs leading-normal hover:bg-black dark:hover:bg-white hover:border-black dark:hover:border-white transition-all cursor-pointer">
                    New smart list
                </a>
            </div>
        </div>

        @if (session('status'))
            <div class="text-xs text-[#1b1b18] dark:text-[#EDEDEC]">
                {{ session('status') }}
            </div>
        @endif

        <div x-show="open" x-cloak>
            <input x-ref="search" x-model="search" type="text" placeholder="Filter smart lists…"
                class="w-full border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm px-2 py-1 text-xs bg-[#FDFDFC] dark:bg-[#161615] outline-none focus:border-[#a0a09c] dark:focus:border-[#60605c] mb-2">
        </div>

        @forelse ($lists as $entry)
            @php
                /** @var \App\Models\SmartList $model */
                $model = $entry['model'];
                $count = $entry['count'];
                $todayCount = $entry['todayCount'];
                $anytimeCount = $entry['anytimeCount'];
            @endphp
            <div data-name="{{ $model->name }}" x-show="matchesItem($el)">
                <a href="{{ route('smart-lists.show', $model) }}"
                    class="block bg-white dark:bg-[#161615] shadow-[inset_0px_0px_0px_1px_rgba(26,26,0,0.16)] dark:shadow-[inset_0px_0px_0px_1px_#fffaed2d] rounded-lg p-4 lg:p-6">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-sm font-medium">{{ $model->name }}</span>
                            <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                {{ $count }} {{ Str::plural('item', $count) }}
                                @if ($todayCount > 0)
                                    | {{ $todayCount }} today
                                @endif
                                @if ($anytimeCount > 0)
                                    | {{ $anytimeCount }} anytime
                                @endif
                            </span>
                        </div>
                    </div>
                </a>
            </div>
        @empty
            <p class="text-[#706f6c] dark:text-[#A1A09A] text-xs">No smart lists yet.</p>
        @endforelse
    </div>
</x-layouts.app>
