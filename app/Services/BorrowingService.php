<?php

/**
 * BorrowingService.php
 *
 * This service handles all business logic related to book borrowing and returning.
 * It encapsulates the rules for:
 * - Checking if a student can borrow books
 * - Processing book borrowing transactions
 * - Processing book returns
 * - Updating book availability
 *
 * By keeping this logic in a service class (instead of controllers),
 * we follow the "thin controller, fat model/service" principle,
 * making the code more maintainable and testable.
 *
 * @package App\Services
 * @author  Library Management System
 * @version 1.0
 */

namespace App\Services;

use App\Models\Student;
use App\Models\Book;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Setting;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

/**
 * Class BorrowingService
 *
 * Handles all book borrowing and returning operations.
 * This service enforces the library's borrowing rules:
 * - Maximum books per student (default: 3)
 * - Borrowing period (default: 7 days)
 * - Restrictions for students with overdue books or unpaid fines
 */
class BorrowingService
{
    /**
     * Check if a student is eligible to borrow books
     *
     * A student can borrow books only if:
     * 1. They have borrowed less than the maximum allowed books
     * 2. They have no overdue books
     * 3. They have no unpaid fines
     *
     * @param Student $student The student to check eligibility for
     * @return array Contains 'eligible' (bool) and 'reason' (string if not eligible)
     *
     * @example
     * $result = $borrowingService->canBorrow($student);
     * if ($result['eligible']) {
     *     // Student can borrow
     * } else {
     *     // Show $result['reason'] to user
     * }
     */
    public function canBorrow(Student $student): array
    {
        // Get the maximum books allowed per student from settings
        // Default is 3 if not configured
        $maxBooks = Setting::getValue('max_books_per_student', 3);

        // Count how many books the student currently has borrowed
        $currentBorrowed = $student->transactions()
            ->whereIn('status', ['borrowed', 'overdue'])
            ->count();

        // Check 1: Has the student reached their borrowing limit?
        if ($currentBorrowed >= $maxBooks) {
            return [
                'eligible' => false,
                'reason' => "Student has reached the maximum limit of {$maxBooks} borrowed books."
            ];
        }

        // Check 2: Does the student have any overdue books?
        $hasOverdue = $student->transactions()
            ->where('status', 'overdue')
            ->exists();

        if ($hasOverdue) {
            return [
                'eligible' => false,
                'reason' => 'Student has overdue books that must be returned first.'
            ];
        }

        // Check 3: Does the student have any unpaid fines?
        $hasUnpaidFines = $student->transactions()
            ->where('fine_amount', '>', 0)
            ->where('fine_paid', false)
            ->exists();

        if ($hasUnpaidFines) {
            return [
                'eligible' => false,
                'reason' => 'Student has unpaid fines that must be settled first.'
            ];
        }

        // All checks passed - student can borrow
        return [
            'eligible' => true,
            'reason' => null
        ];
    }

    /**
     * Process a book borrowing transaction
     *
     * This method:
     * 1. Validates that the student can borrow
     * 2. Validates that the book is available
     * 3. Creates a transaction record
     * 4. Updates the book's available copies
     *
     * Uses database transaction to ensure data consistency -
     * if any step fails, all changes are rolled back.
     *
     * @param Student $student The student borrowing the book
     * @param Book $book The book being borrowed
     * @param User $librarian The librarian processing the transaction
     * @param Carbon|null $dueDate Optional custom due date (defaults to borrowing period from settings)
     * @return Transaction The created transaction record
     * @throws Exception If borrowing is not allowed or book is unavailable
     *
     * @example
     * try {
     *     $transaction = $borrowingService->borrowBook($student, $book, $librarian);
     *     // Success - show receipt
     * } catch (Exception $e) {
     *     // Show error message: $e->getMessage()
     * }
     */
    public function borrowBook(Student $student, Book $book, User $librarian, ?Carbon $dueDate = null): Transaction
    {
        // Step 1: Check if student can borrow
        $eligibility = $this->canBorrow($student);
        if (!$eligibility['eligible']) {
            throw new Exception($eligibility['reason']);
        }

        // Step 2: Check if book is available
        if ($book->copies_available <= 0) {
            throw new Exception('This book is not available for borrowing. All copies are currently borrowed.');
        }

        // Step 3: Calculate due date if not provided
        if (!$dueDate) {
            // Get borrowing period from settings (default: 7 days)
            $borrowingPeriod = Setting::getValue('borrowing_period', 7);
            $dueDate = Carbon::now()->addDays($borrowingPeriod);
        }

        // Step 4: Use database transaction for data integrity
        // If anything fails, all changes will be rolled back
        return DB::transaction(function () use ($student, $book, $librarian, $dueDate) {
            // Create the borrowing transaction record
            $transaction = Transaction::create([
                'student_id' => $student->id,
                'book_id' => $book->id,
                'librarian_id' => $librarian->id,
                'borrowed_date' => Carbon::now(),
                'due_date' => $dueDate,
                'status' => 'borrowed',
                'fine_amount' => 0,
                'fine_paid' => false,
            ]);

            // Decrease the available copies count
            $book->decrement('copies_available');

            return $transaction;
        });
    }

