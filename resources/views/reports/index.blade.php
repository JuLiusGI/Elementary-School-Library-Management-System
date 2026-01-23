{{--
    Reports Dashboard

    This is the main reports hub that provides:
    - Quick statistics overview
    - Links to all available reports
    - Quick export options

    @author Library Management System
--}}

@extends('layouts.app')

@section('title', 'Reports')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Page Header --}}
    <div class="mb-6">
        <h1 class="text-2xl font-bold text-gray-900">Reports & Analytics</h1>
        <p class="text-gray-600 mt-1">Generate and export library reports</p>
    </div>

    {{-- Quick Statistics --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4 mb-8">
        {{-- Total Books --}}
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Books</p>
                    <p class="text-xl font-semibold text-gray-900">{{ number_format($statistics['total_books']) }}</p>
                    <p class="text-xs text-gray-500">{{ number_format($statistics['available_copies']) }} available</p>
                </div>
            </div>
        </div>

        {{-- Active Students --}}
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Active Students</p>
                    <p class="text-xl font-semibold text-gray-900">{{ number_format($statistics['total_students']) }}</p>
                    <p class="text-xs text-gray-500">{{ number_format($statistics['students_with_borrowed_books']) }} currently borrowing</p>
                </div>
            </div>
        </div>

        {{-- Today's Activity --}}
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Today's Activity</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $todayTransactions['borrowed_count'] + $todayTransactions['returned_count'] }}</p>
                    <p class="text-xs text-gray-500">{{ $todayTransactions['borrowed_count'] }} borrowed, {{ $todayTransactions['returned_count'] }} returned</p>
                </div>
            </div>
        </div>

        {{-- Overdue --}}
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-red-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Overdue Books</p>
                    <p class="text-xl font-semibold text-gray-900">{{ number_format($overdueCount) }}</p>
                    <p class="text-xs text-gray-500">P{{ number_format($statistics['total_unpaid_fines'], 2) }} in fines</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Report Cards --}}
    <h2 class="text-lg font-semibold text-gray-900 mb-4">Available Reports</h2>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        {{-- Daily Transactions Report --}}
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-blue-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-3 7h3m-3 4h3m-6-4h.01M9 16h.01"></path>
                        </svg>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">Daily Transactions</h3>
                </div>
                <p class="text-gray-600 text-sm mb-4">View all borrowing and return transactions for a specific date. Track daily library activity.</p>
                <div class="flex justify-between items-center">
                    <a href="{{ route('reports.daily-transactions') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View Report &rarr;
                    </a>
                    <div class="flex space-x-2">
                        <a href="{{ route('reports.export.pdf', ['reportType' => 'daily']) }}" class="text-gray-400 hover:text-red-600" title="Export PDF">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </a>
                        <a href="{{ route('reports.export.csv', ['reportType' => 'daily']) }}" class="text-gray-400 hover:text-green-600" title="Export CSV">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Overdue Books Report --}}
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-red-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">Overdue Books</h3>
                </div>
                <p class="text-gray-600 text-sm mb-4">List of all books that are past their due date. Includes student contact info and calculated fines.</p>
                <div class="flex justify-between items-center">
                    <a href="{{ route('reports.overdue') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View Report &rarr;
                    </a>
                    <div class="flex space-x-2">
                        <a href="{{ route('reports.export.pdf', ['reportType' => 'overdue']) }}" class="text-gray-400 hover:text-red-600" title="Export PDF">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </a>
                        <a href="{{ route('reports.export.csv', ['reportType' => 'overdue']) }}" class="text-gray-400 hover:text-green-600" title="Export CSV">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Most Borrowed Books Report --}}
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-yellow-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.049 2.927c.3-.921 1.603-.921 1.902 0l1.519 4.674a1 1 0 00.95.69h4.915c.969 0 1.371 1.24.588 1.81l-3.976 2.888a1 1 0 00-.363 1.118l1.518 4.674c.3.922-.755 1.688-1.538 1.118l-3.976-2.888a1 1 0 00-1.176 0l-3.976 2.888c-.783.57-1.838-.197-1.538-1.118l1.518-4.674a1 1 0 00-.363-1.118l-3.976-2.888c-.784-.57-.38-1.81.588-1.81h4.914a1 1 0 00.951-.69l1.519-4.674z"></path>
                        </svg>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">Most Borrowed</h3>
                </div>
                <p class="text-gray-600 text-sm mb-4">Rankings of the most popular books based on borrowing frequency. Filter by date range.</p>
                <div class="flex justify-between items-center">
                    <a href="{{ route('reports.most-borrowed') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View Report &rarr;
                    </a>
                    <div class="flex space-x-2">
                        <a href="{{ route('reports.export.pdf', ['reportType' => 'most-borrowed']) }}" class="text-gray-400 hover:text-red-600" title="Export PDF">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </a>
                        <a href="{{ route('reports.export.csv', ['reportType' => 'most-borrowed']) }}" class="text-gray-400 hover:text-green-600" title="Export CSV">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Inventory Report --}}
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-purple-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">Inventory</h3>
                </div>
                <p class="text-gray-600 text-sm mb-4">Complete inventory overview. Total books, availability, condition status, and category breakdown.</p>
                <div class="flex justify-between items-center">
                    <a href="{{ route('reports.inventory') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View Report &rarr;
                    </a>
                    <div class="flex space-x-2">
                        <a href="{{ route('reports.export.pdf', ['reportType' => 'inventory']) }}" class="text-gray-400 hover:text-red-600" title="Export PDF">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </a>
                        <a href="{{ route('reports.export.csv', ['reportType' => 'inventory']) }}" class="text-gray-400 hover:text-green-600" title="Export CSV">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Monthly Statistics Report --}}
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-green-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">Monthly Statistics</h3>
                </div>
                <p class="text-gray-600 text-sm mb-4">Detailed monthly analytics with charts. Daily breakdown, top borrowers, and popular books.</p>
                <div class="flex justify-between items-center">
                    <a href="{{ route('reports.monthly-stats') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View Report &rarr;
                    </a>
                    <div class="flex space-x-2">
                        <a href="{{ route('reports.export.pdf', ['reportType' => 'monthly']) }}" class="text-gray-400 hover:text-red-600" title="Export PDF">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </a>
                        <a href="{{ route('reports.export.csv', ['reportType' => 'monthly']) }}" class="text-gray-400 hover:text-green-600" title="Export CSV">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>

        {{-- Students with Fines Report --}}
        <div class="bg-white rounded-lg shadow hover:shadow-lg transition-shadow">
            <div class="p-6">
                <div class="flex items-center mb-4">
                    <div class="bg-orange-100 rounded-lg p-3">
                        <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                        </svg>
                    </div>
                    <h3 class="ml-3 text-lg font-semibold text-gray-900">Students with Fines</h3>
                </div>
                <p class="text-gray-600 text-sm mb-4">List of students with unpaid fines. Total amounts and number of overdue incidents.</p>
                <div class="flex justify-between items-center">
                    <a href="{{ route('transactions.fines') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
                        View Fines &rarr;
                    </a>
                    <div class="flex space-x-2">
                        <a href="{{ route('reports.export.pdf', ['reportType' => 'students-with-fines']) }}" class="text-gray-400 hover:text-red-600" title="Export PDF">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </a>
                        <a href="{{ route('reports.export.csv', ['reportType' => 'students-with-fines']) }}" class="text-gray-400 hover:text-green-600" title="Export CSV">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                            </svg>
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
