{{--
    Most Borrowed Books Report

    Shows books ranked by borrowing frequency with:
    - Date range filtering
    - Bar chart visualization
    - Category breakdown pie chart
    - Export options

    @author Library Management System
--}}

@extends('layouts.app')

@section('title', 'Most Borrowed Books Report')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Most Borrowed Books</h1>
            <p class="text-gray-600 mt-1">
                @if($startDate && $endDate)
                    {{ $startDate->format('M j, Y') }} - {{ $endDate->format('M j, Y') }}
                @else
                    All Time
                @endif
            </p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-2">
            {{-- Export Buttons --}}
            <a href="{{ route('reports.export.pdf', array_merge(['reportType' => 'most-borrowed'], request()->only(['start_date', 'end_date', 'limit']))) }}"
               class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                PDF
            </a>
            <a href="{{ route('reports.export.csv', array_merge(['reportType' => 'most-borrowed'], request()->only(['start_date', 'end_date', 'limit']))) }}"
               class="inline-flex items-center px-3 py-2 border border-green-300 shadow-sm text-sm font-medium rounded-md text-green-700 bg-white hover:bg-green-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                CSV
            </a>
        </div>
    </div>

    {{-- Filters --}}
    <div class="bg-white rounded-lg shadow p-4 mb-6">
        <form method="GET" action="{{ route('reports.most-borrowed') }}" class="flex flex-wrap gap-4 items-end">
            <div>
                <label for="start_date" class="block text-sm font-medium text-gray-700 mb-1">Start Date</label>
                <input type="date"
                       id="start_date"
                       name="start_date"
                       value="{{ $startDate?->format('Y-m-d') }}"
                       class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label for="end_date" class="block text-sm font-medium text-gray-700 mb-1">End Date</label>
                <input type="date"
                       id="end_date"
                       name="end_date"
                       value="{{ $endDate?->format('Y-m-d') }}"
                       class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
            </div>
            <div>
                <label for="limit" class="block text-sm font-medium text-gray-700 mb-1">Show Top</label>
                <select id="limit"
                        name="limit"
                        class="rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500 text-sm">
                    <option value="5" {{ $limit == 5 ? 'selected' : '' }}>5 books</option>
                    <option value="10" {{ $limit == 10 ? 'selected' : '' }}>10 books</option>
                    <option value="20" {{ $limit == 20 ? 'selected' : '' }}>20 books</option>
                    <option value="50" {{ $limit == 50 ? 'selected' : '' }}>50 books</option>
                </select>
            </div>
            <div class="flex gap-2">
                <button type="submit"
                        class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:bg-blue-700">
                    Apply Filters
                </button>
                <a href="{{ route('reports.most-borrowed') }}"
                   class="inline-flex items-center px-4 py-2 bg-gray-100 border border-gray-300 rounded-md font-semibold text-xs text-gray-700 uppercase tracking-widest hover:bg-gray-200">
                    Reset
                </a>
            </div>
        </form>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Bar Chart - Top Books --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Top {{ $limit }} Borrowed Books</h3>
            <canvas id="topBooksChart" height="300"></canvas>
        </div>

        {{-- Pie Chart - By Category --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Books by Category</h3>
            <canvas id="categoryChart" height="300"></canvas>
        </div>
    </div>

    {{-- Most Borrowed Books Table --}}
    <div class="bg-white rounded-lg shadow">
        <div class="px-6 py-4 border-b border-gray-200">
            <h2 class="text-lg font-semibold text-gray-900">Most Borrowed Books Ranking</h2>
        </div>

        @if($books->count() > 0)
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Rank</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Accession No.</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Times Borrowed</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($books as $index => $book)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    @if($index < 3)
                                        <span class="inline-flex items-center justify-center w-8 h-8 rounded-full
                                            {{ $index === 0 ? 'bg-yellow-100 text-yellow-800' :
                                               ($index === 1 ? 'bg-gray-100 text-gray-800' : 'bg-orange-100 text-orange-800') }}
                                            font-bold text-sm">
                                            {{ $index + 1 }}
                                        </span>
                                    @else
                                        <span class="text-gray-500 font-medium pl-2">{{ $index + 1 }}</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    <div class="text-sm font-medium text-gray-900">{{ $book->title }}</div>
                                </td>
                                <td class="px-6 py-4 text-sm text-gray-500">
                                    {{ $book->author }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                    {{ $book->accession_number }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="text-sm font-medium text-gray-900 mr-2">{{ $book->borrow_count }}</span>
                                        <div class="w-24 bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full"
                                                 style="width: {{ $books->max('borrow_count') > 0 ? ($book->borrow_count / $books->max('borrow_count')) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                    </div>
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
                <h3 class="mt-2 text-lg font-medium text-gray-900">No Borrowing Data</h3>
                <p class="mt-1 text-gray-500">No books have been borrowed during this period.</p>
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

{{-- Chart.js Scripts --}}
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Top Books Bar Chart
    const topBooksCtx = document.getElementById('topBooksChart').getContext('2d');
    new Chart(topBooksCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode($books->pluck('title')->map(fn($t) => Str::limit($t, 20))->toArray()) !!},
            datasets: [{
                label: 'Times Borrowed',
                data: {!! json_encode($books->pluck('borrow_count')->toArray()) !!},
                backgroundColor: 'rgba(59, 130, 246, 0.8)',
                borderColor: 'rgba(59, 130, 246, 1)',
                borderWidth: 1
            }]
        },
        options: {
            indexAxis: 'y',
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
                }
            },
            scales: {
                x: {
                    beginAtZero: true,
                    ticks: {
                        stepSize: 1
                    }
                }
            }
        }
    });

    // Category Pie Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'doughnut',
        data: {
            labels: {!! json_encode($booksByCategory->pluck('name')->toArray()) !!},
            datasets: [{
                data: {!! json_encode($booksByCategory->pluck('books_count')->toArray()) !!},
                backgroundColor: [
                    'rgba(59, 130, 246, 0.8)',
                    'rgba(16, 185, 129, 0.8)',
                    'rgba(245, 158, 11, 0.8)',
                    'rgba(239, 68, 68, 0.8)',
                    'rgba(139, 92, 246, 0.8)',
                    'rgba(236, 72, 153, 0.8)',
                    'rgba(14, 165, 233, 0.8)',
                    'rgba(34, 197, 94, 0.8)',
                ],
                borderWidth: 2,
                borderColor: '#ffffff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right',
                    labels: {
                        boxWidth: 12,
                        padding: 15
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
