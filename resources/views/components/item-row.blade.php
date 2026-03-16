@props(['item'])
<div class="flex items-center gap-2 min-w-0">
    <h3 class="text-sm font-medium shrink-0">
        <a href="things:///show?id={{ $item->id }}">{{ $item->title }}</a>
    </h3>
    @if (!empty($item->tags))
        <div class="flex gap-1 overflow-hidden">
            @foreach ($item->tags as $tag)
                <span class="shrink-0 whitespace-nowrap rounded-full bg-[#f0f0ec] dark:bg-[#1e1e1c] px-2 py-0.5 text-xs text-[#706f6c] dark:text-[#A1A09A]">{{ $tag }}</span>
            @endforeach
        </div>
    @endif
</div>
