<?php

/**
 * ReportService.php
 *
 * This service handles all report generation for the library system.
 * It provides methods to generate various reports including:
 * - Daily transaction reports
 * - Overdue books reports
 * - Student borrowing history
 * - Most borrowed books
 * - Inventory reports
 * - Fine reports
 *
 * Reports can be returned as data arrays for display in views,
 * or formatted for PDF/Excel export.
 *
 * @package App\Services
 * @author  Library Management System
 * @version 1.0
 */

namespace App\Services;

use App\Models\Book;
use App\Models\Student;
use App\Models\Transaction;
use App\Models\Category;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

/**
 * Class ReportService
 *
 * Generates various reports for the library management system.
 * All methods return data that can be displayed in views or exported.
 */
class ReportService
{
    /**
     * Get daily transactions report
     *
     * Returns all transactions (borrowed and returned) for a specific date.
     *
     * @param Carbon|null $date The date to get transactions for (defaults to today)
     * @return array Contains 'borrowed' and 'returned' transaction collections
     *
     * @example
     * $report = $reportService->getDailyTransactions(Carbon::today());
     * echo "Borrowed today: " . count($report['borrowed']);
     * echo "Returned today: " . count($report['returned']);
     */
    public function getDailyTransactions(?Carbon $date = null): array
    {
        // Default to today if no date provided
        $date = $date ?? Carbon::today();

        // Get books borrowed on this date
        $borrowed = Transaction::with(['student', 'book', 'librarian'])
            ->whereDate('borrowed_date', $date)
            ->orderBy('created_at', 'desc')
            ->get();

        // Get books returned on this date
        $returned = Transaction::with(['student', 'book', 'librarian'])
            ->whereDate('returned_date', $date)
            ->orderBy('updated_at', 'desc')
            ->get();

        return [
            'date' => $date->format('F j, Y'),
            'borrowed' => $borrowed,
            'borrowed_count' => $borrowed->count(),
            'returned' => $returned,
            'returned_count' => $returned->count(),
        ];
    }

    /**
     * Get overdue books report
     *
     * Returns all transactions where books are currently overdue.
     * Includes student contact information for follow-up.
     *
     * @return \Illuminate\Database\Eloquent\Collection Collection of overdue transactions
     */
    public function getOverdueBooks()
    {
        return Transaction::with(['student', 'book'])
            ->where('status', 'overdue')
            ->orWhere(function ($query) {
                // Also include borrowed books past due date that haven't been marked overdue yet
                $query->where('status', 'borrowed')
                      ->where('due_date', '<', Carbon::today());
            })
            ->orderBy('due_date', 'asc') // Most overdue first
            ->get()
            ->map(function ($transaction) {
                // Calculate days overdue for each transaction
                $fineService = new FineCalculationService();
                $transaction->days_overdue = $fineService->getDaysOverdue($transaction);
                $transaction->calculated_fine = $fineService->calculateFine($transaction);
                return $transaction;
            });
    }

    /**
     * Get student borrowing history
     *
     * Returns complete borrowing history for a specific student.
     *
     * @param Student $student The student to get history for
     * @param int|null $limit Maximum number of records (null for all)
     * @return array Contains student info and transaction history
     */
    public function getStudentBorrowingHistory(Student $student, ?int $limit = null): array
    {
        $query = $student->transactions()
            ->with('book')
            ->orderBy('borrowed_date', 'desc');

        if ($limit) {
            $query->limit($limit);
        }

        $transactions = $query->get();

        // Calculate summary statistics
        $totalBorrowed = $student->transactions()->count();
        $currentlyBorrowed = $student->transactions()->whereIn('status', ['borrowed', 'overdue'])->count();
        $totalFines = $student->transactions()->sum('fine_amount');
        $unpaidFines = $student->transactions()
            ->where('fine_amount', '>', 0)
            ->where('fine_paid', false)
            ->sum('fine_amount');

        return [
            'student' => $student,
            'transactions' => $transactions,
            'summary' => [
                'total_borrowed' => $totalBorrowed,
                'currently_borrowed' => $currentlyBorrowed,
                'total_fines' => $totalFines,
                'unpaid_fines' => $unpaidFines,
            ],
        ];
    }

    /**
     * Get most borrowed books report
     *
     * Returns books ranked by how many times they've been borrowed.
     *
     * @param int $limit Number of books to return (default: 10)
     * @param Carbon|null $startDate Optional start date for date range
     * @param Carbon|null $endDate Optional end date for date range
     * @return \Illuminate\Support\Collection Collection of books with borrow counts
     */
    public function getMostBorrowedBooks(int $limit = 10, ?Carbon $startDate = null, ?Carbon $endDate = null)
    {
        $query = DB::table('transactions')
            ->join('books', 'transactions.book_id', '=', 'books.id')
            ->select('books.*', DB::raw('COUNT(transactions.id) as borrow_count'))
            ->groupBy('books.id');

        // Apply date range filter if provided
        if ($startDate) {
            $query->where('transactions.borrowed_date', '>=', $startDate);
        }
        if ($endDate) {
            $query->where('transactions.borrowed_date', '<=', $endDate);
        }

        return $query->orderBy('borrow_count', 'desc')
            ->limit($limit)
            ->get();
    }

