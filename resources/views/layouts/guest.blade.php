{{--
    Guest Layout
    ============
    This layout is used for authentication pages (login, register, password reset).
    It displays a centered card with the school logo and name.

    Features:
    - School branding (logo and name)
    - Centered card layout
    - Responsive design
    - Clean, professional appearance
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- Page title --}}
        <title>{{ config('app.name', 'Library Management System') }}</title>

        {{-- Fonts - Figtree for clean, readable text --}}
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        {{-- Vite compiles our CSS (Tailwind) and JS files --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        {{--
            Main container
            - Full screen height
            - Centered content vertically and horizontally
            - Gradient background using our primary colors
        --}}
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-gradient-to-br from-primary-600 to-primary-800">

            {{-- School Logo and Name Section --}}
            <div class="text-center mb-6">
                {{-- School Logo (Book icon placeholder) --}}
                <div class="flex justify-center mb-4">
                    <div class="w-20 h-20 bg-white rounded-full flex items-center justify-center shadow-lg">
                        <svg class="w-12 h-12 text-primary-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                  d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                        </svg>
                    </div>
                </div>

                {{-- School Name --}}
                <h1 class="text-2xl font-bold text-white">Bobon B Elementary School</h1>
                <p class="text-primary-200 text-sm mt-1">Library Management System</p>
            </div>

            {{--
                Login/Register Card
                - White background
                - Rounded corners
                - Shadow for depth
            --}}
            <div class="w-full sm:max-w-md px-6 py-6 bg-white shadow-xl overflow-hidden sm:rounded-lg">
                {{ $slot }}
            </div>

            {{-- Footer text --}}
            <p class="text-primary-200 text-xs mt-6">
                &copy; {{ date('Y') }} Bobon B Elementary School. All rights reserved.
            </p>
        </div>
    </body>
</html>
