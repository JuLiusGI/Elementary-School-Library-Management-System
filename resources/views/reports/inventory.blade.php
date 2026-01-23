{{--
    Inventory Report

    Shows comprehensive library inventory statistics:
    - Summary cards (total, available, borrowed)
    - Utilization rate
    - Books by condition chart
    - Books by category chart
    - Export options

    @author Library Management System
--}}

@extends('layouts.app')

@section('title', 'Inventory Report')

@section('content')
<div class="container mx-auto px-4 py-6">
    {{-- Page Header --}}
    <div class="flex flex-col md:flex-row md:items-center md:justify-between mb-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">Inventory Report</h1>
            <p class="text-gray-600 mt-1">As of {{ now()->format('F j, Y') }}</p>
        </div>
        <div class="mt-4 md:mt-0 flex gap-2">
            {{-- Export Buttons --}}
            <a href="{{ route('reports.export.pdf', ['reportType' => 'inventory']) }}"
               class="inline-flex items-center px-3 py-2 border border-red-300 shadow-sm text-sm font-medium rounded-md text-red-700 bg-white hover:bg-red-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                </svg>
                PDF
            </a>
            <a href="{{ route('reports.export.csv', ['reportType' => 'inventory']) }}"
               class="inline-flex items-center px-3 py-2 border border-green-300 shadow-sm text-sm font-medium rounded-md text-green-700 bg-white hover:bg-green-50">
                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                CSV
            </a>
        </div>
    </div>

    {{-- Summary Cards --}}
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-4 mb-8">
        {{-- Total Titles --}}
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-blue-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Titles</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($inventory['total_titles']) }}</p>
                </div>
            </div>
        </div>

        {{-- Total Copies --}}
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7v8a2 2 0 002 2h6M8 7V5a2 2 0 012-2h4.586a1 1 0 01.707.293l4.414 4.414a1 1 0 01.293.707V15a2 2 0 01-2 2h-2M8 7H6a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2v-2"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Total Copies</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($inventory['total_copies']) }}</p>
                </div>
            </div>
        </div>

        {{-- Available Copies --}}
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Available</p>
                    <p class="text-2xl font-bold text-green-600">{{ number_format($inventory['available_copies']) }}</p>
                </div>
            </div>
        </div>

        {{-- Borrowed Copies --}}
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Borrowed</p>
                    <p class="text-2xl font-bold text-yellow-600">{{ number_format($inventory['borrowed_copies']) }}</p>
                </div>
            </div>
        </div>

        {{-- Utilization Rate --}}
        <div class="bg-white rounded-lg shadow p-5">
            <div class="flex items-center">
                <div class="flex-shrink-0 bg-indigo-100 rounded-lg p-3">
                    <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm text-gray-500">Utilization</p>
                    <p class="text-2xl font-bold text-indigo-600">{{ $inventory['utilization_rate'] }}%</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Utilization Gauge --}}
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold text-gray-900 mb-4">Library Utilization</h3>
        <div class="flex items-center justify-center">
            <div class="relative w-48 h-48">
                <svg class="w-48 h-48 transform -rotate-90" viewBox="0 0 160 160">
                    {{-- Background circle --}}
                    <circle cx="80" cy="80" r="70" fill="none" stroke="#e5e7eb" stroke-width="16"/>
                    {{-- Progress circle --}}
                    <circle cx="80" cy="80" r="70" fill="none"
                            stroke="{{ $inventory['utilization_rate'] > 80 ? '#ef4444' : ($inventory['utilization_rate'] > 50 ? '#f59e0b' : '#10b981') }}"
                            stroke-width="16"
                            stroke-dasharray="{{ 2 * 3.14159 * 70 }}"
                            stroke-dashoffset="{{ 2 * 3.14159 * 70 * (1 - $inventory['utilization_rate'] / 100) }}"
                            stroke-linecap="round"/>
                </svg>
                <div class="absolute inset-0 flex items-center justify-center">
                    <div class="text-center">
                        <span class="text-3xl font-bold text-gray-900">{{ $inventory['utilization_rate'] }}%</span>
                        <p class="text-sm text-gray-500">of books borrowed</p>
                    </div>
                </div>
            </div>
        </div>
        <p class="text-center text-sm text-gray-600 mt-4">
            {{ $inventory['borrowed_copies'] }} out of {{ $inventory['total_copies'] }} copies are currently checked out
        </p>
    </div>

    {{-- Charts Row --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        {{-- Books by Condition --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Books by Condition</h3>
            <canvas id="conditionChart" height="250"></canvas>
        </div>

        {{-- Books by Category --}}
        <div class="bg-white rounded-lg shadow p-6">
            <h3 class="text-lg font-semibold text-gray-900 mb-4">Books by Category</h3>
            <canvas id="categoryChart" height="250"></canvas>
        </div>
    </div>

    {{-- Detailed Tables --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        {{-- By Condition Table --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Condition Breakdown</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Condition</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $conditionColors = [
                                'new' => 'bg-green-100 text-green-800',
                                'good' => 'bg-blue-100 text-blue-800',
                                'fair' => 'bg-yellow-100 text-yellow-800',
                                'poor' => 'bg-orange-100 text-orange-800',
                                'damaged' => 'bg-red-100 text-red-800',
                            ];
                            $totalCondition = array_sum($inventory['by_condition']);
                        @endphp
                        @foreach($inventory['by_condition'] as $condition => $count)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium {{ $conditionColors[$condition] ?? 'bg-gray-100 text-gray-800' }}">
                                        {{ ucfirst($condition) }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ number_format($count) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="text-sm text-gray-500 mr-2">
                                            {{ $totalCondition > 0 ? round(($count / $totalCondition) * 100, 1) : 0 }}%
                                        </span>
                                        <div class="w-16 bg-gray-200 rounded-full h-2">
                                            <div class="bg-blue-600 h-2 rounded-full"
                                                 style="width: {{ $totalCondition > 0 ? ($count / $totalCondition) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>

        {{-- By Category Table --}}
        <div class="bg-white rounded-lg shadow">
            <div class="px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900">Category Breakdown</h3>
            </div>
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Category</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Count</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Percentage</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @php
                            $totalCategory = array_sum($inventory['by_category']);
                        @endphp
                        @foreach($inventory['by_category'] as $category => $count)
                            <tr class="hover:bg-gray-50">
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ $category }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                    {{ number_format($count) }}
                                </td>
                                <td class="px-6 py-4 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <span class="text-sm text-gray-500 mr-2">
                                            {{ $totalCategory > 0 ? round(($count / $totalCategory) * 100, 1) : 0 }}%
                                        </span>
                                        <div class="w-16 bg-gray-200 rounded-full h-2">
                                            <div class="bg-green-600 h-2 rounded-full"
                                                 style="width: {{ $totalCategory > 0 ? ($count / $totalCategory) * 100 : 0 }}%">
                                            </div>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
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
    // Condition Pie Chart
    const conditionCtx = document.getElementById('conditionChart').getContext('2d');
    new Chart(conditionCtx, {
        type: 'pie',
        data: {
            labels: {!! json_encode(array_map('ucfirst', array_keys($inventory['by_condition']))) !!},
            datasets: [{
                data: {!! json_encode(array_values($inventory['by_condition'])) !!},
                backgroundColor: [
                    'rgba(16, 185, 129, 0.8)',  // new - green
                    'rgba(59, 130, 246, 0.8)',  // good - blue
                    'rgba(245, 158, 11, 0.8)',  // fair - yellow
                    'rgba(249, 115, 22, 0.8)',  // poor - orange
                    'rgba(239, 68, 68, 0.8)',   // damaged - red
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
                    position: 'bottom',
                    labels: {
                        padding: 20
                    }
                }
            }
        }
    });

    // Category Bar Chart
    const categoryCtx = document.getElementById('categoryChart').getContext('2d');
    new Chart(categoryCtx, {
        type: 'bar',
        data: {
            labels: {!! json_encode(array_keys($inventory['by_category'])) !!},
            datasets: [{
                label: 'Books',
                data: {!! json_encode(array_values($inventory['by_category'])) !!},
                backgroundColor: 'rgba(16, 185, 129, 0.8)',
                borderColor: 'rgba(16, 185, 129, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    display: false
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
                    ticks: {
                        maxRotation: 45,
                        minRotation: 45
                    }
                }
            }
        }
    });
</script>
@endpush
@endsection
