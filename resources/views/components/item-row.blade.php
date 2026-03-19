@props(['item', 'showParent' => true, 'hideTags' => false])
@php
    $isProject = $item->type === 'Project';
    $isSomeday = $item->start === 'Someday';
    $itemUrl = $isProject ? route('projects.show', $item) : route('todos.show', $item);
    $unloggedCount = $isProject
        ? \App\Models\Item::notTrashed()
            ->where('parent_id', $item->id)
            ->where('type', 'To-Do')
            ->where('status', 'Open')
            ->where('is_logged', false)
            ->count()
        : 0;
@endphp
<div class="flex gap-2 {{ $isProject ? 'items-start' : 'items-center' }} min-w-0 {{ $isSomeday ? 'opacity-50' : '' }}">
    @if ($isProject && $item->status === 'Open')
        <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="size-5 shrink-0 mt-0.5 text-[#706f6c] dark:text-[#A1A09A]"
            viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5" {!! $isSomeday ? 'stroke-dasharray="3 2"' : '' !!}>
            <circle cx="10" cy="10" r="7.5" />
        </svg>
    @elseif ($item->type === 'To-Do' && $item->status === 'Open')
        <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="size-4 shrink-0 text-[#706f6c] dark:text-[#A1A09A]"
            viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5" {!! $isSomeday ? 'stroke-dasharray="3 2"' : '' !!}>
            <rect x="2" y="2" width="12" height="12" rx="2" />
        </svg>
    @elseif ($item->type === 'Project' && $item->status === 'Completed')
        <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="size-4 shrink-0 text-[#706f6c] dark:text-[#A1A09A]"
            viewBox="0 0 16 16" fill="currentColor">
            <path fill-rule="evenodd"
                d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm3.844-8.791a.75.75 0 0 0-1.188-.918l-3.7 4.79-1.646-1.647a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.12-.08l4.224-5.455z"
                clip-rule="evenodd" />
        </svg>
    @elseif ($item->type === 'Project' && $item->status === 'Canceled')
        <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="size-4 shrink-0 text-[#706f6c] dark:text-[#A1A09A]"
            viewBox="0 0 16 16" fill="currentColor">
            <path fill-rule="evenodd"
                d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm2.78-4.22a.75.75 0 0 1-1.06 0L8 9.06l-1.72 1.72a.75.75 0 0 1-1.06-1.06L6.94 8 5.22 6.28a.75.75 0 0 1 1.06-1.06L8 6.94l1.72-1.72a.75.75 0 1 1 1.06 1.06L9.06 8l1.72 1.72a.75.75 0 0 1 0 1.06z"
                clip-rule="evenodd" />
        </svg>
    @elseif ($item->type === 'To-Do' && $item->status === 'Completed')
        <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="size-4 shrink-0 text-[#706f6c] dark:text-[#A1A09A]"
            viewBox="0 0 16 16" fill="currentColor">
            <path fill-rule="evenodd"
                d="M2 4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V4zm10.03 1.97a.75.75 0 0 0-1.06-1.06L6.75 9.19 5.03 7.47a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.06 0l4.75-4.81z"
                clip-rule="evenodd" />
        </svg>
    @elseif ($item->type === 'To-Do' && $item->status === 'Canceled')
        <svg xmlns="http://www.w3.org/2000/svg" aria-hidden="true" class="size-4 shrink-0 text-[#706f6c] dark:text-[#A1A09A]"
            viewBox="0 0 16 16" fill="currentColor">
            <path fill-rule="evenodd"
                d="M2 4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V4zm8.28 1.72a.75.75 0 0 0-1.06 0L8 6.94 6.78 5.72a.75.75 0 0 0-1.06 1.06L6.94 8l-1.22 1.22a.75.75 0 1 0 1.06 1.06L8 9.06l1.22 1.22a.75.75 0 1 0 1.06-1.06L9.06 8l1.22-1.22a.75.75 0 0 0 0-1.06z"
                clip-rule="evenodd" />
        </svg>
    @endif

    <div class="flex flex-col min-w-0">
        <div class="flex items-center gap-2 min-w-0">
            <h3 class="shrink-0 {{ $isProject ? 'text-base font-bold' : 'text-sm font-medium' }}">
                <a href="{{ $itemUrl }}">{{ $item->title }}</a>
            </h3>
            @if ($isProject && $unloggedCount > 0)
                <span
                    class="shrink-0 text-xs text-[#706f6c] dark:text-[#A1A09A] border border-[#d0d0cc] dark:border-[#3E3E3A] px-1.5 py-0.5 rounded-sm">{{ $unloggedCount }}</span>
            @endif
            @if (!empty($item->tags) && !$hideTags)
                <div class="flex gap-1 overflow-hidden">
                    @foreach ($item->tags as $tag)
                        <span
                            class="shrink-0 whitespace-nowrap rounded-full bg-[#f0f0ec] dark:bg-[#1e1e1c] px-2 py-0.5 text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $tag }}</span>
                    @endforeach
                </div>
            @endif
        </div>
        @if ($showParent && $item->parent)
            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $item->parent }}</p>
        @endif
    </div>
</div>
