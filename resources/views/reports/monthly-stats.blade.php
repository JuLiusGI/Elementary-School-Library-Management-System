{{--
    Monthly Statistics Report

    Shows detailed statistics for a specific month:
    - Summary cards
    - Daily activity line chart
    - Top borrowers
    - Most popular books
    - Grade level breakdown
    - Export options

    @author Library Management System
--}}

@extends('layouts.app')

@section('title', 'Monthly Statistics Report')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Monthly Statistics</h1>
            <p class="text-gray-600 mt-1">{{ $statistics['month'] }} {{ $statistics['year'] }}</p>
        </div>
        <div class="mt-4 md:mt-0 flex flex-col sm:flex-row gap-3">
            {{-- Month/Year Selector --}}
            <form method="GET" action="{{ route('reports.monthly-stats') }}" class="flex items-center gap-2">
                <select name="month" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    @foreach(range(1, 12) as $m)
                        <option value="{{ $m }}" {{ $selectedMonth == $m ? 'selected' : '' }}>
                            {{ \Carbon\Carbon::create()->month($m)->format('F') }}
                        </option>
                    @endforeach
                </select>
                <select name="year" class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    @foreach(range(date('Y'), date('Y') - 5) as $y)
                        <option value="{{ $y }}" {{ $selectedYear == $y ? 'selected' : '' }}>{{ $y }}</option>
                    @endforeach
                </select>
                <button type="submit" class="inline-flex items-center px-3 py-2 bg-blue-600 text-white text-sm font-medium rounded-md hover:bg-blue-700">
                    Go
                </button>
            </form>

            {{-- Export Buttons --}}
            <div class="flex gap-2">
                <a href="{{ route('reports.export.pdf', ['reportType' => 'monthly', 'month' => $selectedMonth, 'year' => $selectedYear]) }}"
                   class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                    </svg>
                    PDF
                </a>
                <a href="{{ route('reports.export.csv', ['reportType' => 'monthly', 'month' => $selectedMonth, 'year' => $selectedYear]) }}"
                   class="inline-flex items-center px-3 py-2 border border-green-300 shadow-sm text-sm font-medium rounded-md text-green-700 bg-white hover:bg-green-50">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                    </svg>
                    CSV
                </a>
            </div>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4 mb-8">
        {{-- Total Borrowed --}}
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Borrowed</p>
            <p class="text-2xl font-bold text-blue-600">{{ number_format($statistics['summary']['total_borrowed']) }}</p>
        </div>

        {{-- Total Returned --}}
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Returned</p>
            <p class="text-2xl font-bold text-green-600">{{ number_format($statistics['summary']['total_returned']) }}</p>
        </div>

        {{-- Unique Borrowers --}}
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Borrowers</p>
            <p class="text-2xl font-bold text-purple-600">{{ number_format($statistics['summary']['unique_borrowers']) }}</p>
        </div>

        {{-- Overdue --}}
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Overdue</p>
            <p class="text-2xl font-bold text-red-600">{{ number_format($statistics['summary']['overdue_count']) }}</p>
        </div>

        {{-- Fines Generated --}}
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Fines Generated</p>
            <p class="text-2xl font-bold text-orange-600">P{{ number_format($statistics['summary']['fines_generated'], 2) }}</p>
        </div>

        {{-- Fines Collected --}}
        <div class="bg-white rounded-lg shadow p-4">
            <p class="text-sm text-gray-500">Fines Collected</p>
            <p class="text-2xl font-bold text-yellow-600">P{{ number_format($statistics['summary']['fines_collected'], 2) }}</p>
        </div>
    </div>

    {{-- Daily Activity Chart --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Daily Activity</h3>
        <div class="h-72">
            <canvas id="dailyActivityChart"></canvas>
        </div>
        <div class="mt-4 text-center text-sm text-gray-500">
            Average: {{ $statistics['summary']['average_daily_borrows'] }} books borrowed per day
        </div>
    </div>

    {{-- Annual Comparison Chart --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">{{ $selectedYear }} Monthly Comparison</h3>
        <div class="h-72">
            <canvas id="annualComparisonChart"></canvas>
        </div>
    </div>

    {{-- Two Column Layout --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Top Borrowers --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Top Borrowers</h3>
            </div>
            @if($statistics['top_borrowers']->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Books</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($statistics['top_borrowers'] as $index => $borrower)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        @if($index < 3)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full
                                                {{ $index === 0 ? 'bg-yellow-100 text-yellow-800' :
                                                   ($index === 1 ? 'bg-gray-100 text-gray-800' : 'bg-orange-100 text-orange-800') }}
                                                font-bold text-xs">
                                                {{ $index + 1 }}
                                            </span>
                                        @else
                                            <span class="text-gray-500 text-sm pl-1.5">{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $borrower->student->full_name ?? 'Unknown' }}</div>
                                        <div class="text-xs text-gray-500">{{ $borrower->student->grade_level ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-blue-600">
                                        {{ $borrower->borrow_count }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-8 text-center text-gray-500">
                    No borrowing data for this month.
                </div>
            @endif
        </div>

        {{-- Most Popular Books --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Most Popular Books</h3>
            </div>
            @if($statistics['top_books']->count() > 0)
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Times</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($statistics['top_books'] as $index => $book)
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-3 whitespace-nowrap">
                                        @if($index < 3)
                                            <span class="inline-flex items-center justify-center w-6 h-6 rounded-full
                                                {{ $index === 0 ? 'bg-yellow-100 text-yellow-800' :
                                                   ($index === 1 ? 'bg-gray-100 text-gray-800' : 'bg-orange-100 text-orange-800') }}
                                                font-bold text-xs">
                                                {{ $index + 1 }}
                                            </span>
                                        @else
                                            <span class="text-gray-500 text-sm pl-1.5">{{ $index + 1 }}</span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-3">
                                        <div class="text-sm font-medium text-gray-900">{{ Str::limit($book->book->title ?? 'Unknown', 30) }}</div>
                                        <div class="text-xs text-gray-500">{{ $book->book->author ?? '' }}</div>
                                    </td>
                                    <td class="px-6 py-3 whitespace-nowrap text-sm font-medium text-green-600">
                                        {{ $book->borrow_count }}
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <div class="px-6 py-8 text-center text-gray-500">
                    No borrowing data for this month.
                </div>
            @endif
        </div>
    </div>

    {{-- Borrowing by Grade Level --}}
    <div class="bg-white rounded-lg shadow mb-6">
        <div class="px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900">Borrowing by Grade Level</h3>
        </div>
        <div class="p-6">
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
                @php
                    $maxCount = max($statistics['by_grade_level']) ?: 1;
                @endphp
                @foreach($statistics['by_grade_level'] as $grade => $count)
                    <div class="text-center">
                        <div class="relative h-32 bg-gray-100 rounded-lg overflow-hidden mb-2">
                            <div class="absolute bottom-0 left-0 right-0 bg-blue-500 transition-all duration-500"
                                 style="height: {{ ($count / $maxCount) * 100 }}%">
                            </div>
                            <div class="absolute inset-0 flex items-center justify-center">
                                <span class="text-2xl font-bold {{ ($count / $maxCount) > 0.5 ? 'text-white' : 'text-gray-700' }}">
                                    {{ $count }}
                                </span>
                            </div>
                        </div>
                        <p class="text-sm font-medium text-gray-700">{{ $grade }}</p>
                    </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Back to Reports --}}
    <div class="mt-6">
        <a href="{{ route('reports.index') }}" class="text-blue-600 hover:text-blue-800 text-sm font-medium">
            &larr; Back to Reports
        </a>
    </div>
</div>

{{-- Chart.js Scripts --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Daily Activity Line Chart
    const dailyCtx = document.getElementById('dailyActivityChart').getContext('2d');
    const dailyData = @json($statistics['daily_breakdown']);

    new Chart(dailyCtx, {
        type: 'line',
        data: {
            labels: Object.keys(dailyData).map(date => {
                const d = new Date(date);
                return d.getDate();
            }),
            datasets: [
                {
                    label: 'Borrowed',
                    data: Object.values(dailyData).map(d => d.borrowed),
                    borderColor: 'rgba(59, 130, 246, 1)',
                    backgroundColor: 'rgba(59, 130, 246, 0.1)',
                    fill: true,
                    tension: 0.3
                },
                {
                    label: 'Returned',
                    data: Object.values(dailyData).map(d => d.returned),
                    borderColor: 'rgba(16, 185, 129, 1)',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    fill: true,
                    tension: 0.3
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Day of Month'
                    }
                }
            }
        }
    });

    // Annual Comparison Bar Chart
    const annualCtx = document.getElementById('annualComparisonChart').getContext('2d');
    const annualData = @json($annualStats['monthly_breakdown']);

    new Chart(annualCtx, {
        type: 'bar',
        data: {
            labels: Object.keys(annualData),
            datasets: [
                {
                    label: 'Borrowed',
                    data: Object.values(annualData).map(d => d.borrowed),
                    backgroundColor: 'rgba(59, 130, 246, 0.8)',
                    borderColor: 'rgba(59, 130, 246, 1)',
                    borderWidth: 1
                },
                {
                    label: 'Returned',
                    data: Object.values(annualData).map(d => d.returned),
                    backgroundColor: 'rgba(16, 185, 129, 0.8)',
                    borderColor: 'rgba(16, 185, 129, 1)',
                    borderWidth: 1
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'top',
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
