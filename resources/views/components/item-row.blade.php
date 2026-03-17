@props(['item'])
@php $isProject = $item->type === 'Project'; @endphp
<div class="flex gap-2 {{ $isProject ? 'items-start' : 'items-center' }} min-w-0">
    @if ($isProject)
        <svg xmlns="http://www.w3.org/2000/svg" class="size-5 shrink-0 mt-0.5 text-[#706f6c] dark:text-[#A1A09A]" viewBox="0 0 20 20" fill="none" stroke="currentColor" stroke-width="1.5">
            <circle cx="10" cy="10" r="7.5" />
        </svg>
    @elseif ($item->type === 'To-Do')
        <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0 text-[#706f6c] dark:text-[#A1A09A]" viewBox="0 0 16 16" fill="none" stroke="currentColor" stroke-width="1.5">
            <rect x="2" y="2" width="12" height="12" rx="2" />
        </svg>
    @endif

    <div class="flex flex-col min-w-0">
        <div class="flex items-center gap-2 min-w-0">
            <h3 class="shrink-0 {{ $isProject ? 'text-base font-bold' : 'text-sm font-medium' }}">
                <a href="{{ route('items.show', $item) }}">{{ $item->title }}</a>
            </h3>
            @if (!empty($item->tags))
                <div class="flex gap-1 overflow-hidden">
                    @foreach ($item->tags as $tag)
                        <span class="shrink-0 whitespace-nowrap rounded-full bg-[#f0f0ec] dark:bg-[#1e1e1c] px-2 py-0.5 text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $tag }}</span>
                    @endforeach
                </div>
            @endif
        </div>
        @if ($item->parent)
            <p class="text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $item->parent }}</p>
        @endif
    </div>
</div>
