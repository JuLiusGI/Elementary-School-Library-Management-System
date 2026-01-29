{{--
    503 Service Unavailable Error Page
    ===================================
    Displayed when the application is in maintenance mode.
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Maintenance Mode - {{ config('app.name', 'Library Management System') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    {{-- Auto-refresh every 30 seconds --}}
    <meta http-equiv="refresh" content="30">
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen flex flex-col items-center justify-center px-4">
        {{-- Maintenance Illustration --}}
        <div class="text-center">
            <div class="mb-8">
                <svg class="w-32 h-32 mx-auto text-primary-500 animate-pulse" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                </svg>
            </div>

            {{-- Error Code --}}
            <h1 class="text-7xl font-bold text-gray-200 dark:text-gray-700">503</h1>

            {{-- Error Title --}}
            <h2 class="mt-4 text-3xl font-bold text-gray-800 dark:text-gray-100">Under Maintenance</h2>

            {{-- Error Message --}}
            <p class="mt-4 text-lg text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                We're currently performing scheduled maintenance to improve our services.
                Please check back shortly.
            </p>

            {{-- Loading Indicator --}}
            <div class="mt-8 flex justify-center">
                <div class="flex space-x-2">
                    <div class="w-3 h-3 bg-primary-500 rounded-full animate-bounce" style="animation-delay: 0s"></div>
                    <div class="w-3 h-3 bg-primary-500 rounded-full animate-bounce" style="animation-delay: 0.1s"></div>
                    <div class="w-3 h-3 bg-primary-500 rounded-full animate-bounce" style="animation-delay: 0.2s"></div>
                </div>
            </div>

            {{-- Info Text --}}
            <p class="mt-8 text-sm text-gray-500 dark:text-gray-400">
                This page will automatically refresh. Thank you for your patience.
            </p>
        </div>

        {{-- Footer --}}
        <div class="mt-16 text-center text-sm text-gray-500 dark:text-gray-400">
            <p>Bobon B Elementary School Library Management System</p>
        </div>
    </div>
</body>
</html>
