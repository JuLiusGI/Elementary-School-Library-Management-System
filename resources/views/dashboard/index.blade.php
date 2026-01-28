{{--
    Dashboard Index Page
    ====================

    This is the main dashboard page of the Library Management System.
    It provides librarians and administrators with an overview of:
    - Key statistics (books, students, transactions)
    - Recent activity
    - Alerts and warnings
    - Charts for visual representation
    - Quick action buttons

    The page uses a combination of:
    - Regular Blade for static content and charts
    - Livewire component for real-time statistics

    @see App\Http\Controllers\DashboardController
    @see App\Livewire\DashboardStats
--}}

@extends('layouts.app')

@section('title', 'Dashboard')

@section('content')
<div class="py-6 px-4 sm:px-6 lg:px-8">

    {{-- ================================================================
         WELCOME HEADER
         Personalized greeting with current date/time
         ================================================================ --}}
    <div class="mb-6">
        <div class="flex flex-col sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 class="text-2xl sm:text-3xl font-bold text-gray-900">
                    Welcome back, {{ Auth::user()->name ?? 'Librarian' }}!
                </h1>
                <p class="mt-1 text-sm text-gray-500">
                    {{ now()->format('l, F j, Y') }} | {{ now()->format('g:i A') }}
                </p>
            </div>

            {{-- Quick Actions --}}
            <div class="mt-4 sm:mt-0 flex flex-wrap gap-2">
                <a href="{{ route('transactions.borrow') }}"
                   class="inline-flex items-center px-4 py-2 bg-primary-600 text-white text-sm font-medium rounded-lg hover:bg-primary-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/>
                    </svg>
                    Borrow Book
                </a>
                <a href="{{ route('transactions.return') }}"
                   class="inline-flex items-center px-4 py-2 bg-success-600 text-white text-sm font-medium rounded-lg hover:bg-success-700 transition-colors shadow-sm">
                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    Return Book
                </a>
            </div>
        </div>
    </div>

    {{-- ================================================================
         LIVEWIRE DASHBOARD STATS
         Real-time statistics with auto-refresh
         ================================================================ --}}
    <livewire:dashboard-stats />

    {{-- ================================================================
         CHARTS SECTION
         Visual representations of library data
         ================================================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 xl:grid-cols-3 gap-6 mb-6">

        {{-- Weekly Borrowing Trend Chart --}}
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Weekly Activity</h3>
            <div class="h-64">
                <canvas id="weeklyTrendChart"></canvas>
            </div>
        </div>

        {{-- Books by Category Pie Chart --}}
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Books by Category</h3>
            <div class="h-64">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>

        {{-- Top Borrowed Books This Month --}}
        <div class="bg-white rounded-lg shadow-sm p-6">
            <h3 class="text-lg font-semibold text-gray-800 mb-4">Top Borrowed This Month</h3>
            <div class="h-64">
                <canvas id="topBorrowedChart"></canvas>
            </div>
        </div>

    </div>

    {{-- ================================================================
         QUICK ACCESS CARDS
         Navigation shortcuts for common tasks
         ================================================================ --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">

        {{-- Add New Book --}}
        <a href="{{ route('books.create') }}"
           class="bg-white rounded-lg shadow-sm p-6 text-center hover:shadow-md transition-shadow group">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-primary-100 text-primary-600 mb-3 group-hover:bg-primary-200 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                </svg>
            </div>
            <h4 class="font-medium text-gray-900">Add New Book</h4>
            <p class="text-sm text-gray-500 mt-1">Add to catalog</p>
        </a>

        {{-- Add New Student --}}
        <a href="{{ route('students.create') }}"
           class="bg-white rounded-lg shadow-sm p-6 text-center hover:shadow-md transition-shadow group">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-success-100 text-success-600 mb-3 group-hover:bg-success-200 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/>
                </svg>
            </div>
            <h4 class="font-medium text-gray-900">Add Student</h4>
            <p class="text-sm text-gray-500 mt-1">Register new</p>
        </a>

        {{-- View Reports --}}
        <a href="{{ route('reports.index') }}"
           class="bg-white rounded-lg shadow-sm p-6 text-center hover:shadow-md transition-shadow group">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-warning-100 text-warning-600 mb-3 group-hover:bg-warning-200 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/>
                </svg>
            </div>
            <h4 class="font-medium text-gray-900">View Reports</h4>
            <p class="text-sm text-gray-500 mt-1">Analytics</p>
        </a>

        {{-- Manage Fines --}}
        <a href="{{ route('transactions.fines') }}"
           class="bg-white rounded-lg shadow-sm p-6 text-center hover:shadow-md transition-shadow group">
            <div class="inline-flex items-center justify-center w-12 h-12 rounded-full bg-danger-100 text-danger-600 mb-3 group-hover:bg-danger-200 transition-colors">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
            </div>
            <h4 class="font-medium text-gray-900">Manage Fines</h4>
            <p class="text-sm text-gray-500 mt-1">Payments</p>
        </a>

    </div>

</div>
@endsection

@push('scripts')
{{-- Chart.js for data visualization --}}
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    // =========================================================================
    // CHART DATA FROM CONTROLLER
    // =========================================================================

    // Weekly Trend Data
    const weeklyLabels = @json($weeklyTrend['labels']);
    const weeklyBorrowed = @json($weeklyTrend['borrowed']);
    const weeklyReturned = @json($weeklyTrend['returned']);

    // Category Data
    const categoryLabels = @json($booksByCategory['labels']);
    const categoryData = @json($booksByCategory['data']);

    // Top Borrowed Books Data
    const topBorrowedLabels = @json($topBorrowedBooks['labels']);
    const topBorrowedData = @json($topBorrowedBooks['data']);

    // =========================================================================
    // CHART COLOR PALETTE
    // Using the application's color scheme
    // =========================================================================

    const colors = {
        primary: 'rgb(59, 130, 246)',
        primaryLight: 'rgba(59, 130, 246, 0.2)',
        success: 'rgb(16, 185, 129)',
        successLight: 'rgba(16, 185, 129, 0.2)',
        warning: 'rgb(245, 158, 11)',
        warningLight: 'rgba(245, 158, 11, 0.2)',
        danger: 'rgb(239, 68, 68)',
        dangerLight: 'rgba(239, 68, 68, 0.2)',
    };

    // Pie chart colors (for categories)
    const pieColors = [
        'rgb(59, 130, 246)',   // Primary Blue
        'rgb(16, 185, 129)',   // Success Green
        'rgb(245, 158, 11)',   // Warning Yellow
        'rgb(239, 68, 68)',    // Danger Red
        'rgb(139, 92, 246)',   // Purple
        'rgb(236, 72, 153)',   // Pink
        'rgb(20, 184, 166)',   // Teal
        'rgb(249, 115, 22)',   // Orange
    ];

    // =========================================================================
    // WEEKLY TREND LINE CHART
    // Shows borrowing and return activity over the past week
    // =========================================================================

    const weeklyTrendCtx = document.getElementById('weeklyTrendChart');
    if (weeklyTrendCtx) {
        new Chart(weeklyTrendCtx, {
            type: 'line',
            data: {
                labels: weeklyLabels,
                datasets: [
                    {
                        label: 'Borrowed',
                        data: weeklyBorrowed,
                        borderColor: colors.primary,
                        backgroundColor: colors.primaryLight,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    },
                    {
                        label: 'Returned',
                        data: weeklyReturned,
                        borderColor: colors.success,
                        backgroundColor: colors.successLight,
                        fill: true,
                        tension: 0.4,
                        pointRadius: 4,
                        pointHoverRadius: 6,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
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
    }

    // =========================================================================
    // BOOKS BY CATEGORY PIE CHART
    // Shows distribution of books across categories
    // =========================================================================

    const categoryCtx = document.getElementById('categoryChart');
    if (categoryCtx) {
        new Chart(categoryCtx, {
            type: 'doughnut',
            data: {
                labels: categoryLabels,
                datasets: [{
                    data: categoryData,
                    backgroundColor: pieColors.slice(0, categoryLabels.length),
                    borderWidth: 2,
                    borderColor: '#fff',
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                size: 11
                            }
                        }
                    }
                },
                cutout: '60%',
            }
        });
    }

    // =========================================================================
    // TOP BORROWED BOOKS BAR CHART
    // Shows the most popular books this month
    // =========================================================================

    const topBorrowedCtx = document.getElementById('topBorrowedChart');
    if (topBorrowedCtx) {
        new Chart(topBorrowedCtx, {
            type: 'bar',
            data: {
                labels: topBorrowedLabels,
                datasets: [{
                    label: 'Times Borrowed',
                    data: topBorrowedData,
                    backgroundColor: [
                        colors.primary,
                        colors.success,
                        colors.warning,
                        colors.danger,
                        'rgb(139, 92, 246)',
                    ],
                    borderRadius: 4,
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                indexAxis: 'y',
                plugins: {
                    legend: {
                        display: false,
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
    }

    // =========================================================================
    // LIVEWIRE EVENT LISTENERS
    // Refresh charts when Livewire refreshes data
    // =========================================================================

    document.addEventListener('livewire:initialized', () => {
        Livewire.on('stats-refreshed', () => {
            // Charts will be refreshed on next page load
            // For real-time chart updates, you would need to
            // emit chart data from Livewire and update here
        });
    });
</script>
@endpush