    /**
     * Process a book return
     *
     * This method:
     * 1. Marks the transaction as returned
     * 2. Records the return date
     * 3. Calculates any fines for overdue books
     * 4. Updates the book's available copies
     *
     * @param Transaction $transaction The borrowing transaction to process return for
     * @param string|null $condition The condition of the book upon return (optional)
     * @param string|null $notes Any notes about the return (optional)
     * @return Transaction The updated transaction record
     * @throws Exception If the book has already been returned
     *
     * @example
     * try {
     *     $transaction = $borrowingService->returnBook($transaction, 'good', 'Book in good condition');
     *     // Success - show fine if any
     * } catch (Exception $e) {
     *     // Show error message
     * }
     */
    public function returnBook(Transaction $transaction, ?string $condition = null, ?string $notes = null): Transaction
    {
        // Check if book has already been returned
        if ($transaction->status === 'returned') {
            throw new Exception('This book has already been returned.');
        }

        // Use database transaction for data integrity
        return DB::transaction(function () use ($transaction, $condition, $notes) {
            // Calculate fine if the book is overdue
            $fineAmount = 0;
            if ($transaction->status === 'overdue' || Carbon::parse($transaction->due_date)->isPast()) {
                $fineCalculator = new FineCalculationService();
                $fineAmount = $fineCalculator->calculateFine($transaction);
            }

            // Update the transaction record
            $transaction->update([
                'returned_date' => Carbon::now(),
                'status' => 'returned',
                'fine_amount' => $fineAmount,
                'notes' => $notes,
            ]);

            // Increase the available copies count
            $transaction->book->increment('copies_available');

            // Update book condition if provided
            if ($condition) {
                $transaction->book->update(['condition' => $condition]);
            }

            return $transaction->fresh();
        });
    }

    /**
     * Get a student's current borrowed books
     *
     * Returns all books that the student currently has borrowed
     * (status is either 'borrowed' or 'overdue')
     *
     * @param Student $student The student to get borrowed books for
     * @return \Illuminate\Database\Eloquent\Collection Collection of Transaction models
     */
    public function getCurrentBorrowedBooks(Student $student)
    {
        return $student->transactions()
            ->with('book') // Eager load book data to avoid N+1 queries
            ->whereIn('status', ['borrowed', 'overdue'])
            ->orderBy('due_date', 'asc') // Show books due soonest first
            ->get();
    }

    /**
     * Get the remaining borrowing capacity for a student
     *
     * Calculates how many more books a student can borrow.
     *
     * @param Student $student The student to check
     * @return int Number of additional books the student can borrow
     */
    public function getRemainingBorrowingCapacity(Student $student): int
    {
        $maxBooks = Setting::getValue('max_books_per_student', 3);
        $currentBorrowed = $student->transactions()
            ->whereIn('status', ['borrowed', 'overdue'])
            ->count();

        return max(0, $maxBooks - $currentBorrowed);
    }
}
