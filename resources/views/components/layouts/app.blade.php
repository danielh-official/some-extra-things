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
</head>

<body class="flex h-screen overflow-hidden bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-white">
    <aside class="w-52 shrink-0 flex flex-col border-r border-[#e5e5e5] dark:border-[#2a2a28] p-3 gap-1">
        <x-sidebar-link href="{{ route('inbox') }}" :active="request()->routeIs('inbox')">Inbox</x-sidebar-link>
        <x-sidebar-link href="{{ route('today') }}" :active="request()->routeIs('today')">Today</x-sidebar-link>
        <x-sidebar-link href="{{ route('upcoming') }}" :active="request()->routeIs('upcoming')">Upcoming</x-sidebar-link>
        <x-sidebar-link href="{{ route('anytime') }}" :active="request()->routeIs('anytime')">Anytime</x-sidebar-link>
        <x-sidebar-link href="{{ route('someday') }}" :active="request()->routeIs('someday')">Someday</x-sidebar-link>
        <x-sidebar-link href="{{ route('all') }}" :active="request()->routeIs('all')">All</x-sidebar-link>
        <x-sidebar-link href="{{ route('logbook') }}" :active="request()->routeIs('logbook')">Logbook</x-sidebar-link>
        <x-sidebar-link href="{{ route('trash') }}" :active="request()->routeIs('trash')">Trash</x-sidebar-link>

        @if ($sidebarSmartLists->isNotEmpty())
            <hr class="border-[#e5e5e5] dark:border-[#2a2a28] my-1" />
            @foreach ($sidebarSmartLists as $smartList)
                <x-sidebar-link href="{{ route('smart-lists.show', $smartList) }}" :active="request()->routeIs('smart-lists.show') && request()->route('smart_list')?->is($smartList)">
                    {{ $smartList->name }}
                </x-sidebar-link>
            @endforeach
        @endif

        <div class="flex-1"></div>

        <hr class="border-[#e5e5e5] dark:border-[#2a2a28] my-1" />
        <x-sidebar-link href="{{ route('smart-lists.index') }}" :active="request()->routeIs('smart-lists.index')">Smart Lists</x-sidebar-link>
        <x-sidebar-link href="{{ route('settings') }}" :active="request()->routeIs('settings')">Settings</x-sidebar-link>
    </aside>
    <main class="flex-1 overflow-y-auto p-6">
        {{ $slot }}
    </main>
</body>

</html>
