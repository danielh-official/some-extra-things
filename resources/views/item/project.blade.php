<x-layouts.app>
    <div class="flex flex-col gap-4 w-full">
        <div>
            <a href="javascript:history.back()" class="text-xs text-[#706f6c] dark:text-[#A1A09A] hover:text-[#1b1b18] dark:hover:text-white">&larr; Go Back</a>
        </div>

        <div class="flex flex-col gap-1">
            <div class="flex items-center gap-2">
                <h1 class="text-base font-bold">
                    {{ $item->title }}
                </h1>
                <span class="text-xs text-[#706f6c] dark:text-[#A1A09A] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-full px-2 py-0.5">{{ $item->type }}</span>
            </div>
            @if ($item->parent)
                <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                    @if ($parentItem)
                        @if ($parentItem->type === 'Area')
                            <a href="{{ route('areas.show', $parentItem) }}">{{ $item->parent }}</a>
                        @else
                            <a href="{{ route('projects.show', $parentItem) }}">{{ $item->parent }}</a>
                        @endif
                    @else
                        {{ $item->parent }}
                    @endif
                </p>
            @endif
        </div>

        <div class="flex flex-col gap-3 text-sm">
            @if ($item->status)
                <div class="flex gap-2">
                    <span class="text-xs font-medium w-28 shrink-0 text-[#706f6c] dark:text-[#A1A09A]">Status</span>
                    <span class="text-xs">{{ $item->status }}</span>
                </div>
            @endif

            @if ($item->start)
                <div class="flex gap-2">
                    <span class="text-xs font-medium w-28 shrink-0 text-[#706f6c] dark:text-[#A1A09A]">Start</span>
                    <span class="text-xs">{{ $item->start }}</span>
                </div>
            @endif

            @if ($item->start_date)
                <div class="flex gap-2">
                    <span class="text-xs font-medium w-28 shrink-0 text-[#706f6c] dark:text-[#A1A09A]">Start Date</span>
                    <span class="text-xs">
                        {{ $item->start_date->format('F j, Y') }}
                        @php
                            $days = (int) today()->diffInDays($item->start_date, false);
                        @endphp
                        @if ($days === 0)
                            <span class="text-[#706f6c] dark:text-[#A1A09A]">(Today)</span>
                        @elseif ($days === 1)
                            <span class="text-[#706f6c] dark:text-[#A1A09A]">(Tomorrow)</span>
                        @elseif ($days > 1)
                            <span class="text-[#706f6c] dark:text-[#A1A09A]">(in {{ $days }} days)</span>
                        @else
                            <span class="text-[#706f6c] dark:text-[#A1A09A]">({{ abs($days) }} {{ abs($days) === 1 ? 'day' : 'days' }} ago)</span>
                        @endif
                    </span>
                </div>
            @endif

            @if ($item->deadline)
                <div class="flex gap-2">
                    <span class="text-xs font-medium w-28 shrink-0 text-[#706f6c] dark:text-[#A1A09A]">Deadline</span>
                    <span class="text-xs">
                        {{ $item->deadline->format('F j, Y') }}
                        @php
                            $days = (int) today()->diffInDays($item->deadline, false);
                        @endphp
                        @if ($days === 0)
                            <span class="text-[#706f6c] dark:text-[#A1A09A]">(Today)</span>
                        @elseif ($days === 1)
                            <span class="text-[#706f6c] dark:text-[#A1A09A]">(Tomorrow)</span>
                        @elseif ($days > 1)
                            <span class="text-[#706f6c] dark:text-[#A1A09A]">(in {{ $days }} days)</span>
                        @else
                            <span class="text-[#706f6c] dark:text-[#A1A09A]">({{ abs($days) }} {{ abs($days) === 1 ? 'day' : 'days' }} ago)</span>
                        @endif
                    </span>
                </div>
            @endif

            @if ($item->completion_date)
                <div class="flex gap-2">
                    <span class="text-xs font-medium w-28 shrink-0 text-[#706f6c] dark:text-[#A1A09A]">Completed</span>
                    <span class="text-xs">{{ $item->completion_date->format('F j, Y') }}</span>
                </div>
            @endif

            @if (!empty($item->tags))
                <div class="flex gap-2 items-start">
                    <span class="text-xs font-medium w-28 shrink-0 text-[#706f6c] dark:text-[#A1A09A]">Tags</span>
                    <div class="flex flex-wrap gap-1">
                        @foreach ($item->tags as $tag)
                            <span class="rounded-full bg-[#f0f0ec] dark:bg-[#1e1e1c] px-2 py-0.5 text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $tag }}</span>
                        @endforeach
                    </div>
                </div>
            @endif

            @if ($notesHtml)
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-medium text-[#706f6c] dark:text-[#A1A09A]">Notes</span>
                    <div class="prose prose-sm dark:prose-invert max-w-none text-xs">{!! $notesHtml !!}</div>
                </div>
            @endif

            @if (!empty($item->checklist))
                <div class="flex flex-col gap-1">
                    <span class="text-xs font-medium text-[#706f6c] dark:text-[#A1A09A]">Checklist</span>
                    <ul class="flex flex-col gap-1">
                        @foreach ($item->checklist as $checklistItem)
                            <li class="text-xs">{{ is_array($checklistItem) ? ($checklistItem['title'] ?? '') : $checklistItem }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>

        @if ($childTodos && $childTodos->isNotEmpty())
            <div class="flex flex-col gap-3 pt-4 border-t border-[#e5e5e5] dark:border-[#2a2a28]">
                <span class="text-xs font-medium text-[#706f6c] dark:text-[#A1A09A]">To-Dos</span>
                @foreach ($childTodos as $headingName => $todos)
                    @if ($headingName !== '')
                        @php $firstTodo = $todos->first(); @endphp
                        <p class="text-xs font-semibold text-[#706f6c] dark:text-[#A1A09A]">
                            @if ($firstTodo->heading_id)
                                <a href="things:///show?id={{ $firstTodo->heading_id }}">{{ $headingName }}</a>
                            @else
                                {{ $headingName }}
                            @endif
                        </p>
                    @endif
                    @foreach ($todos as $todo)
                        <x-item-row :item="$todo" :show-parent="false" />
                    @endforeach
                @endforeach
            </div>
        @endif

        <div class="pt-4 border-t border-[#e5e5e5] dark:border-[#2a2a28] flex items-center gap-2">
            <a href="things:///show?id={{ $item->id }}"
                class="inline-block px-3 py-1 bg-[#1b1b18] dark:bg-[#eeeeec] text-white dark:text-[#1C1C1A] border border-black dark:border-[#eeeeec] rounded-sm text-xs leading-normal transition-all cursor-pointer">
                Open in Things
            </a>
            <form method="POST" action="{{ route('items.trash', $item) }}"
                onsubmit="return confirm('Move this item to trash?');">
                @csrf
                @method('DELETE')
                <button type="submit"
                    class="inline-block px-3 py-1 bg-transparent text-xs text-[#706f6c] dark:text-[#A1A09A] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm hover:bg-[#f5f5f2] dark:hover:bg-[#161615] transition-all cursor-pointer">
                    Move to Trash
                </button>
            </form>
        </div>
    </div>
</x-layouts.app>
