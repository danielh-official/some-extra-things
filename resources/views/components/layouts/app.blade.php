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

<body class="bg-[#FDFDFC] dark:bg-[#0a0a0a] text-[#1b1b18] flex flex-col p-6 gap-4">
    <header>
        <nav class="flex w-full gap-4">
            <a href="{{ route('home') }}" class="text-sm font-medium text-center">Home</a>
            <a href="{{ route('settings') }}" class="text-sm font-medium text-center">Settings</a>
        </nav>
    </header>
    <main class="w-full justify-center items-center flex">
        {{ $slot }}
    </main>
</body>

</html>