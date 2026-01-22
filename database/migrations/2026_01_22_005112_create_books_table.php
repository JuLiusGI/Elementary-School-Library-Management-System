<?php

/**
 * Migration: Create Books Table
 *
 * This migration creates the 'books' table which stores the library's
 * book catalog/inventory. Each book record represents a title in the library.
 *
 * Key Features:
 * - Unique accession number for each book title
 * - Tracks total copies and available copies separately
 * - Links to categories for organization
 * - Tracks book condition (excellent, good, fair, poor)
 * - Supports cover image uploads
 *
 * Copy Tracking:
 * - copies_total: Total number of copies the library owns
 * - copies_available: Currently available for borrowing
 * - When a book is borrowed: copies_available decreases by 1
 * - When a book is returned: copies_available increases by 1
 *
 * @see App\Models\Book
 * @see docs/TECHNICAL_SPEC.md - Section 3.1 (books table)
 */

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * Creates the books table with all catalog fields, foreign keys, and indexes.
     *
     * @return void
     */
    public function up(): void
    {
        Schema::create('books', function (Blueprint $table) {
            // Primary key - auto-incrementing unique identifier
            $table->id();

            // ===== BOOK IDENTIFICATION =====

            // Library's unique identifier for this book
            // Format varies by library (e.g., "2024-0001", "FIC-001")
            // Used for quick lookup and labeling books
            $table->string('accession_number', 50)
                  ->unique()
                  ->comment('Library unique identifier for this book');

            // International Standard Book Number (optional)
            // 13-digit ISBN for published books
            // May be null for locally published or donated books
            $table->string('isbn', 13)
                  ->nullable()
                  ->comment('13-digit ISBN (optional for local books)');

            // ===== BIBLIOGRAPHIC INFORMATION =====

            // Book title (required)
            $table->string('title', 255)
                  ->comment('Full title of the book');

            // Author name(s) (required)
            // For multiple authors, store as comma-separated
            $table->string('author', 255)
                  ->comment('Book author(s)');

            // Publisher name (optional)
            $table->string('publisher', 255)
                  ->nullable()
                  ->comment('Publishing company name');

            // Year the book was published (optional)
            // Using year type for 4-digit year
            $table->year('publication_year')
                  ->nullable()
                  ->comment('Year of publication (YYYY format)');

            // ===== CATEGORIZATION =====

            // Foreign key to categories table
            // Links this book to a category for organization
            // Uses unsignedBigInteger to match the id type in categories
            $table->foreignId('category_id')
                  ->constrained('categories')
                  ->onDelete('cascade')
                  ->comment('Category this book belongs to');

            // ===== PHYSICAL DETAILS =====

            // Edition of the book (e.g., "1st Edition", "Revised")
            $table->string('edition', 50)
                  ->nullable()
                  ->comment('Book edition (e.g., 1st Edition)');

            // Number of pages in the book
            $table->unsignedInteger('pages')
                  ->nullable()
                  ->comment('Total number of pages');

            // ===== INVENTORY TRACKING =====

            // Total copies the library owns
            // This number only changes when library acquires or discards copies
            $table->unsignedInteger('copies_total')
                  ->default(1)
                  ->comment('Total copies owned by library');

            // Currently available copies for borrowing
            // Decreases when borrowed, increases when returned
            // Should never exceed copies_total
            $table->unsignedInteger('copies_available')
                  ->default(1)
                  ->comment('Copies currently available for borrowing');

            // Physical location in the library
            // Example: "Shelf A-3", "Reference Section", "Reading Corner"
            $table->string('location', 100)
                  ->nullable()
                  ->comment('Physical location in library (shelf/section)');

            // Physical condition of the book
            // Used to track book deterioration over time
            $table->enum('condition', ['excellent', 'good', 'fair', 'poor'])
                  ->default('good')
                  ->comment('Physical condition: excellent, good, fair, or poor');

            // ===== ADDITIONAL INFORMATION =====

            // Book description or summary (optional)
            // Can include synopsis, subject matter, reading level
            $table->text('description')
                  ->nullable()
                  ->comment('Book description or summary');

            // Path to cover image file (optional)
            // Stored in storage/app/public/book-covers/
            $table->string('cover_image', 255)
                  ->nullable()
                  ->comment('Path to cover image file');

            // ===== STATUS =====

            // Overall availability status
            // 'available': Can be borrowed (if copies_available > 0)
            // 'unavailable': Cannot be borrowed (lost, damaged, withdrawn)
            $table->enum('status', ['available', 'unavailable'])
                  ->default('available')
                  ->comment('Overall status: available or unavailable');

            // ===== TIMESTAMPS =====

            // created_at: When the book was added to catalog
            // updated_at: When the book record was last modified
            $table->timestamps();

            // ===== INDEXES FOR PERFORMANCE =====

            // Index on accession_number for quick lookup (already unique, so indexed)

            // Index on isbn for searching by ISBN
            $table->index('isbn', 'books_isbn_index');

            // Index on category_id for filtering by category
            $table->index('category_id', 'books_category_index');

            // Index on status for filtering available books
            $table->index('status', 'books_status_index');

            // Composite index for common search: accession, isbn, category, status
            $table->index(['accession_number', 'isbn', 'category_id', 'status'], 'books_search_index');
        });
    }

    /**
     * Reverse the migrations.
     *
     * Drops the books table.
     * WARNING: This will fail if transactions table exists with foreign key!
     * Drop transactions table first.
     *
     * @return void
     */
    public function down(): void
    {
        Schema::dropIfExists('books');
    }
};
