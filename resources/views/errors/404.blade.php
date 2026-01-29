{{--
    404 Not Found Error Page
    ========================
    Displayed when a user tries to access a page that doesn't exist.
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Page Not Found - {{ config('app.name', 'Library Management System') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen flex flex-col items-center justify-center px-4">
        {{-- Error Illustration --}}
        <div class="text-center">
            <div class="mb-8">
                <svg class="w-32 h-32 mx-auto text-primary-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>

            {{-- Error Code --}}
            <h1 class="text-9xl font-bold text-gray-200 dark:text-gray-700">404</h1>

            {{-- Error Title --}}
            <h2 class="mt-4 text-3xl font-bold text-gray-800 dark:text-gray-100">Page Not Found</h2>

            {{-- Error Message --}}
            <p class="mt-4 text-lg text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                Oops! The page you're looking for seems to have wandered off the shelves.
                It might have been moved, deleted, or never existed.
            </p>

            {{-- Action Buttons --}}
            <div class="mt-8 flex flex-col sm:flex-row gap-4 justify-center">
                <a href="{{ route('dashboard') }}"
                   class="inline-flex items-center justify-center px-6 py-3 bg-primary-600 text-white font-semibold rounded-lg hover:bg-primary-700 transition-colors shadow-sm">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                    </svg>
                    Go to Dashboard
                </a>
                <button onclick="history.back()"
                        class="inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 font-semibold rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm border border-gray-300 dark:border-gray-600">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                    </svg>
                    Go Back
                </button>
            </div>
        </div>

        {{-- Footer --}}
        <div class="mt-16 text-center text-sm text-gray-500 dark:text-gray-400">
            <p>Bobon B Elementary School Library Management System</p>
        </div>
    </div>
</body>
</html>
