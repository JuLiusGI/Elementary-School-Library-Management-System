{{--
    500 Server Error Page
    =====================
    Displayed when there's an internal server error.
--}}

<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
      :class="{ 'dark': darkMode }">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Server Error - {{ config('app.name', 'Library Management System') }}</title>
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="font-sans antialiased bg-gray-100 dark:bg-gray-900">
    <div class="min-h-screen flex flex-col items-center justify-center px-4">
        {{-- Error Illustration --}}
        <div class="text-center">
            <div class="mb-8">
                <svg class="w-32 h-32 mx-auto text-warning-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5"
                          d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L2.697 16c-.77 1.333.192 3 1.732 3z"/>
                </svg>
            </div>

            {{-- Error Code --}}
            <h1 class="text-9xl font-bold text-gray-200 dark:text-gray-700">500</h1>

            {{-- Error Title --}}
            <h2 class="mt-4 text-3xl font-bold text-gray-800 dark:text-gray-100">Server Error</h2>

            {{-- Error Message --}}
            <p class="mt-4 text-lg text-gray-600 dark:text-gray-400 max-w-md mx-auto">
                Oops! Something went wrong on our end.
                Our team has been notified and is working on fixing the issue.
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
                <button onclick="location.reload()"
                        class="inline-flex items-center justify-center px-6 py-3 bg-white dark:bg-gray-800 text-gray-700 dark:text-gray-200 font-semibold rounded-lg hover:bg-gray-50 dark:hover:bg-gray-700 transition-colors shadow-sm border border-gray-300 dark:border-gray-600">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
                    </svg>
                    Try Again
                </button>
            </div>

            {{-- Help Text --}}
            <p class="mt-8 text-sm text-gray-500 dark:text-gray-400">
                If the problem persists, please contact the system administrator.
            </p>
        </div>

        {{-- Footer --}}
        <div class="mt-16 text-center text-sm text-gray-500 dark:text-gray-400">
            <p>Bobon B Elementary School Library Management System</p>
        </div>
    </div>
</body>
</html>
