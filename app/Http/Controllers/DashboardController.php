<?php

/**
 * DashboardController
 *
 * This controller handles the main dashboard page of the Library Management System.
 * It gathers statistics, recent transactions, alerts, and other data needed
 * for the dashboard overview.
 *
 * The dashboard provides librarians and admins with:
 * - Quick statistics (total books, students, borrowed, overdue)
 * - Recent transaction activity
 * - Overdue book alerts
 * - Low stock book warnings
 * - Quick action buttons for common tasks
 * - Charts for visual data representation
 *
 * @package App\Http\Controllers
 * @see resources/views/dashboard/index.blade.php
 * @see App\Services\ReportService
 */

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\Category;
use App\Services\ReportService;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    /**
     * The report service instance.
     *
     * @var ReportService
     */
    protected ReportService $reportService;

    /**
     * Create a new controller instance.
     *
     * Inject the ReportService for generating statistics.
     *
     * @param ReportService $reportService
     */
    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    /**
     * Display the main dashboard.
     *
     * This method gathers all the data needed for the dashboard view:
     * - Statistics cards (total books, available, students, borrowed)
     * - Recent transactions (last 10)
     * - Overdue book alerts
     * - Low stock book warnings (copies_available < 2)
     * - Charts data (books by category, weekly trend, top borrowed)
     *
     * @param Request $request
     * @return \Illuminate\View\View
     */
    public function index(Request $request)
    {
        // =====================================================================
        // STATISTICS CARDS
        // =====================================================================

        // Get the basic dashboard statistics
        $statistics = $this->getStatistics();

        // =====================================================================
        // RECENT TRANSACTIONS
        // =====================================================================

        // Get the last 10 transactions (both borrowed and returned)
        $recentTransactions = $this->getRecentTransactions(10);

        // =====================================================================
        // ALERTS
        // =====================================================================

        // Get overdue books for alert display
        $overdueBooks = $this->getOverdueBooks();

        // Get low stock books (copies_available < 2)
        $lowStockBooks = $this->getLowStockBooks();

        // =====================================================================
        // CHARTS DATA
        // =====================================================================

        // Books by category for pie chart
        $booksByCategory = $this->getBooksByCategory();

        // Weekly borrowing trend for line chart
        $weeklyTrend = $this->getWeeklyBorrowingTrend();

        // Top 5 most borrowed books this month for bar chart
        $topBorrowedBooks = $this->getTopBorrowedBooksThisMonth(5);

        // =====================================================================
        // RETURN VIEW
        // =====================================================================

        return view('dashboard.index', compact(
            'statistics',
            'recentTransactions',
            'overdueBooks',
            'lowStockBooks',
            'booksByCategory',
            'weeklyTrend',
            'topBorrowedBooks'
        ));
    }

    /**
     * Get dashboard statistics.
     *
     * Returns an array of statistics for the dashboard cards:
     * - Total books (titles)
     * - Total copies in the library
     * - Available copies for borrowing
     * - Availability percentage
     * - Active students count
     * - Currently borrowed books count
     * - Overdue books count
     * - Books borrowed today
     * - Books returned today
     * - Total unpaid fines
     *
     * @return array Statistics data
     */
    protected function getStatistics(): array
    {
        // Count total book titles in the library
        $totalBooks = Book::count();

        // Sum of all copies owned by the library
        $totalCopies = Book::sum('copies_total');

        // Sum of copies currently available for borrowing
        $availableCopies = Book::sum('copies_available');

        // Calculate availability percentage
        $availabilityPercentage = $totalCopies > 0
            ? round(($availableCopies / $totalCopies) * 100, 1)
            : 0;

        // Count active students who can borrow books
        $activeStudents = Student::where('status', 'active')->count();

        // Count currently borrowed books (not yet returned)
        $currentlyBorrowed = Transaction::whereIn('status', ['borrowed', 'overdue'])->count();

        // Count overdue books (past due date and not returned)
        $overdueCount = Transaction::where('status', 'overdue')->count();

        // Count books borrowed today
        $borrowedToday = Transaction::whereDate('borrowed_date', Carbon::today())->count();

        // Count books returned today
        $returnedToday = Transaction::whereDate('returned_date', Carbon::today())->count();

        // Sum of all unpaid fines
        $totalUnpaidFines = Transaction::where('fine_amount', '>', 0)
            ->where('fine_paid', false)
            ->sum('fine_amount');

        return [
            'total_books' => $totalBooks,
            'total_copies' => $totalCopies,
            'available_copies' => $availableCopies,
            'availability_percentage' => $availabilityPercentage,
            'active_students' => $activeStudents,
            'currently_borrowed' => $currentlyBorrowed,
            'overdue_count' => $overdueCount,
            'borrowed_today' => $borrowedToday,
            'returned_today' => $returnedToday,
            'total_unpaid_fines' => $totalUnpaidFines,
        ];
    }

    /**
     * Get recent transactions.
     *
     * Retrieves the most recent borrowing and return transactions
     * with related student, book, and librarian information.
     *
     * @param int $limit Number of transactions to retrieve
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getRecentTransactions(int $limit = 10)
    {
        return Transaction::with(['student', 'book', 'librarian'])
            ->latest('created_at')
            ->limit($limit)
            ->get();
    }

    /**
     * Get overdue books.
     *
     * Retrieves all transactions where books are currently overdue.
     * Includes student and book information for follow-up.
     * Orders by most overdue first (oldest due date).
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getOverdueBooks()
    {
        return Transaction::with(['student', 'book'])
            ->where(function ($query) {
                // Books marked as overdue
                $query->where('status', 'overdue')
                    // Also include borrowed books past due date
                    ->orWhere(function ($q) {
                        $q->where('status', 'borrowed')
                          ->where('due_date', '<', Carbon::today());
                    });
            })
            ->orderBy('due_date', 'asc')
            ->get();
    }

    /**
     * Get low stock books.
     *
     * Retrieves books where copies_available is less than 2.
     * These books may need attention - either reordering or
     * following up on borrowed copies.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    protected function getLowStockBooks()
    {
        return Book::with('category')
            ->where('copies_available', '<', 2)
            ->where('status', 'available')
            ->orderBy('copies_available', 'asc')
            ->limit(10)
            ->get();
    }

    /**
     * Get books by category for pie chart.
     *
     * Returns book counts grouped by category.
     * Used to display a pie chart showing the distribution
     * of books across different categories.
     *
     * @return array Category names and book counts
     */
    protected function getBooksByCategory(): array
    {
        $categories = Category::withCount('books')
            ->orderBy('books_count', 'desc')
            ->get();

        return [
            'labels' => $categories->pluck('name')->toArray(),
            'data' => $categories->pluck('books_count')->toArray(),
        ];
    }

    /**
     * Get weekly borrowing trend.
     *
     * Returns daily borrowing counts for the last 7 days.
     * Used to display a line chart showing borrowing activity.
     *
     * @return array Dates and borrow counts for the week
     */
    protected function getWeeklyBorrowingTrend(): array
    {
        $labels = [];
        $borrowedData = [];
        $returnedData = [];

        // Get data for the last 7 days
        for ($i = 6; $i >= 0; $i--) {
            $date = Carbon::today()->subDays($i);

            // Format date for display (e.g., "Mon", "Tue")
            $labels[] = $date->format('D');

            // Count borrowed on this date
            $borrowedData[] = Transaction::whereDate('borrowed_date', $date)->count();

            // Count returned on this date
            $returnedData[] = Transaction::whereDate('returned_date', $date)->count();
        }

        return [
            'labels' => $labels,
            'borrowed' => $borrowedData,
            'returned' => $returnedData,
        ];
    }

    /**
     * Get top borrowed books this month.
     *
     * Returns the most frequently borrowed books for the current month.
     * Used to display a bar chart of popular books.
     *
     * @param int $limit Number of books to retrieve
     * @return array Book titles and borrow counts
     */
    protected function getTopBorrowedBooksThisMonth(int $limit = 5): array
    {
        $startOfMonth = Carbon::now()->startOfMonth();
        $endOfMonth = Carbon::now()->endOfMonth();

        $topBooks = Transaction::whereBetween('borrowed_date', [$startOfMonth, $endOfMonth])
            ->select('book_id', DB::raw('COUNT(*) as borrow_count'))
            ->groupBy('book_id')
            ->orderBy('borrow_count', 'desc')
            ->limit($limit)
            ->with('book')
            ->get();

        // Prepare data for chart
        $labels = [];
        $data = [];

        foreach ($topBooks as $item) {
            if ($item->book) {
                // Truncate long titles
                $title = strlen($item->book->title) > 20
                    ? substr($item->book->title, 0, 20) . '...'
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
}
