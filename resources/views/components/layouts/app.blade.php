<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
    @if ($theme === 'dark') class="dark" @endif>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link rel="icon" href="{{ asset('icon.png') }}" type="image/x-icon"/>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    @if ($theme === 'system')
        <!-- Theme: apply dark class when OS prefers dark -->
        <script>
            if (window.matchMedia('(prefers-color-scheme: dark)').matches) {
                document.documentElement.classList.add('dark');
            }
        </script>
    @endif

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-white flex h-screen">
    <a href="#main-content"
        class="sr-only focus:not-sr-only focus:absolute focus:z-50 focus:top-2 focus:left-2 focus:px-3 focus:py-1 focus:bg-[#1b1b18] focus:text-white focus:rounded focus:text-sm">
        Skip to content
    </a>
    <aside aria-label="Main navigation" class="border-r border-[#e5e5e5] dark:border-[#2a2a28] p-3 gap-1 flex flex-col w-64 h-full">
        <nav class="flex-1 overflow-y-auto flex flex-col gap-1">
            <div style="margin-bottom:16px;">
                <x-sidebar-link href="{{ route('all.index') }}" :active="request()->routeIs('all')">All</x-sidebar-link>
            </div>

            {{-- Smart Lists --}}
            <div>
                <x-sidebar-link href="{{ route('smart-lists.index') }}" :active="request()->routeIs('smart-lists.*')">Smart Lists</x-sidebar-link>
            </div>
            @foreach ($sidebarPinnedSmartLists as $pinnedList)
                <div style="padding-left:8px;{{ $loop->first ? 'margin-top:4px;' : '' }}{{ $loop->last ? 'margin-bottom:16px;' : '' }}">
                    <x-sidebar-link href="{{ route('smart-lists.show', $pinnedList) }}" :active="request()->routeIs('smart-lists.show') && request()->route('smart_list')?->is($pinnedList)">{{ $pinnedList->name }}</x-sidebar-link>
                </div>
            @endforeach
            @if ($sidebarPinnedSmartLists->isEmpty())
                <div style="margin-bottom:16px;"></div>
            @endif

            <x-sidebar-link href="{{ route('logbook') }}" :active="request()->routeIs('logbook')">Logbook</x-sidebar-link>
            @if (\App\Models\Item::query()->where('is_trashed', true)->count() > 0)
                <x-sidebar-link href="{{ route('trash.index') }}" :active="request()->routeIs('trash')">Trash</x-sidebar-link>
            @endif
        </nav>

        <hr class="border-gray-300 dark:border-gray-600" />

        {{-- Settings & Tags --}}
        <div style="margin-top:16px;">
            <x-sidebar-link href="{{ route('tags.index') }}" :active="request()->routeIs('tags*')">Tags</x-sidebar-link>
            <x-sidebar-link href="{{ route('settings.index') }}" :active="request()->routeIs('settings')">Settings</x-sidebar-link>
        </div>
    </aside>
    <div class="flex flex-col flex-1 overflow-hidden"
        x-data="{
            importing: false,
            confirming: false,
        }">

        {{-- Confirm modal --}}
        <div x-show="confirming" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/20 dark:bg-black/40">
            <div class="flex flex-col gap-4 rounded-lg bg-white dark:bg-[#1c1c1a] px-6 py-5 shadow-lg w-80">
                <p class="text-sm text-[#1b1b18] dark:text-[#EDEDEC]">Import active items from Things 3? This will update any existing items.</p>
                <div class="flex justify-end gap-2">
                    <button type="button" @click="confirming = false"
                        class="px-3 py-1 text-xs text-[#706f6c] dark:text-[#A1A09A] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm hover:bg-[#f5f5f2] dark:hover:bg-[#161615] transition-all cursor-pointer">
                        Cancel
                    </button>
                    <button type="button" @click="confirming = false; importing = true; $refs.importForm.submit()"
                        class="px-3 py-1 text-xs text-white bg-[#1b1b18] dark:bg-[#EDEDEC] dark:text-[#1b1b18] rounded-sm hover:opacity-80 transition-all cursor-pointer">
                        Import
                    </button>
                </div>
            </div>
        </div>

        {{-- Loading overlay --}}
        <div x-show="importing" x-cloak
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/20 dark:bg-black/40">
            <div class="flex items-center gap-3 rounded-lg bg-white dark:bg-[#1c1c1a] px-5 py-3 shadow-lg">
                <svg class="size-4 animate-spin text-[#706f6c] dark:text-[#A1A09A]" xmlns="http://www.w3.org/2000/svg"
                    fill="none" viewBox="0 0 24 24">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" />
                    <path class="opacity-75" fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z" />
                </svg>
                <span class="text-xs text-[#1b1b18] dark:text-[#EDEDEC]">Importing from Things 3… Please wait.</span>
            </div>
        </div>

        {{-- Top bar --}}
        <header class="border-b border-[#e5e5e5] dark:border-[#2a2a28] px-4 py-2 flex items-center justify-end gap-3 shrink-0">
            @if (session('import_status'))
                <p class="text-xs text-green-600 dark:text-green-400">{{ session('import_status') }}</p>
            @endif
            @if (session('import_error'))
                <p class="text-xs text-red-600 dark:text-red-400">{{ session('import_error') }}</p>
            @endif

            <form x-ref="importForm" method="POST" action="{{ route('items.import') }}">
                @csrf
                <button type="button" @click="confirming = true"
                    class="px-3 py-1 text-xs text-[#706f6c] dark:text-[#A1A09A] border border-[#e3e3e0] dark:border-[#3E3E3A] rounded-sm hover:bg-[#f5f5f2] dark:hover:bg-[#161615] transition-all cursor-pointer">
                    Import from Things
                </button>
            </form>
        </header>

        <main id="main-content" class="overflow-y-auto p-4 w-full flex-1">
            {{ $slot }}
        </main>
    </div>
    @livewireScripts
</body>

</html>
