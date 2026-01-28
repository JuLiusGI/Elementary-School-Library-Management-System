{{--
    Dashboard Stats Livewire Component
    ===================================

    This component displays real-time dashboard statistics with auto-refresh.
    Features:
    - Statistics cards with icons and color-coding
    - Auto-refresh every 60 seconds
    - Quick view of overdue alerts and low stock warnings
    - Charts for visual data representation

    Usage:
    <livewire:dashboard-stats />

    @see App\Livewire\DashboardStats
--}}

{{-- Wrap entire component in wire:poll for auto-refresh --}}
<div wire:poll.60s="refresh">

    {{-- ================================================================
         LAST REFRESH INDICATOR
         Shows when the data was last updated
         ================================================================ --}}
    <div class="flex items-center justify-between mb-6">
        <div class="text-sm text-gray-500">
            <span class="hidden sm:inline">Last updated: </span>{{ $lastRefresh }}
        </div>
        <button wire:click="refresh"
                wire:loading.attr="disabled"
                class="inline-flex items-center px-3 py-1 text-sm text-primary-600 hover:text-primary-800 focus:outline-none">
            <svg wire:loading.class="animate-spin" class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"/>
            </svg>
            <span wire:loading.remove>Refresh</span>
            <span wire:loading>Refreshing...</span>
        </button>
    </div>

    {{-- ================================================================
         STATISTICS CARDS
         Four cards showing key metrics
         ================================================================ --}}
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">

        {{-- Total Books Card --}}
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-primary-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-primary-100 text-primary-600">
                    {{-- Book Icon --}}
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 6.253v13m0-13C10.832 5.477 9.246 5 7.5 5S4.168 5.477 3 6.253v13C4.168 18.477 5.754 18 7.5 18s3.332.477 4.5 1.253m0-13C13.168 5.477 14.754 5 16.5 5c1.747 0 3.332.477 4.5 1.253v13C19.832 18.477 18.247 18 16.5 18c-1.746 0-3.332.477-4.5 1.253"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Total Books</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($this->statistics['total_books']) }}</p>
                    <p class="text-xs text-gray-500">{{ number_format($this->statistics['total_copies']) }} copies</p>
                </div>
            </div>
        </div>

        {{-- Available Books Card --}}
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-success-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-success-100 text-success-600">
                    {{-- Check Icon --}}
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Available</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($this->statistics['available_copies']) }}</p>
                    <p class="text-xs text-success-600">{{ $this->statistics['availability_percentage'] }}% available</p>
                </div>
            </div>
        </div>

        {{-- Active Students Card --}}
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-warning-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-warning-100 text-warning-600">
                    {{-- Users Icon --}}
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Active Students</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($this->statistics['active_students']) }}</p>
                    <p class="text-xs text-gray-500">registered</p>
                </div>
            </div>
        </div>

        {{-- Currently Borrowed Card --}}
        <div class="bg-white rounded-lg shadow-sm p-6 border-l-4 border-danger-500">
            <div class="flex items-center">
                <div class="p-3 rounded-full bg-danger-100 text-danger-600">
                    {{-- Clipboard Icon --}}
                    <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
                    </svg>
                </div>
                <div class="ml-4">
                    <p class="text-sm font-medium text-gray-500">Borrowed</p>
                    <p class="text-2xl font-bold text-gray-900">{{ number_format($this->statistics['currently_borrowed']) }}</p>
                    @if($this->statistics['overdue_count'] > 0)
                        <p class="text-xs text-danger-600">{{ $this->statistics['overdue_count'] }} overdue</p>
                    @else
                        <p class="text-xs text-gray-500">currently out</p>
                    @endif
                </div>
            </div>
        </div>

    </div>

    {{-- ================================================================
         TODAY'S ACTIVITY SUMMARY
         Quick stats for today's transactions
         ================================================================ --}}
    <div class="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-lg shadow-sm p-4 text-center">
            <p class="text-3xl font-bold text-primary-600">{{ $this->statistics['borrowed_today'] }}</p>
            <p class="text-sm text-gray-500">Borrowed Today</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 text-center">
            <p class="text-3xl font-bold text-success-600">{{ $this->statistics['returned_today'] }}</p>
            <p class="text-sm text-gray-500">Returned Today</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 text-center">
            <p class="text-3xl font-bold text-danger-600">{{ $this->statistics['overdue_count'] }}</p>
            <p class="text-sm text-gray-500">Overdue Books</p>
        </div>
        <div class="bg-white rounded-lg shadow-sm p-4 text-center">
            <p class="text-3xl font-bold text-warning-600">{{ number_format($this->statistics['unpaid_fines'], 2) }}</p>
            <p class="text-sm text-gray-500">Unpaid Fines</p>
        </div>
    </div>

    {{-- ================================================================
         ALERTS AND WARNINGS SECTION
         ================================================================ --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">

        {{-- Overdue Books Alert --}}
        @if($this->overdueAlerts->count() > 0)
        <div class="bg-danger-50 border border-danger-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-danger-800 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"/>
                    </svg>
                    Overdue Books Alert
                </h3>
                <a href="{{ route('transactions.fines') }}" class="text-sm text-danger-600 hover:text-danger-800">
                    View All
                </a>
            </div>
            <div class="space-y-2">
                @foreach($this->overdueAlerts as $transaction)
                <div class="bg-white rounded p-3 flex justify-between items-center">
                    <div>
                        <p class="font-medium text-gray-900">{{ $transaction->book->title ?? 'Unknown Book' }}</p>
                        <p class="text-sm text-gray-600">{{ $transaction->student->full_name ?? 'Unknown Student' }}</p>
                    </div>
                    <span class="text-danger-600 font-semibold">
                        {{ $transaction->days_overdue }} days overdue
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- Low Stock Books Warning --}}
        @if($this->lowStockBooks->count() > 0)
        <div class="bg-warning-50 border border-warning-200 rounded-lg p-4">
            <div class="flex items-center justify-between mb-3">
                <h3 class="text-lg font-semibold text-warning-800 flex items-center">
                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                              d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                    </svg>
                    Low Stock Books
                </h3>
                <a href="{{ route('books.index') }}" class="text-sm text-warning-600 hover:text-warning-800">
                    View All Books
                </a>
            </div>
            <div class="space-y-2">
                @foreach($this->lowStockBooks as $book)
                <div class="bg-white rounded p-3 flex justify-between items-center">
                    <div>
                        <p class="font-medium text-gray-900">{{ $book->title }}</p>
                        <p class="text-sm text-gray-600">{{ $book->category->name ?? 'Uncategorized' }}</p>
                    </div>
                    <span class="px-2 py-1 text-xs rounded-full {{ $book->copies_available == 0 ? 'bg-danger-100 text-danger-800' : 'bg-warning-100 text-warning-800' }}">
                        {{ $book->copies_available }} available
                    </span>
                </div>
                @endforeach
            </div>
        </div>
        @endif

        {{-- If no alerts, show success message --}}
        @if($this->overdueAlerts->count() == 0 && $this->lowStockBooks->count() == 0)
        <div class="lg:col-span-2 bg-success-50 border border-success-200 rounded-lg p-6 text-center">
            <svg class="w-12 h-12 mx-auto text-success-500 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                      d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
            </svg>
            <h3 class="text-lg font-semibold text-success-800">All Clear!</h3>
            <p class="text-success-600">No overdue books or low stock warnings at this time.</p>
        </div>
        @endif

    </div>

    {{-- ================================================================
         RECENT TRANSACTIONS
         Quick view of last 5 transactions
         ================================================================ --}}
    <div class="bg-white rounded-lg shadow-sm p-6 mb-6">
        <div class="flex items-center justify-between mb-4">
            <h3 class="text-lg font-semibold text-gray-800">Recent Transactions</h3>
            <a href="{{ route('transactions.history') }}" class="text-sm text-primary-600 hover:text-primary-800">
                View All
            </a>
        </div>
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Student</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Book</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                        <th class="px-4 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($this->recentTransactions as $transaction)
                    <tr class="hover:bg-gray-50">
                        <td class="px-4 py-3 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $transaction->student->full_name ?? 'N/A' }}</div>
                            <div class="text-xs text-gray-500">{{ $transaction->student->student_id ?? '' }}</div>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-sm text-gray-900">{{ Str::limit($transaction->book->title ?? 'N/A', 30) }}</div>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap">
                            <span class="px-2 py-1 text-xs font-medium rounded-full {{ $transaction->status_color }}">
                                {{ $transaction->status_label }}
                            </span>
                        </td>
                        <td class="px-4 py-3 whitespace-nowrap text-sm text-gray-500">
                            {{ $transaction->created_at->diffForHumans() }}
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="4" class="px-4 py-8 text-center text-gray-500">
                            No recent transactions found.
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