    /**
     * Get books by category report
     *
     * Returns book counts grouped by category.
     *
     * @return \Illuminate\Database\Eloquent\Collection Categories with book counts
     */
    public function getBooksByCategory()
    {
        return Category::withCount('books')
            ->orderBy('books_count', 'desc')
            ->get();
    }

    /**
     * Get students with fines report
     *
     * Returns students who have unpaid fines.
     *
     * @return \Illuminate\Support\Collection Students with fine information
     */
    public function getStudentsWithFines()
    {
        return Student::select('students.*')
            ->join('transactions', 'students.id', '=', 'transactions.student_id')
            ->where('transactions.fine_amount', '>', 0)
            ->where('transactions.fine_paid', false)
            ->groupBy('students.id')
            ->selectRaw('SUM(transactions.fine_amount) as total_fines')
            ->selectRaw('COUNT(transactions.id) as fine_count')
            ->orderBy('total_fines', 'desc')
            ->get();
    }

    /**
     * Get inventory report
     *
     * Returns summary statistics about the library's book inventory.
     *
     * @return array Inventory statistics
     */
    public function getInventoryReport(): array
    {
        // Total number of book titles
        $totalTitles = Book::count();

        // Total number of copies (sum of copies_total)
        $totalCopies = Book::sum('copies_total');

        // Available copies (sum of copies_available)
        $availableCopies = Book::sum('copies_available');

        // Currently borrowed (total - available)
        $borrowedCopies = $totalCopies - $availableCopies;

        // Books by condition
        $byCondition = Book::select('condition', DB::raw('COUNT(*) as count'))
            ->groupBy('condition')
            ->pluck('count', 'condition')
            ->toArray();

        // Books by category
        $byCategory = Category::withCount('books')
            ->orderBy('books_count', 'desc')
            ->get()
            ->pluck('books_count', 'name')
            ->toArray();

        return [
            'total_titles' => $totalTitles,
            'total_copies' => $totalCopies,
            'available_copies' => $availableCopies,
            'borrowed_copies' => $borrowedCopies,
            'utilization_rate' => $totalCopies > 0
                ? round(($borrowedCopies / $totalCopies) * 100, 2)
                : 0,
            'by_condition' => $byCondition,
            'by_category' => $byCategory,
        ];
    }

    /**
     * Get circulation statistics
     *
     * Returns borrowing statistics for a given period.
     *
     * @param Carbon $startDate Start of the period
     * @param Carbon $endDate End of the period
     * @return array Circulation statistics
     */
    public function getCirculationStatistics(Carbon $startDate, Carbon $endDate): array
    {
        // Total transactions in period
        $totalBorrowed = Transaction::whereBetween('borrowed_date', [$startDate, $endDate])->count();
        $totalReturned = Transaction::whereBetween('returned_date', [$startDate, $endDate])->count();

        // Fines collected
        $totalFines = Transaction::whereBetween('returned_date', [$startDate, $endDate])
            ->where('fine_paid', true)
            ->sum('fine_amount');

        // Daily breakdown
        $dailyBreakdown = Transaction::whereBetween('borrowed_date', [$startDate, $endDate])
            ->select(DB::raw('DATE(borrowed_date) as date'), DB::raw('COUNT(*) as count'))
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Unique borrowers
        $uniqueBorrowers = Transaction::whereBetween('borrowed_date', [$startDate, $endDate])
            ->distinct('student_id')
            ->count('student_id');

        return [
            'period' => [
                'start' => $startDate->format('F j, Y'),
                'end' => $endDate->format('F j, Y'),
            ],
            'total_borrowed' => $totalBorrowed,
            'total_returned' => $totalReturned,
            'total_fines_collected' => $totalFines,
            'unique_borrowers' => $uniqueBorrowers,
            'average_daily_borrows' => $startDate->diffInDays($endDate) > 0
                ? round($totalBorrowed / $startDate->diffInDays($endDate), 2)
                : $totalBorrowed,
            'daily_breakdown' => $dailyBreakdown,
        ];
    }

    /**
     * Get dashboard statistics
     *
     * Returns key metrics for the dashboard overview.
     *
     * @return array Dashboard statistics
     */
    public function getDashboardStatistics(): array
    {
        return [
            // Book statistics
            'total_books' => Book::count(),
            'available_books' => Book::where('copies_available', '>', 0)->count(),
            'total_copies' => Book::sum('copies_total'),
            'available_copies' => Book::sum('copies_available'),

            // Student statistics
            'total_students' => Student::where('status', 'active')->count(),
            'students_with_borrowed_books' => Transaction::whereIn('status', ['borrowed', 'overdue'])
                ->distinct('student_id')
                ->count('student_id'),

            // Transaction statistics
            'books_borrowed_today' => Transaction::whereDate('borrowed_date', Carbon::today())->count(),
            'books_returned_today' => Transaction::whereDate('returned_date', Carbon::today())->count(),
            'overdue_books' => Transaction::where('status', 'overdue')->count(),

            // Fine statistics
            'total_unpaid_fines' => Transaction::where('fine_amount', '>', 0)
                ->where('fine_paid', false)
                ->sum('fine_amount'),
        ];
    }
}
