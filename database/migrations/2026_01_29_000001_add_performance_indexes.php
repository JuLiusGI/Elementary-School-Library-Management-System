<?php

/**
 * Add Performance Indexes Migration
 *
 * This migration adds database indexes to improve query performance.
 * Indexes speed up SELECT queries but slightly slow down INSERT/UPDATE operations.
 *
 * Key indexes added:
 * - students: status, grade_level for filtering active students
 * - books: status, category_id, copies_available for availability queries
 * - transactions: status, borrowed_date, due_date, student_id, book_id for reporting
 * - settings: key for quick lookups
 *
 * @see https://laravel.com/docs/migrations#creating-indexes
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Add indexes to improve query performance.
     */
    public function up(): void
    {
        // =====================================================================
        // STUDENTS TABLE INDEXES
        // =====================================================================
        Schema::table('students', function (Blueprint $table) {
            // Index for filtering by status (active, inactive, graduated)
            // Used frequently in: Student::active()->get()
            $table->index('status', 'students_status_index');

            // Index for filtering by grade level
            // Used in: Student::inGrade(3)->get()
            $table->index('grade_level', 'students_grade_level_index');

            // Composite index for common filter combinations
            // Used in: Student::active()->inGrade(3)->get()
            $table->index(['status', 'grade_level'], 'students_status_grade_index');
        });

        // =====================================================================
        // BOOKS TABLE INDEXES
        // =====================================================================
        Schema::table('books', function (Blueprint $table) {
            // Index for filtering by status
            // Used in: Book::available()->get()
            $table->index('status', 'books_status_index');

            // Index for filtering by category
            // Used in: Book::inCategory(1)->get()
            $table->index('category_id', 'books_category_id_index');

            // Index for checking availability
            // Used in: Book::where('copies_available', '>', 0)->get()
            $table->index('copies_available', 'books_copies_available_index');

            // Composite index for availability checks
            // Used in: Book::available() which checks both status and copies_available
            $table->index(['status', 'copies_available'], 'books_availability_index');

            // Index for search by title (helps with LIKE queries)
            // Note: For full-text search, consider using fulltext index
            $table->index('title', 'books_title_index');
        });

        // =====================================================================
        // TRANSACTIONS TABLE INDEXES
        // =====================================================================
        Schema::table('transactions', function (Blueprint $table) {
            // Index for filtering by status
            // Used frequently in: Transaction::borrowed(), Transaction::overdue()
            $table->index('status', 'transactions_status_index');

            // Index for date-based queries
            // Used in: daily reports, finding transactions by date
            $table->index('borrowed_date', 'transactions_borrowed_date_index');
            $table->index('due_date', 'transactions_due_date_index');
            $table->index('returned_date', 'transactions_returned_date_index');

            // Index for student lookups
            // Used in: $student->transactions()
            $table->index('student_id', 'transactions_student_id_index');

            // Index for book lookups
            // Used in: $book->transactions()
            $table->index('book_id', 'transactions_book_id_index');

            // Index for fine queries
            // Used in: Transaction::withUnpaidFines()
            $table->index(['fine_amount', 'fine_paid'], 'transactions_fines_index');

            // Composite index for overdue checks
            // Used in: Finding overdue books
            $table->index(['status', 'due_date'], 'transactions_overdue_index');

            // Composite index for student's active books
            // Used in: $student->currentBorrowedBooks()
            $table->index(['student_id', 'status'], 'transactions_student_status_index');
        });

        // =====================================================================
        // SETTINGS TABLE INDEXES
        // =====================================================================
        Schema::table('settings', function (Blueprint $table) {
            // Index for key lookups (if not already primary or unique)
            // Used in: Setting::get('key_name')
            $table->index('key', 'settings_key_index');
        });

        // =====================================================================
        // CATEGORIES TABLE INDEXES
        // =====================================================================
        Schema::table('categories', function (Blueprint $table) {
            // Index for category name lookups
            // Used in: Category::where('name', 'Fiction')->first()
            $table->index('name', 'categories_name_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Remove all performance indexes.
     */
    public function down(): void
    {
        // Remove students indexes
        Schema::table('students', function (Blueprint $table) {
            $table->dropIndex('students_status_index');
            $table->dropIndex('students_grade_level_index');
            $table->dropIndex('students_status_grade_index');
        });

        // Remove books indexes
        Schema::table('books', function (Blueprint $table) {
            $table->dropIndex('books_status_index');
            $table->dropIndex('books_category_id_index');
            $table->dropIndex('books_copies_available_index');
            $table->dropIndex('books_availability_index');
            $table->dropIndex('books_title_index');
        });

        // Remove transactions indexes
        Schema::table('transactions', function (Blueprint $table) {
            $table->dropIndex('transactions_status_index');
            $table->dropIndex('transactions_borrowed_date_index');
            $table->dropIndex('transactions_due_date_index');
            $table->dropIndex('transactions_returned_date_index');
            $table->dropIndex('transactions_student_id_index');
            $table->dropIndex('transactions_book_id_index');
            $table->dropIndex('transactions_fines_index');
            $table->dropIndex('transactions_overdue_index');
            $table->dropIndex('transactions_student_status_index');
        });

        // Remove settings indexes
        Schema::table('settings', function (Blueprint $table) {
            $table->dropIndex('settings_key_index');
        });

        // Remove categories indexes
        Schema::table('categories', function (Blueprint $table) {
            $table->dropIndex('categories_name_index');
        });
    }
};
