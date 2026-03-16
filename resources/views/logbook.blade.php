<x-layouts.app>
    <div class="flex flex-col gap-4 w-full max-w-2xl">
        <h1 class="text-sm font-medium mb-2">Logbook</h1>
        @forelse ($grouped as $date => $items)
            <div class="flex flex-col gap-2">
                <h2 class="text-sm font-medium text-[#706f6c] dark:text-[#A1A09A]">
                    {{ \Carbon\Carbon::parse($date)->format('l, F j') }}
                </h2>
                @foreach ($items as $item)
                    <div class="flex items-center gap-2">
                        @if ($item->type === 'Project')
                            @if ($item->status === 'Completed')
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0 text-[#706f6c] dark:text-[#A1A09A]" viewBox="0 0 16 16" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm3.844-8.791a.75.75 0 0 0-1.188-.918l-3.7 4.79-1.646-1.647a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.12-.08l4.224-5.455z" clip-rule="evenodd" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0 text-[#706f6c] dark:text-[#A1A09A]" viewBox="0 0 16 16" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8 15A7 7 0 1 0 8 1a7 7 0 0 0 0 14zm2.78-4.22a.75.75 0 0 1-1.06 0L8 9.06l-1.72 1.72a.75.75 0 0 1-1.06-1.06L6.94 8 5.22 6.28a.75.75 0 0 1 1.06-1.06L8 6.94l1.72-1.72a.75.75 0 1 1 1.06 1.06L9.06 8l1.72 1.72a.75.75 0 0 1 0 1.06z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        @else
                            @if ($item->status === 'Completed')
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0 text-[#706f6c] dark:text-[#A1A09A]" viewBox="0 0 16 16" fill="currentColor">
                                    <path fill-rule="evenodd" d="M2 4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V4zm10.03 1.97a.75.75 0 0 0-1.06-1.06L6.75 9.19 5.03 7.47a.75.75 0 0 0-1.06 1.06l2.25 2.25a.75.75 0 0 0 1.06 0l4.75-4.81z" clip-rule="evenodd" />
                                </svg>
                            @else
                                <svg xmlns="http://www.w3.org/2000/svg" class="size-4 shrink-0 text-[#706f6c] dark:text-[#A1A09A]" viewBox="0 0 16 16" fill="currentColor">
                                    <path fill-rule="evenodd" d="M2 4a2 2 0 0 1 2-2h8a2 2 0 0 1 2 2v8a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V4zm8.28 1.72a.75.75 0 0 0-1.06 0L8 6.94 6.78 5.72a.75.75 0 0 0-1.06 1.06L6.94 8l-1.22 1.22a.75.75 0 1 0 1.06 1.06L8 9.06l1.22 1.22a.75.75 0 1 0 1.06-1.06L9.06 8l1.22-1.22a.75.75 0 0 0 0-1.06z" clip-rule="evenodd" />
                                </svg>
                            @endif
                        @endif
                        <h3 class="text-sm {{ $item->type === 'Project' ? 'font-bold' : 'font-medium' }}">
                            <a href="things:///show?id={{ $item->id }}">{{ $item->title }}</a>
                        </h3>
                    </div>
                @endforeach
            </div>
        @empty
            <p class="text-[#706f6c] dark:text-[#A1A09A]">No items found</p>
        @endforelse
    </div>
</x-layouts.app>
