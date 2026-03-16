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

<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] dark:text-white flex h-screen">
    <aside class="border-r border-[#e5e5e5] dark:border-[#2a2a28] p-3 gap-1 overflow-y-auto w-64 h-full">
        <div>
            <div class="h-full px-3 py-4 bg-neutral-primary-soft">
                <ul class="space-y-2 font-medium">
                    <li style="margin-bottom:20px;">
                        <x-sidebar-link href="{{ route('inbox') }}" :active="request()->routeIs('inbox')">Inbox</x-sidebar-link>
                    </li>

                    <x-sidebar-link href="{{ route('today') }}" :active="request()->routeIs('today')">Today</x-sidebar-link>
                    <x-sidebar-link href="{{ route('upcoming') }}" :active="request()->routeIs('upcoming')">Upcoming</x-sidebar-link>
                    <x-sidebar-link href="{{ route('anytime') }}" :active="request()->routeIs('anytime')">Anytime</x-sidebar-link>
                    <x-sidebar-link href="{{ route('someday') }}" :active="request()->routeIs('someday')">Someday</x-sidebar-link>
                    <x-sidebar-link href="{{ route('all') }}" :active="request()->routeIs('all')">All</x-sidebar-link>

                    <li style="margin-top:20px;">
                        <x-sidebar-link href="{{ route('logbook') }}" :active="request()->routeIs('logbook')">Logbook</x-sidebar-link>
                    </li>
                    <li>
                        <x-sidebar-link href="{{ route('trash') }}" :active="request()->routeIs('trash')">Trash</x-sidebar-link>
                    </li>
                </ul>
                <ul style="margin-top:20px;">
                    <li>
                        <x-sidebar-link href="{{ route('smart-lists.index') }}" :active="request()->routeIs('smart-lists.index')">Smart
                            Lists</x-sidebar-link>
                    </li>
                    <li>
                        <x-sidebar-link href="{{ route('settings') }}" :active="request()->routeIs('settings')">Settings</x-sidebar-link>
                    </li>
                </ul>
            </div>
        </div>
    </aside>
    <main class="overflow-y-auto p-4 w-full"">
        {{ $slot }}
    </main>
</body>

</html>
