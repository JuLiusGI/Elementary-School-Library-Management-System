<?php

/**
 * Migration: Create Transactions Table
 *
 * This migration creates the 'transactions' table which records all
 * book borrowing and returning activities in the library.
 *
 * Each transaction represents one book being borrowed by one student.
 * The transaction tracks:
 * - Who borrowed (student_id)
 * - What was borrowed (book_id)
 * - Who processed it (librarian_id)
 * - When it was borrowed and when it's due
 * - Current status (borrowed, returned, overdue)
 * - Any fines incurred
 *
 * Transaction Flow:
 * 1. Student borrows book -> status = 'borrowed'
 * 2. If past due_date -> status = 'overdue' (updated by scheduled task)
 * 3. Student returns book -> status = 'returned', fine calculated if overdue
 *
 * @see App\Models\Transaction
 * @see App\Services\BorrowingService
 * @see docs/TECHNICAL_SPEC.md - Section 3.1 (transactions table)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the transactions table with all fields, foreign keys, and indexes.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('transactions', function (Blueprint $table) {
            // Primary key - auto-incrementing unique identifier
            $table->id();

            // ===== FOREIGN KEYS (WHO & WHAT) =====

            // Student who borrowed the book
            // Links to students table
            // Cascade delete: if student is deleted, their transactions are too
            $table->foreignId('student_id')
                  ->constrained('students')
                  ->onDelete('cascade')
                  ->comment('Student who borrowed the book');

            // Book that was borrowed
            // Links to books table
            // Cascade delete: if book is deleted, its transactions are too
            $table->foreignId('book_id')
                  ->constrained('books')
                  ->onDelete('cascade')
                  ->comment('Book that was borrowed');

            // Librarian who processed the transaction
            // Links to users table (librarian or admin)
            // Cascade delete: if user is deleted, transaction records remain
            $table->foreignId('librarian_id')
                  ->constrained('users')
                  ->onDelete('cascade')
                  ->comment('Librarian/admin who processed this transaction');

            // ===== DATES =====

            // Date when the book was borrowed
            $table->date('borrowed_date')
                  ->comment('Date the book was borrowed');

            // Date when the book must be returned
            // Calculated as: borrowed_date + borrowing_period (from settings)
            $table->date('due_date')
                  ->comment('Date the book must be returned by');

            // Date when the book was actually returned
            // NULL if book hasn't been returned yet
            $table->date('returned_date')
                  ->nullable()
                  ->comment('Actual return date (null if not yet returned)');

            // ===== STATUS =====

            // Current status of the transaction
            // 'borrowed': Book is currently with the student
            // 'returned': Book has been returned
            // 'overdue': Book is past due date and not returned
            $table->enum('status', ['borrowed', 'returned', 'overdue'])
                  ->default('borrowed')
                  ->comment('Transaction status: borrowed, returned, or overdue');

            // ===== NOTES =====

            // Optional notes about the transaction
            // Example: "Book returned with minor damage", "Late due to illness"
            $table->text('notes')
                  ->nullable()
                  ->comment('Additional notes about this transaction');

            // ===== FINE TRACKING =====

            // Fine amount in Philippine Pesos
            // Calculated when book is returned late
            // Formula: (days_overdue - grace_period) × fine_per_day
            $table->decimal('fine_amount', 8, 2)
                  ->default(0.00)
                  ->comment('Fine amount in PHP (0 if no fine)');

            // Whether the fine has been paid
            // Only relevant if fine_amount > 0
            $table->boolean('fine_paid')
                  ->default(false)
                  ->comment('True if fine has been paid');

            // ===== TIMESTAMPS =====

            // created_at: When the transaction was created
            // updated_at: When the transaction was last modified
            $table->timestamps();

            // ===== INDEXES FOR PERFORMANCE =====

            // Index on student_id for finding all transactions by a student
            $table->index('student_id', 'transactions_student_index');

            // Index on book_id for finding borrowing history of a book
            $table->index('book_id', 'transactions_book_index');

            // Index on status for filtering (e.g., all overdue transactions)
            $table->index('status', 'transactions_status_index');

            // Index on due_date for finding books due on a specific date
            $table->index('due_date', 'transactions_due_date_index');

            // Composite index for common query: student's active transactions
            // Used when checking if student can borrow more books
            $table->index(['student_id', 'status'], 'transactions_student_status_index');

            // Composite index for finding overdue books
            // Used by scheduled task that updates status to 'overdue'
            $table->index(['status', 'due_date'], 'transactions_overdue_check_index');

            // Full composite index as specified in TECHNICAL_SPEC.md
            $table->index(
                ['student_id', 'book_id', 'status', 'due_date'],
                'transactions_full_search_index'
            );
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the transactions table.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('transactions');
    }
};
