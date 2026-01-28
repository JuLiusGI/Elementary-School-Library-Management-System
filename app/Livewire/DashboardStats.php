<?php

/**
 * DashboardStats Livewire Component
 *
 * This component provides real-time dashboard statistics with auto-refresh.
 * It displays key metrics about the library's current state including:
 * - Total books and availability
 * - Active students
 * - Currently borrowed books
 * - Overdue books alert
 * - Recent activity
 *
 * Features:
 * - Auto-refresh every 60 seconds using Livewire polling
 * - Interactive elements for quick navigation
 * - Real-time updates without page refresh
 *
 * @package App\Livewire
 * @see resources/views/livewire/dashboard-stats.blade.php
 */

namespace App\Livewire;

use App\Models\Book;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Livewire\Attributes\Computed;

class DashboardStats extends Component
{
    // =========================================================================
    // PROPERTIES
    // =========================================================================

    /**
     * Refresh interval in seconds (60 seconds = 1 minute)
     *
     * This controls how often the dashboard automatically updates.
     * Using Livewire's wire:poll directive with this interval.
     */
    public int $refreshInterval = 60;

    /**
     * Last refresh timestamp
     *
     * Used to display when the data was last updated.
     */
    public string $lastRefresh = '';

    // =========================================================================
    // LIFECYCLE HOOKS
    // =========================================================================

    /**
     * Mount the component.
     *
     * Called once when the component is first loaded.
     * Initializes the last refresh timestamp.
     */
    public function mount(): void
    {
        $this->lastRefresh = Carbon::now()->format('h:i:s A');
    }

    /**
     * Refresh the component data.
     *
     * Called when the user manually refreshes or by auto-poll.
     * Updates the last refresh timestamp.
     */
    public function refresh(): void
    {
        $this->lastRefresh = Carbon::now()->format('h:i:s A');
    }

    // =========================================================================
    // COMPUTED PROPERTIES
    // =========================================================================

    /**
     * Get main statistics for dashboard cards.
     *
     * Returns an array of statistics:
     * - total_books: Total number of book titles
     * - total_copies: Total copies owned
     * - available_copies: Copies available for borrowing
     * - availability_percentage: Percentage of books available
     * - active_students: Number of active students
     * - currently_borrowed: Books currently checked out
     * - overdue_count: Number of overdue books
     * - unpaid_fines: Total unpaid fines amount
     *
     * @return array Statistics data
     */
    #[Computed]
    public function statistics(): array
    {
        return [
            // Book statistics
            'total_books' => Book::count(),
            'total_copies' => Book::sum('copies_total'),
            'available_copies' => Book::sum('copies_available'),
            'availability_percentage' => $this->calculateAvailabilityPercentage(),

            // Student statistics
            'active_students' => Student::where('status', 'active')->count(),

            // Transaction statistics
            'currently_borrowed' => Transaction::whereIn('status', ['borrowed', 'overdue'])->count(),
            'overdue_count' => Transaction::where('status', 'overdue')->count() +
                Transaction::where('status', 'borrowed')
                    ->where('due_date', '<', Carbon::today())
                    ->count(),

            // Fine statistics
            'unpaid_fines' => Transaction::where('fine_amount', '>', 0)
                ->where('fine_paid', false)
                ->sum('fine_amount'),

            // Today's activity
            'borrowed_today' => Transaction::whereDate('borrowed_date', Carbon::today())->count(),
            'returned_today' => Transaction::whereDate('returned_date', Carbon::today())->count(),
        ];
    }

    /**
     * Calculate availability percentage.
     *
     * Returns the percentage of copies currently available
     * out of the total copies in the library.
     *
     * @return float Availability percentage (0-100)
     */
    protected function calculateAvailabilityPercentage(): float
    {
        $totalCopies = Book::sum('copies_total');
        $availableCopies = Book::sum('copies_available');

        if ($totalCopies <= 0) {
            return 0;
        }

        return round(($availableCopies / $totalCopies) * 100, 1);
    }

    /**
     * Get overdue alerts.
     *
     * Returns a collection of overdue transactions
     * for display in the alerts section.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    #[Computed]
    public function overdueAlerts()
    {
        return Transaction::with(['student', 'book'])
            ->where(function ($query) {
                $query->where('status', 'overdue')
                    ->orWhere(function ($q) {
                        $q->where('status', 'borrowed')
                          ->where('due_date', '<', Carbon::today());
                    });
            })
            ->orderBy('due_date', 'asc')
            ->limit(5)
            ->get()
            ->map(function ($transaction) {
                $transaction->days_overdue = Carbon::today()->diffInDays($transaction->due_date);
                return $transaction;
            });
    }

    /**
     * Get recent transactions.
     *
     * Returns the last 5 transactions for quick view.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    #[Computed]
    public function recentTransactions()
    {
        return Transaction::with(['student', 'book'])
            ->latest('created_at')
            ->limit(5)
            ->get();
    }

    /**
     * Get low stock books.
     *
     * Returns books with less than 2 copies available.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    #[Computed]
    public function lowStockBooks()
    {
        return Book::with('category')
            ->where('copies_available', '<', 2)
            ->where('status', 'available')
            ->orderBy('copies_available', 'asc')
            ->limit(5)
            ->get();
    }

    /**
     * Get books by category for pie chart.
     *
     * @return array Category names and book counts
     */
    #[Computed]
    public function booksByCategory(): array
    {
        $categories = Category::withCount('books')
            ->orderBy('books_count', 'desc')
            ->limit(6)
            ->get();

        return [
            'labels' => $categories->pluck('name')->toArray(),
            'data' => $categories->pluck('books_count')->toArray(),
        ];
    }

    /**
     * Get weekly borrowing trend for line chart.
     *
     * @return array Daily borrowing/return counts for the last 7 days
     */
    #[Computed]
    public function weeklyTrend(): array
    {
        $labels = [];
        $borrowedData = [];
        $returnedData = [];

        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);
            $labels[] = $date->format('D');
            $borrowedData[] = Transaction::whereDate('borrowed_date', $date)->count();
            $returnedData[] = Transaction::whereDate('returned_date', $date)->count();
        }

        return [
            'labels' => $labels,
            'borrowed' => $borrowedData,
            'returned' => $returnedData,
        ];
    }

    /**
     * Get top borrowed books this month for bar chart.
     *
     * @return array Book titles and borrow counts
     */
    #[Computed]
    public function topBorrowedBooks(): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $topBooks = Transaction::whereBetween('borrowed_date', [$startOfMonth, $endOfMonth])
            ->select('book_id', DB::raw('COUNT(*) as borrow_count'))
            ->groupBy('book_id')
            ->orderBy('borrow_count', 'desc')
            ->limit(5)
            ->with('book')
            ->get();

        $labels = [];
        $data = [];

        foreach ($topBooks as $item) {
            if ($item->book) {
                $title = strlen($item->book->title) > 15
                    ? substr($item->book->title, 0, 15) . '...'
                    : $item->book->title;
                $labels[] = $title;
                $data[] = $item->borrow_count;
            }
        }

        return [
            'labels' => $labels,
            'data' => $data,
        ];
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.dashboard-stats');
    }
}
