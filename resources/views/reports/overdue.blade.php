{{--
    Overdue Books Report

    Shows all books that are currently past their due date.
    Features:
    - Sortable by various columns
    - Shows calculated fines
    - Student contact information
    - Export options (PDF, CSV)

    @author Library Management System
--}}

@extends('layouts.app')

@section('title', 'Overdue Books Report')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Overdue Books Report</h1>
            <p class="text-gray-600 mt-1">As of {{ now()->format('F j, Y') }}</p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-2">
            {{-- Export Buttons --}}
            <a href="{{ route('reports.export.pdf', ['reportType' => 'overdue']) }}"
               class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                PDF
            </a>
            <a href="{{ route('reports.export.csv', ['reportType' => 'overdue']) }}"
               class="inline-flex items-center px-3 py-2 border border-green-300 shadow-sm text-sm font-medium rounded-md text-green-700 bg-white hover:bg-green-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                CSV
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-4 mb-8">
        <div class="bg-red-50 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-red-100 rounded-lg p-2">
                    <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-red-600 font-medium">Overdue Books</p>
                    <p class="text-2xl font-bold text-red-900">{{ $overdueBooks->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-orange-50 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-orange-100 rounded-lg p-2">
                    <svg class="w-5 h-5 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-orange-600 font-medium">Students Affected</p>
                    <p class="text-2xl font-bold text-orange-900">{{ $overdueBooks->unique('student_id')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-yellow-50 rounded-lg p-4">
            <div class="flex items-center">
                <div class="bg-yellow-100 rounded-lg p-2">
                    <svg class="w-5 h-5 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
                <div class="ml-3">
                    <p class="text-sm text-yellow-600 font-medium">Total Fines</p>
                    <p class="text-2xl font-bold text-yellow-900">P{{ number_format($totalFines, 2) }}</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Overdue Books Table --}}
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Overdue Transactions</h2>
        </div>

        @if($overdueBooks->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('reports.overdue', ['sort' => 'student', 'order' => $sortBy === 'student' && $sortOrder === 'asc' ? 'desc' : 'asc']) }}"
                                   class="flex items-center hover:text-gray-700">
                                    Student
                                    @if($sortBy === 'student')
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($sortOrder === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            @endif
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('reports.overdue', ['sort' => 'due_date', 'order' => $sortBy === 'due_date' && $sortOrder === 'asc' ? 'desc' : 'asc']) }}"
                                   class="flex items-center hover:text-gray-700">
                                    Due Date
                                    @if($sortBy === 'due_date')
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($sortOrder === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            @endif
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('reports.overdue', ['sort' => 'days_overdue', 'order' => $sortBy === 'days_overdue' && $sortOrder === 'asc' ? 'desc' : 'asc']) }}"
                                   class="flex items-center hover:text-gray-700">
                                    Days Overdue
                                    @if($sortBy === 'days_overdue')
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($sortOrder === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            @endif
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                <a href="{{ route('reports.overdue', ['sort' => 'fine', 'order' => $sortBy === 'fine' && $sortOrder === 'asc' ? 'desc' : 'asc']) }}"
                                   class="flex items-center hover:text-gray-700">
                                    Fine
                                    @if($sortBy === 'fine')
                                        <svg class="w-4 h-4 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            @if($sortOrder === 'asc')
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 15l7-7 7 7"></path>
                                            @else
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                            @endif
                                        </svg>
                                    @endif
                                </a>
                            </th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($overdueBooks as $transaction)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="text-sm font-medium text-gray-900">{{ $transaction->student->full_name }}</div>
                                    <div class="text-sm text-gray-500">{{ $transaction->student->grade_level }} - {{ $transaction->student->section }}</div>
                                    @if($transaction->student->contact_number)
                                        <div class="text-xs text-gray-400">{{ $transaction->student->contact_number }}</div>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $transaction->book->title }}</div>
                                    <div class="text-sm text-gray-500">{{ $transaction->book->accession_number }}</div>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $transaction->due_date->format('M d, Y') }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @php
                                        $daysOverdue = $transaction->days_overdue;
                                        $badgeClass = $daysOverdue <= 7 ? 'bg-yellow-100 text-yellow-800' :
                                                     ($daysOverdue <= 14 ? 'bg-orange-100 text-orange-800' : 'bg-red-100 text-red-800');
                                    @endphp
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $badgeClass }}">
                                        {{ $daysOverdue }} {{ Str::plural('day', $daysOverdue) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="text-sm font-medium text-red-600">P{{ number_format($transaction->calculated_fine, 2) }}</span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm">
                                    <a href="{{ route('transactions.return') }}?student={{ $transaction->student_id }}"
                                       class="text-blue-600 hover:text-blue-800 font-medium">
                                        Process Return
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @else
            <div class="px-6 py-12 text-center">
                <svg class="mx-auto h-12 w-12 text-green-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <h3 class="mt-2 text-lg font-medium text-gray-900">No Overdue Books</h3>
                <p class="mt-1 text-gray-500">All borrowed books are within their due dates.</p>
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
