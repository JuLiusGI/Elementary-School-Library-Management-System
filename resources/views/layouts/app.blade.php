{{--
    Main Application Layout
    =======================
    This is the main layout template for the Library Management System.
    It includes:
    - Header with school name and user menu
    - Sidebar navigation for main menu items
    - Responsive design for mobile devices
    - Flash message display area

    Usage in child views:
    <x-app-layout>
        <x-slot name="header">Page Title Here</x-slot>
        <!-- Page content goes here -->
    </x-app-layout>
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        {{-- Page title - shows "Page Name - App Name" format --}}
        <title>@yield('title', 'Dashboard') - {{ config('app.name', 'Library Management System') }}</title>

        {{-- Fonts - Figtree for clean, readable text --}}
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        {{-- Vite compiles our CSS (Tailwind) and JS files --}}
        @vite(['resources/css/app.css', 'resources/js/app.js'])

        {{-- Livewire styles for dynamic components --}}
        @livewireStyles
    </head>
    <body class="font-sans antialiased">
        {{-- Main container - full height with gray background --}}
        <div class="min-h-screen bg-gray-100">

            {{-- Mobile sidebar overlay - shown when mobile menu is open --}}
            <div x-data="{ sidebarOpen: false }" class="flex">

                {{--
                    MOBILE SIDEBAR OVERLAY
                    This dark overlay appears behind the sidebar on mobile
                    Clicking it closes the sidebar
                --}}
                <div x-show="sidebarOpen"
                     x-transition:enter="transition-opacity ease-linear duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition-opacity ease-linear duration-300"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden"
                     @click="sidebarOpen = false">
                </div>

                {{--
                    SIDEBAR NAVIGATION
                    Contains the main navigation menu
                    - Fixed position on desktop (lg:static)
                    - Slides in on mobile
                --}}
                <aside x-show="sidebarOpen || window.innerWidth >= 1024"
                       x-transition:enter="transition ease-in-out duration-300 transform"
                       x-transition:enter-start="-translate-x-full"
                       x-transition:enter-end="translate-x-0"
                       x-transition:leave="transition ease-in-out duration-300 transform"
                       x-transition:leave-start="translate-x-0"
                       x-transition:leave-end="-translate-x-full"
                       class="fixed inset-y-0 left-0 z-30 w-64 bg-primary-800 lg:static lg:inset-0 lg:translate-x-0"
                       @resize.window="if (window.innerWidth >= 1024) sidebarOpen = true">

                    {{-- Sidebar Header - School Name and Logo --}}
                    <div class="flex items-center justify-center h-20 bg-primary-900">
                        <div class="flex items-center">
                            {{-- School Logo placeholder - replace with actual logo --}}
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <div class="ml-3">
                                <span class="text-white font-semibold text-lg">Bobon B</span>
                                <span class="text-primary-200 text-sm block">Elementary School</span>
                            </div>
                        </div>
                    </div>

                    {{-- Navigation Menu --}}
                    <nav class="mt-6">
                        {{-- Dashboard Link --}}
                        <a href="{{ route('dashboard') }}"
                           class="flex items-center px-6 py-3 text-primary-100 hover:bg-primary-700 hover:text-white transition-colors
                                  {{ request()->routeIs('dashboard') ? 'bg-primary-700 text-white border-l-4 border-white' : '' }}">
                            {{-- Dashboard Icon --}}
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span class="ml-3">Dashboard</span>
                        </a>

                        {{-- Section Divider --}}
                        <div class="px-6 py-2 mt-4">
                            <p class="text-xs font-semibold text-primary-400 uppercase tracking-wider">Library</p>
                        </div>

                        {{-- Students Link --}}
                        <a href="{{ route('students.index') ?? '#' }}"
                           class="flex items-center px-6 py-3 text-primary-100 hover:bg-primary-700 hover:text-white transition-colors
                                  {{ request()->routeIs('students.*') ? 'bg-primary-700 text-white border-l-4 border-white' : '' }}">
                            {{-- Students Icon --}}
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span class="ml-3">Students</span>
                        </a>

                        {{-- Books Link --}}
                        <a href="{{ route('books.index') ?? '#' }}"
                           class="flex items-center px-6 py-3 text-primary-100 hover:bg-primary-700 hover:text-white transition-colors
                                  {{ request()->routeIs('books.*') ? 'bg-primary-700 text-white border-l-4 border-white' : '' }}">
                            {{-- Books Icon --}}
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span class="ml-3">Books</span>
                        </a>

                        {{-- Categories Link --}}
                        <a href="{{ route('categories.index') ?? '#' }}"
                           class="flex items-center px-6 py-3 text-primary-100 hover:bg-primary-700 hover:text-white transition-colors
                                  {{ request()->routeIs('categories.*') ? 'bg-primary-700 text-white border-l-4 border-white' : '' }}">
                            {{-- Categories Icon --}}
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            <span class="ml-3">Categories</span>
                        </a>

                        {{-- Section Divider --}}
                        <div class="px-6 py-2 mt-4">
                            <p class="text-xs font-semibold text-primary-400 uppercase tracking-wider">Transactions</p>
                        </div>

                        {{-- Borrow Book Link --}}
                        <a href="{{ route('transactions.borrow') ?? '#' }}"
                           class="flex items-center px-6 py-3 text-primary-100 hover:bg-primary-700 hover:text-white transition-colors
                                  {{ request()->routeIs('transactions.borrow') ? 'bg-primary-700 text-white border-l-4 border-white' : '' }}">
                            {{-- Borrow Icon --}}
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="ml-3">Borrow Book</span>
                        </a>

                        {{-- Return Book Link --}}
                        <a href="{{ route('transactions.return') ?? '#' }}"
                           class="flex items-center px-6 py-3 text-primary-100 hover:bg-primary-700 hover:text-white transition-colors
                                  {{ request()->routeIs('transactions.return') ? 'bg-primary-700 text-white border-l-4 border-white' : '' }}">
                            {{-- Return Icon --}}
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="ml-3">Return Book</span>
                        </a>

                        {{-- Transaction History Link --}}
                        <a href="{{ route('transactions.index') ?? '#' }}"
                           class="flex items-center px-6 py-3 text-primary-100 hover:bg-primary-700 hover:text-white transition-colors
                                  {{ request()->routeIs('transactions.index') || request()->routeIs('transactions.history') ? 'bg-primary-700 text-white border-l-4 border-white' : '' }}">
                            {{-- History Icon --}}
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="ml-3">History</span>
                        </a>

                        {{-- Fines Management Link --}}
                        <a href="{{ route('transactions.fines') ?? '#' }}"
                           class="flex items-center px-6 py-3 text-primary-100 hover:bg-primary-700 hover:text-white transition-colors
                                  {{ request()->routeIs('transactions.fines') ? 'bg-primary-700 text-white border-l-4 border-white' : '' }}">
                            {{-- Fines Icon --}}
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="ml-3">Fines</span>
                        </a>

                        {{-- Section Divider --}}
                        <div class="px-6 py-2 mt-4">
                            <p class="text-xs font-semibold text-primary-400 uppercase tracking-wider">Reports</p>
                        </div>

                        {{-- Reports Link --}}
                        <a href="{{ route('reports.index') ?? '#' }}"
                           class="flex items-center px-6 py-3 text-primary-100 hover:bg-primary-700 hover:text-white transition-colors
                                  {{ request()->routeIs('reports.*') ? 'bg-primary-700 text-white border-l-4 border-white' : '' }}">
                            {{-- Reports Icon --}}
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="ml-3">Reports</span>
                        </a>

                        {{-- Section Divider (Admin Only) --}}
                        @if(auth()->user() && auth()->user()->role === 'admin')
                        <div class="px-6 py-2 mt-4">
                            <p class="text-xs font-semibold text-primary-400 uppercase tracking-wider">Administration</p>
                        </div>

                        {{-- Settings Link --}}
                        <a href="{{ route('settings.index') ?? '#' }}"
                           class="flex items-center px-6 py-3 text-primary-100 hover:bg-primary-700 hover:text-white transition-colors
                                  {{ request()->routeIs('settings.*') ? 'bg-primary-700 text-white border-l-4 border-white' : '' }}">
                            {{-- Settings Icon --}}
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="ml-3">Settings</span>
                        </a>

                        {{-- Users Link --}}
                        <a href="{{ route('users.index') ?? '#' }}"
                           class="flex items-center px-6 py-3 text-primary-100 hover:bg-primary-700 hover:text-white transition-colors
                                  {{ request()->routeIs('users.*') ? 'bg-primary-700 text-white border-l-4 border-white' : '' }}">
                            {{-- Users Icon --}}
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="ml-3">Users</span>
                        </a>
                        @endif
                    </nav>
                </aside>

                {{-- MAIN CONTENT AREA --}}
                <div class="flex-1 flex flex-col min-h-screen lg:ml-0">

                    {{--
                        TOP HEADER BAR
                        Contains mobile menu button and user dropdown
                    --}}
                    <header class="bg-white shadow-sm border-b border-gray-200">
                        <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">

                            {{-- Mobile menu button - only shown on mobile --}}
                            <button @click="sidebarOpen = !sidebarOpen"
                                    class="lg:hidden p-2 rounded-md text-gray-600 hover:text-gray-900 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>

                            {{-- Page Title (from child view) --}}
                            <div class="flex-1 lg:flex-none">
                                @isset($header)
                                    <h1 class="text-xl font-semibold text-gray-800">{{ $header }}</h1>
                                @endisset
                            </div>

                            {{-- User Dropdown Menu --}}
                            <div class="flex items-center" x-data="{ userMenuOpen: false }">
                                {{-- Current Date Display --}}
                                <span class="hidden sm:block text-sm text-gray-500 mr-4">
                                    {{ now()->format('l, F j, Y') }}
                                </span>

                                {{-- User Menu Button --}}
                                <div class="relative">
                                    <button @click="userMenuOpen = !userMenuOpen"
                                            class="flex items-center space-x-3 text-gray-700 hover:text-gray-900 focus:outline-none">
                                        {{-- User Avatar --}}
                                        <div class="w-8 h-8 rounded-full bg-primary-500 flex items-center justify-center text-white font-semibold">
                                            {{ substr(Auth::user()->name ?? 'U', 0, 1) }}
                                        </div>
                                        <span class="hidden sm:block text-sm font-medium">{{ Auth::user()->name ?? 'User' }}</span>
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/>
                                        </svg>
                                    </button>

                                    {{-- Dropdown Menu --}}
                                    <div x-show="userMenuOpen"
                                         x-transition:enter="transition ease-out duration-100"
                                         x-transition:enter-start="transform opacity-0 scale-95"
                                         x-transition:enter-end="transform opacity-100 scale-100"
                                         x-transition:leave="transition ease-in duration-75"
                                         x-transition:leave-start="transform opacity-100 scale-100"
                                         x-transition:leave-end="transform opacity-0 scale-95"
                                         @click.away="userMenuOpen = false"
                                         class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg py-1 z-50 border border-gray-200">

                                        {{-- User Info --}}
                                        <div class="px-4 py-2 border-b border-gray-100">
                                            <p class="text-sm font-medium text-gray-900">{{ Auth::user()->name ?? 'User' }}</p>
                                            <p class="text-xs text-gray-500">{{ Auth::user()->email ?? '' }}</p>
                                            <p class="text-xs text-primary-600 capitalize">{{ Auth::user()->role ?? 'User' }}</p>
                                        </div>

                                        {{-- Profile Link --}}
                                        <a href="{{ route('profile.edit') }}"
                                           class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100">
                                            Profile Settings
                                        </a>

                                        {{-- Logout --}}
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                    class="block w-full text-left px-4 py-2 text-sm text-danger-600 hover:bg-gray-100">
                                                Sign Out
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </header>

                    {{--
                        FLASH MESSAGES
                        Display success, error, warning messages from session
                    --}}
                    @if(session('success'))
                        <div class="mx-4 mt-4 sm:mx-6 lg:mx-8">
                            <div class="bg-success-100 border border-success-400 text-success-700 px-4 py-3 rounded relative" role="alert">
                                <span class="block sm:inline">{{ session('success') }}</span>
                            </div>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="mx-4 mt-4 sm:mx-6 lg:mx-8">
                            <div class="bg-danger-100 border border-danger-400 text-danger-700 px-4 py-3 rounded relative" role="alert">
                                <span class="block sm:inline">{{ session('error') }}</span>
                            </div>
                        </div>
                    @endif

                    @if(session('warning'))
                        <div class="mx-4 mt-4 sm:mx-6 lg:mx-8">
                            <div class="bg-warning-100 border border-warning-400 text-warning-700 px-4 py-3 rounded relative" role="alert">
                                <span class="block sm:inline">{{ session('warning') }}</span>
                            </div>
                        </div>
                    @endif

                    {{--
                        MAIN CONTENT
                        This is where the page content from child views is rendered
                        Supports both:
                        - Blade components: {{ $slot }}
                        - Traditional views: @yield('content')
                    --}}
                    <main class="flex-1">
                        @hasSection('content')
                            @yield('content')
                        @else
                            {{ $slot ?? '' }}
                        @endif
                    </main>

                    {{--
                        FOOTER
                        Simple footer with copyright and system info
                    --}}
                    <footer class="bg-white border-t border-gray-200 py-4 px-4 sm:px-6 lg:px-8">
                        <div class="flex flex-col sm:flex-row justify-between items-center text-sm text-gray-500">
                            <p>&copy; {{ date('Y') }} Bobon B Elementary School Library. All rights reserved.</p>
                            <p class="mt-2 sm:mt-0">Library Management System v1.0</p>
                        </div>
                    </footer>
                </div>
            </div>
        </div>

        {{-- Livewire scripts for dynamic components --}}
        @livewireScripts
    </body>
</html>
