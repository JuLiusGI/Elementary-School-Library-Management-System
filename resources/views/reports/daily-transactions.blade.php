{{--
    Daily Transactions Report

    Shows all borrowing and return transactions for a specific date.
    Features:
    - Date selector to view different days
    - Separate sections for borrowed and returned books
    - Export options (PDF, CSV)

    @author Library Management System
--}}

@extends('layouts.app')

@section('title', 'Daily Transactions Report')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Page Header with Date Selector --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Daily Transactions Report</h1>
            <p class="text-gray-600 mt-1">{{ $report['date'] }}</p>
        </div>
        <div class="mt-4 md:mt-0 flex flex-col sm:flex-row gap-3">
            {{-- Date Selector --}}
            <form method="GET" action="{{ route('reports.daily-transactions') }}" class="flex items-center gap-2">
                <input type="date"
                       name="date"
                       value="{{ $selectedDate->format('Y-m-d') }}"
                       class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500"
                       onchange="this.form.submit()">
            </form>

            {{-- Export Buttons --}}
            <div class="flex gap-2">
                <a href="{{ route('reports.export.pdf', ['reportType' => 'daily', 'date' => $selectedDate->format('Y-m-d')]) }}"
                   class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    PDF
                </a>
                <a href="{{ route('reports.export.csv', ['reportType' => 'daily', 'date' => $selectedDate->format('Y-m-d')]) }}"
                   class="inline-flex items-center px-3 py-2 border border-green-300 shadow-sm text-sm font-medium rounded-md text-green-700 bg-white hover:bg-green-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    CSV
                </a>
            </div>
        </div>
    </div>

    {{-- Quick Navigation --}}
    <div class="flex gap-2 mb-6">
        <a href="{{ route('reports.daily-transactions', ['date' => $selectedDate->copy()->subDay()->format('Y-m-d')]) }}"
           class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
            &larr; Previous Day
        </a>
        @if(!$selectedDate->isToday())
            <a href="{{ route('reports.daily-transactions') }}"
               class="inline-flex items-center px-3 py-1.5 border border-blue-300 rounded-md text-sm text-blue-700 bg-blue-50 hover:bg-blue-100">
                Today
            </a>
        @endif
        @if($selectedDate->lt(\Carbon\Carbon::today()))
            <a href="{{ route('reports.daily-transactions', ['date' => $selectedDate->copy()->addDay()->format('Y-m-d')]) }}"
               class="inline-flex items-center px-3 py-1.5 border border-gray-300 rounded-md text-sm text-gray-700 bg-white hover:bg-gray-50">
                Next Day &rarr;
            </a>
        @endif
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-blue-50 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-blue-100 rounded-lg p-2">
                    <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-blue-600 font-medium">Books Borrowed</p>
                    <p class="text-2xl font-bold text-blue-900">{{ $report['borrowed_count'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-green-50 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-green-100 rounded-lg p-2">
                    <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-green-600 font-medium">Books Returned</p>
                    <p class="text-2xl font-bold text-green-900">{{ $report['returned_count'] }}</p>
                </div>
            </div>
        </div>

        <div class="bg-gray-50 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-gray-200 rounded-lg p-2">
                    <svg class="w-5 h-5 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-gray-600 font-medium">Total Transactions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $report['borrowed_count'] + $report['returned_count'] }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Borrowed Books Section --}}
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <span class="bg-blue-100 text-blue-800 text-xs font-medium px-2.5 py-0.5 rounded mr-2">{{ $report['borrowed_count'] }}</span>
                Books Borrowed
            </h2>
        </div>

        @if($report['borrowed']->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Accession No.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Due Date</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Processed By</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($report['borrowed'] as $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $transaction->student->full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $transaction->student->grade_level }} - {{ $transaction->student->section }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $transaction->book->title }}</div>
                                    <div class="text-sm text-gray-500">{{ $transaction->book->author }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $transaction->book->accession_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $transaction->due_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $transaction->librarian->name ?? 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                </svg>
                <p class="mt-2 text-gray-500">No books were borrowed on this date.</p>
            </div>
        @endif
    </div>

    {{-- Returned Books Section --}}
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900 flex items-center">
                <span class="bg-green-100 text-green-800 text-xs font-medium px-2.5 py-0.5 rounded mr-2">{{ $report['returned_count'] }}</span>
                Books Returned
            </h2>
        </div>

        @if($report['returned']->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Accession No.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fine</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Processed By</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($report['returned'] as $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $transaction->student->full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $transaction->student->grade_level }} - {{ $transaction->student->section }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $transaction->book->title }}</div>
                                    <div class="text-sm text-gray-500">{{ $transaction->book->author }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $transaction->book->accession_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($transaction->status === 'returned')
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                            On Time
                                        </span>
                                    @else
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-yellow-100 text-yellow-800">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    @if($transaction->fine_amount > 0)
                                        <span class="text-red-600 font-medium">P{{ number_format($transaction->fine_amount, 2) }}</span>
                                        @if($transaction->fine_paid)
                                            <span class="text-green-600 text-xs ml-1">(Paid)</span>
                                        @else
                                            <span class="text-red-600 text-xs ml-1">(Unpaid)</span>
                                        @endif
                                    @else
                                        <span class="text-gray-500">None</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $transaction->librarian->name ?? 'N/A' }}
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <p class="mt-2 text-gray-500">No books were returned on this date.</p>
            </div>
        @endif
    </div>

    {{-- Back to Reports --}}
    <div class="mt-6">
        <a href="{{ route('reports.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            &larr; Back to Reports
        </a>
    </div>
</div>
@endsection
