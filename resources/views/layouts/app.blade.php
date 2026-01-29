{{--
    Main Application Layout
    =======================
    This is the main layout template for the Library Management System.
    It includes:
    - Dark mode support with toggle
    - Sidebar navigation with icons and organized menu groups
    - Top navbar with user menu and dark mode toggle
    - Flash message display with animations
    - Responsive mobile menu
    - Print-friendly styles

    Usage in child views:
    <x-app-layout>
        <x-slot name="header">Page Title Here</x-slot>
        <!-- Page content goes here -->
    </x-app-layout>
--}}
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}"
      x-data="{ darkMode: localStorage.getItem('darkMode') === 'true' }"
      x-init="$watch('darkMode', val => localStorage.setItem('darkMode', val))"
      :class="{ 'dark': darkMode }">
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

        {{-- Print-friendly styles --}}
        <style>
            @media print {
                .no-print, .sidebar, header, footer, nav { display: none !important; }
                .print-only { display: block !important; }
                body { background: white !important; }
                main { margin: 0 !important; padding: 0 !important; }
            }
            .print-only { display: none; }

            /* Hide x-cloak elements until Alpine initializes */
            [x-cloak] { display: none !important; }
        </style>

        {{-- Page-specific head content --}}
        @stack('head')
    </head>
    <body class="font-sans antialiased bg-gray-100 dark:bg-gray-900 transition-colors duration-200">
        {{-- Main container - full height with gray background --}}
        <div class="min-h-screen">

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
                     class="fixed inset-0 z-20 bg-black bg-opacity-50 lg:hidden no-print"
                     @click="sidebarOpen = false"
                     x-cloak>
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
                       class="fixed inset-y-0 left-0 z-30 w-64 bg-primary-800 dark:bg-gray-800 lg:static lg:inset-0 lg:translate-x-0 no-print sidebar"
                       @resize.window="if (window.innerWidth >= 1024) sidebarOpen = true"
                       x-cloak>

                    {{-- Sidebar Header - School Name and Logo --}}
                    <div class="flex items-center justify-center h-20 bg-primary-900 dark:bg-gray-900">
                        <div class="flex items-center">
                            {{-- School Logo placeholder - replace with actual logo --}}
                            <svg class="w-10 h-10 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <div class="ml-3">
                                <span class="text-white font-semibold text-lg">Bobon B</span>
                                <span class="text-primary-200 dark:text-gray-400 text-sm block">Elementary School</span>
                            </div>
                        </div>
                    </div>

                    {{-- Navigation Menu with Scroll --}}
                    <nav class="mt-4 pb-4 overflow-y-auto" style="max-height: calc(100vh - 5rem);">

                        {{-- Dashboard Link --}}
                        <a href="{{ route('dashboard') }}"
                           class="flex items-center px-6 py-3 text-primary-100 dark:text-gray-300 hover:bg-primary-700 dark:hover:bg-gray-700 hover:text-white transition-colors
                                  {{ request()->routeIs('dashboard') ? 'bg-primary-700 dark:bg-gray-700 text-white border-l-4 border-white' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/>
                            </svg>
                            <span class="ml-3">Dashboard</span>
                        </a>

                        {{-- Section: Library --}}
                        <div class="px-6 py-2 mt-4">
                            <p class="text-xs font-semibold text-primary-400 dark:text-gray-500 uppercase tracking-wider">Library</p>
                        </div>

                        {{-- Students Link --}}
                        <a href="{{ route('students.index') }}"
                           class="flex items-center px-6 py-3 text-primary-100 dark:text-gray-300 hover:bg-primary-700 dark:hover:bg-gray-700 hover:text-white transition-colors
                                  {{ request()->routeIs('students.*') ? 'bg-primary-700 dark:bg-gray-700 text-white border-l-4 border-white' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                            </svg>
                            <span class="ml-3">Students</span>
                        </a>

                        {{-- Books Link --}}
                        <a href="{{ route('books.index') }}"
                           class="flex items-center px-6 py-3 text-primary-100 dark:text-gray-300 hover:bg-primary-700 dark:hover:bg-gray-700 hover:text-white transition-colors
                                  {{ request()->routeIs('books.*') ? 'bg-primary-700 dark:bg-gray-700 text-white border-l-4 border-white' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                            </svg>
                            <span class="ml-3">Books</span>
                        </a>

                        {{-- Categories Link --}}
                        <a href="{{ route('categories.index') }}"
                           class="flex items-center px-6 py-3 text-primary-100 dark:text-gray-300 hover:bg-primary-700 dark:hover:bg-gray-700 hover:text-white transition-colors
                                  {{ request()->routeIs('categories.*') ? 'bg-primary-700 dark:bg-gray-700 text-white border-l-4 border-white' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"/>
                            </svg>
                            <span class="ml-3">Categories</span>
                        </a>

                        {{-- Section: Transactions --}}
                        <div class="px-6 py-2 mt-4">
                            <p class="text-xs font-semibold text-primary-400 dark:text-gray-500 uppercase tracking-wider">Transactions</p>
                        </div>

                        {{-- Borrow Book Link --}}
                        <a href="{{ route('transactions.borrow') }}"
                           class="flex items-center px-6 py-3 text-primary-100 dark:text-gray-300 hover:bg-primary-700 dark:hover:bg-gray-700 hover:text-white transition-colors
                                  {{ request()->routeIs('transactions.borrow*') ? 'bg-primary-700 dark:bg-gray-700 text-white border-l-4 border-white' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 9v3m0 0v3m0-3h3m-3 0H9m12 0a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="ml-3">Borrow Book</span>
                        </a>

                        {{-- Return Book Link --}}
                        <a href="{{ route('transactions.return') }}"
                           class="flex items-center px-6 py-3 text-primary-100 dark:text-gray-300 hover:bg-primary-700 dark:hover:bg-gray-700 hover:text-white transition-colors
                                  {{ request()->routeIs('transactions.return*') ? 'bg-primary-700 dark:bg-gray-700 text-white border-l-4 border-white' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="ml-3">Return Book</span>
                        </a>

                        {{-- Transaction History Link --}}
                        <a href="{{ route('transactions.history') }}"
                           class="flex items-center px-6 py-3 text-primary-100 dark:text-gray-300 hover:bg-primary-700 dark:hover:bg-gray-700 hover:text-white transition-colors
                                  {{ request()->routeIs('transactions.index') || request()->routeIs('transactions.history') ? 'bg-primary-700 dark:bg-gray-700 text-white border-l-4 border-white' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="ml-3">History</span>
                        </a>

                        {{-- Fines Management Link --}}
                        <a href="{{ route('transactions.fines') }}"
                           class="flex items-center px-6 py-3 text-primary-100 dark:text-gray-300 hover:bg-primary-700 dark:hover:bg-gray-700 hover:text-white transition-colors
                                  {{ request()->routeIs('transactions.fines') ? 'bg-primary-700 dark:bg-gray-700 text-white border-l-4 border-white' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                            <span class="ml-3">Fines</span>
                        </a>

                        {{-- Section: Reports --}}
                        <div class="px-6 py-2 mt-4">
                            <p class="text-xs font-semibold text-primary-400 dark:text-gray-500 uppercase tracking-wider">Reports</p>
                        </div>

                        {{-- Reports Link --}}
                        <a href="{{ route('reports.index') }}"
                           class="flex items-center px-6 py-3 text-primary-100 dark:text-gray-300 hover:bg-primary-700 dark:hover:bg-gray-700 hover:text-white transition-colors
                                  {{ request()->routeIs('reports.*') ? 'bg-primary-700 dark:bg-gray-700 text-white border-l-4 border-white' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                            </svg>
                            <span class="ml-3">Reports</span>
                        </a>

                        {{-- Section: Administration (Admin Only) --}}
                        @if(auth()->user() && auth()->user()->role === 'admin')
                        <div class="px-6 py-2 mt-4">
                            <p class="text-xs font-semibold text-primary-400 dark:text-gray-500 uppercase tracking-wider">Administration</p>
                        </div>

                        {{-- Settings Link --}}
                        <a href="{{ route('settings.index') }}"
                           class="flex items-center px-6 py-3 text-primary-100 dark:text-gray-300 hover:bg-primary-700 dark:hover:bg-gray-700 hover:text-white transition-colors
                                  {{ request()->routeIs('settings.*') ? 'bg-primary-700 dark:bg-gray-700 text-white border-l-4 border-white' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"/>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/>
                            </svg>
                            <span class="ml-3">Settings</span>
                        </a>

                        {{-- Users Link --}}
                        <a href="{{ route('users.index') }}"
                           class="flex items-center px-6 py-3 text-primary-100 dark:text-gray-300 hover:bg-primary-700 dark:hover:bg-gray-700 hover:text-white transition-colors
                                  {{ request()->routeIs('users.*') ? 'bg-primary-700 dark:bg-gray-700 text-white border-l-4 border-white' : '' }}">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                      d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="ml-3">Users</span>
                        </a>
                        @endif

                        {{-- Logout Link --}}
                        <div class="px-6 py-2 mt-4 border-t border-primary-700 dark:border-gray-700">
                            <form method="POST" action="{{ route('logout') }}">
                                @csrf
                                <button type="submit"
                                        class="flex items-center w-full px-0 py-3 text-primary-100 dark:text-gray-300 hover:text-white transition-colors">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                                    </svg>
                                    <span class="ml-3">Logout</span>
                                </button>
                            </form>
                        </div>
                    </nav>
                </aside>

                {{-- MAIN CONTENT AREA --}}
                <div class="flex-1 flex flex-col min-h-screen lg:ml-0">

                    {{--
                        TOP HEADER BAR
                        Contains mobile menu button, dark mode toggle, and user dropdown
                    --}}
                    <header class="bg-white dark:bg-gray-800 shadow-sm border-b border-gray-200 dark:border-gray-700 no-print">
                        <div class="flex items-center justify-between h-16 px-4 sm:px-6 lg:px-8">

                            {{-- Mobile menu button - only shown on mobile --}}
                            <button @click="sidebarOpen = !sidebarOpen"
                                    class="lg:hidden p-2 rounded-md text-gray-600 dark:text-gray-300 hover:text-gray-900 dark:hover:text-white hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/>
                                </svg>
                            </button>

                            {{-- Page Title (from child view) --}}
                            <div class="flex-1 lg:flex-none">
                                @isset($header)
                                    <h1 class="text-xl font-semibold text-gray-800 dark:text-gray-100">{{ $header }}</h1>
                                @endisset
                            </div>

                            {{-- Right side: Date, Dark Mode Toggle, User Menu --}}
                            <div class="flex items-center space-x-4">
                                {{-- Current Date Display --}}
                                <span class="hidden sm:block text-sm text-gray-500 dark:text-gray-400">
                                    {{ now()->format('l, F j, Y') }}
                                </span>

                                {{-- Dark Mode Toggle --}}
                                <button @click="darkMode = !darkMode"
                                        class="p-2 rounded-lg text-gray-500 dark:text-gray-400 hover:bg-gray-100 dark:hover:bg-gray-700 focus:outline-none focus:ring-2 focus:ring-primary-500 transition-colors"
                                        :title="darkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'">
                                    {{-- Sun icon (shown in dark mode) --}}
                                    <svg x-show="darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"/>
                                    </svg>
                                    {{-- Moon icon (shown in light mode) --}}
                                    <svg x-show="!darkMode" class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24" x-cloak>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                                              d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"/>
                                    </svg>
                                </button>

                                {{-- User Menu --}}
                                <div class="relative" x-data="{ userMenuOpen: false }">
                                    <button @click="userMenuOpen = !userMenuOpen"
                                            class="flex items-center space-x-3 text-gray-700 dark:text-gray-200 hover:text-gray-900 dark:hover:text-white focus:outline-none">
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
                                         class="absolute right-0 mt-2 w-48 bg-white dark:bg-gray-800 rounded-md shadow-lg py-1 z-50 border border-gray-200 dark:border-gray-700"
                                         x-cloak>

                                        {{-- User Info --}}
                                        <div class="px-4 py-2 border-b border-gray-100 dark:border-gray-700">
                                            <p class="text-sm font-medium text-gray-900 dark:text-gray-100">{{ Auth::user()->name ?? 'User' }}</p>
                                            <p class="text-xs text-gray-500 dark:text-gray-400">{{ Auth::user()->email ?? '' }}</p>
                                            <p class="text-xs text-primary-600 dark:text-primary-400 capitalize">{{ Auth::user()->role ?? 'User' }}</p>
                                        </div>

                                        {{-- Profile Link --}}
                                        <a href="{{ route('profile.edit') }}"
                                           class="block px-4 py-2 text-sm text-gray-700 dark:text-gray-200 hover:bg-gray-100 dark:hover:bg-gray-700">
                                            Profile Settings
                                        </a>

                                        {{-- Logout --}}
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit"
                                                    class="block w-full text-left px-4 py-2 text-sm text-danger-600 dark:text-danger-400 hover:bg-gray-100 dark:hover:bg-gray-700">
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
                        Using improved alert component style
                    --}}
                    <div class="mx-4 mt-4 sm:mx-6 lg:mx-8 space-y-3 no-print">
                        @if(session('success'))
                            <x-alert type="success" :message="session('success')" dismissible />
                        @endif

                        @if(session('error'))
                            <x-alert type="error" :message="session('error')" dismissible />
                        @endif

                        @if(session('warning'))
                            <x-alert type="warning" :message="session('warning')" dismissible />
                        @endif

                        @if(session('info'))
                            <x-alert type="info" :message="session('info')" dismissible />
                        @endif
                    </div>

                    {{--
                        MAIN CONTENT
                        This is where the page content from child views is rendered
                        Supports both:
                        - Blade components: {{ $slot }}
                        - Traditional views: @yield('content')
                    --}}
                    <main class="flex-1 bg-gray-100 dark:bg-gray-900">
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
                    <footer class="bg-white dark:bg-gray-800 border-t border-gray-200 dark:border-gray-700 py-4 px-4 sm:px-6 lg:px-8 no-print">
                        <div class="flex flex-col sm:flex-row justify-between items-center text-sm text-gray-500 dark:text-gray-400">
                            <p>&copy; {{ date('Y') }} Bobon B Elementary School Library. All rights reserved.</p>
                            <p class="mt-2 sm:mt-0">Library Management System v1.0</p>
                        </div>
                    </footer>
                </div>
            </div>
        </div>

        {{-- Livewire scripts for dynamic components --}}
        @livewireScripts

        {{-- Global Livewire loading indicator --}}
        <div wire:loading.delay class="fixed top-0 left-0 right-0 z-50 no-print">
            <div class="h-1 bg-primary-500 animate-pulse"></div>
        </div>

        {{-- Page-specific scripts (e.g., Chart.js for reports) --}}
        @stack('scripts')

        {{-- Keyboard shortcuts --}}
        <script>
            document.addEventListener('keydown', function(e) {
                // Ctrl+K or Cmd+K - Quick search (if search field exists)
                if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
                    e.preventDefault();
                    const searchInput = document.querySelector('[data-search-input]');
                    if (searchInput) {
                        searchInput.focus();
                    }
                }

                // Escape - Close modals
                if (e.key === 'Escape') {
                    // Alpine.js handles this, but we can add custom logic here if needed
                }
            });
        </script>
    </body>
</html>
