@props(['item', 'showParent' => true])
@php
    $isProject = $item->type === 'Project';
    $isSomeday = $item->start === 'Someday';
    $unloggedCount = $isProject
        ? \App\Models\Item::notTrashed()->where('parent_id', $item->id)->where('type', 'To-Do')->where('status', 'Open')->where('is_logged', false)->count()
        : 0;
    $childTodos = $isProject
        ? \App\Models\Item::notTrashed()->where('parent_id', $item->id)->where('type', 'To-Do')->where('status', 'Open')->orderBy('creation_date')->get()->groupBy(fn ($t) => $t->heading ?? '')
        : collect();
@endphp
<div class="flex gap-2 {{ $isProject ? 'items-start' : 'items-center' }} min-w-0">
    @if ($isProject)
        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0 mt-0.5 text-[#706f6c] dark:text-[#A1A09A]" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" {{ $isSomeday ? 'stroke-dasharray="3 2"' : '' }}>
            <circle cx="10" cy="10" r="7.5" />
        </svg>
    @elseif ($item->type === 'To-Do')
        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0 text-[#706f6c] dark:text-[#A1A09A]" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" {{ $isSomeday ? 'stroke-dasharray="3 2"' : '' }}>
            <rect x="2" y="2" width="12" height="12" rx="2" />
        </svg>
    @endif

    <div class="flex flex-col min-w-0 flex-1">
        <div class="flex items-center gap-2 min-w-0">
            <h3 class="shrink-0 {{ $isProject ? 'text-base font-bold' : 'text-sm font-medium' }}">
                <a href="{{ route('items.show', $item) }}">{{ $item->title }}</a>
            </h3>
            @if ($isProject && $unloggedCount > 0)
                <span class="shrink-0 text-xs text-[#706f6c] dark:text-[#A1A09A] border border-[#d0d0cc] dark:border-[#3E3E3A] px-1.5 py-0.5 rounded-sm">{{ $unloggedCount }}</span>
            @endif
            @if (!empty($item->tags))
                <div class="flex gap-1 overflow-hidden">
                    @foreach ($item->tags as $tag)
                        <span class="shrink-0 whitespace-nowrap rounded-full bg-[#f0f0ec] dark:bg-[#1e1e1c] px-2 py-0.5 text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $tag }}</span>
                    @endforeach
                </div>
            @endif
        </div>
        @if ($showParent && $item->parent)
            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $item->parent }}</p>
        @endif

        @if ($isProject && $childTodos->isNotEmpty())
            <div class="flex flex-col gap-1 mt-2 ml-1">
                @foreach ($childTodos as $headingName => $todos)
                    @if ($headingName !== '')
                        @php $firstTodo = $todos->first(); @endphp
                        <p class="text-xs font-semibold text-[#706f6c] dark:text-[#A1A09A] mt-1">
                            @if ($firstTodo->heading_id)
                                <a href="things:///show?id={{ $firstTodo->heading_id }}">{{ $headingName }}</a>
                            @else
                                {{ $headingName }}
                            @endif
                        </p>
                    @endif
                    @foreach ($todos as $todo)
                        <div class="flex items-center gap-2 min-w-0">
                            <svg xmlns="http://www.w3.org/2000/svg" class="size-3.5 shrink-0 text-[#a0a09c] dark:text-[#60605c]" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" {{ $todo->start === 'Someday' ? 'stroke-dasharray="3 2"' : '' }}>
                                <rect x="2" y="2" width="12" height="12" rx="2" />
                            </svg>
                            <span class="text-xs text-[#706f6c] dark:text-[#A1A09A]">
                                <a href="{{ route('items.show', $todo) }}">{{ $todo->title }}</a>
                            </span>
                        </div>
                    @endforeach
                @endforeach
            </div>
        @endif
    </div>
</div>
