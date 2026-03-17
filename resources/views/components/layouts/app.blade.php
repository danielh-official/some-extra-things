<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=instrument-sans:400,500,600" rel="stylesheet" />

    <!-- Styles / Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-white flex h-screen">
    <aside class="border-r border-[#e5e5e5] dark:border-[#2a2a28] p-3 gap-1 flex flex-col w-64 h-full">
        <div class="flex-1 overflow-y-auto flex flex-col gap-1">
            {{-- Primary nav --}}
            <div style="margin-bottom:16px;">
                <x-sidebar-link href="{{ route('inbox') }}" :active="request()->routeIs('inbox')">Inbox</x-sidebar-link>
            </div>

            <x-sidebar-link href="{{ route('today') }}" :active="request()->routeIs('today')">Today</x-sidebar-link>
            <x-sidebar-link href="{{ route('upcoming') }}" :active="request()->routeIs('upcoming')">Upcoming</x-sidebar-link>
            <x-sidebar-link href="{{ route('anytime') }}" :active="request()->routeIs('anytime')">Anytime</x-sidebar-link>
            <x-sidebar-link href="{{ route('someday') }}" :active="request()->routeIs('someday')">Someday</x-sidebar-link>

            <div style="margin-top:16px;">
                <x-sidebar-link href="{{ route('logbook') }}" :active="request()->routeIs('logbook')">Logbook</x-sidebar-link>
            </div>
            @if (\App\Models\Item::query()->where('is_trashed', true)->count() > 0)
                <x-sidebar-link href="{{ route('trash') }}" :active="request()->routeIs('trash')">Trash</x-sidebar-link>
            @endif

            {{-- Smart Lists --}}
            <div style="margin-top:16px;">
                <x-sidebar-link href="{{ route('smart-lists.index') }}" :active="request()->routeIs('smart-lists.*')">Smart Lists</x-sidebar-link>
            </div>

            {{-- Settings & Tags --}}
            <div style="margin-top:16px;">
                <x-sidebar-link href="{{ route('settings') }}" :active="request()->routeIs('settings')">Settings</x-sidebar-link>
                <x-sidebar-link href="{{ route('tags') }}" :active="request()->routeIs('tags*')">Tags</x-sidebar-link>
            </div>

            {{-- Top-level active projects --}}
            @foreach ($sidebarTopLevelProjects as $project)
                <div style="margin-top:4px;">
                    <x-sidebar-link href="{{ route('projects.show', $project) }}" :active="request()->routeIs('projects.show') && request()->route('item')?->is($project)">{{ $project->title }}</x-sidebar-link>
                </div>
            @endforeach

            {{-- Later Projects --}}
            @if ($sidebarLaterProjectsCount > 0)
                <div style="margin-top:4px;">
                    <x-sidebar-link href="{{ route('later-projects') }}" :active="request()->routeIs('later-projects')">Later Projects</x-sidebar-link>
                </div>
            @endif

            {{-- Areas with their active projects --}}
            @foreach ($sidebarAreas as $area)
                <div style="margin-top:8px;">
                    <x-sidebar-link href="{{ route('areas.show', $area) }}" :active="request()->routeIs('areas.show') && request()->route('area')?->is($area)" class="text-xs font-semibold uppercase tracking-wide text-[#a0a09c] dark:text-[#60605c]">{{ $area->title }}</x-sidebar-link>
                </div>
                @foreach ($area->sidebarProjects as $project)
                    <div class="pl-3">
                        <x-sidebar-link href="{{ route('projects.show', $project) }}" :active="request()->routeIs('projects.show') && request()->route('item')?->is($project)">{{ $project->title }}</x-sidebar-link>
                    </div>
                @endforeach
            @endforeach
        </div>
    </aside>
    <div class="flex flex-col flex-1 overflow-hidden">
        <main class="overflow-y-auto p-4 w-full flex-1">
            {{ $slot }}
        </main>
    </div>
    @livewireScripts
</body>

</html>
