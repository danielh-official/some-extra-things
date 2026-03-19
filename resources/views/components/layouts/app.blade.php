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
    <div class="flex flex-col flex-1 overflow-hidden">
        <main id="main-content" class="overflow-y-auto p-4 w-full flex-1">
            {{ $slot }}
        </main>
    </div>
    @livewireScripts
</body>

</html>
