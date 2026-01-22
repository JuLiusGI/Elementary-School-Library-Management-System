<?php

/**
 * Book Model
 *
 * This model represents books in the library catalog. Each record is a book
 * title that may have multiple copies available for borrowing.
 *
 * Key Concepts:
 * - accession_number: Library's unique ID for each book title
 * - copies_total: How many copies the library owns
 * - copies_available: How many copies can be borrowed right now
 *
 * Copy Tracking Example:
 * A book has copies_total=5 and copies_available=3
 * This means: 5 copies owned, 3 available, 2 currently borrowed
 *
 * When a book is borrowed: copies_available decreases by 1
 * When a book is returned: copies_available increases by 1
 *
 * Book Conditions:
 * - excellent: Like new
 * - good: Normal wear
 * - fair: Noticeable wear but usable
 * - poor: Damaged, may need replacement
 *
 * @see database/migrations/2026_01_22_005112_create_books_table.php
 * @see App\Services\BorrowingService
 * @see docs/TECHNICAL_SPEC.md - Section 3.1 (books table)
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;

class Book extends Model
{
    /**
     * Use HasFactory trait to enable model factories for testing.
     *
     * Usage:
     * Book::factory()->create(['title' => 'Test Book']);
     */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * All book fields except 'id' and timestamps are fillable.
     * This allows creating/updating books with arrays of data.
     *
     * Usage:
     * Book::create([
     *     'accession_number' => '2024-0001',
     *     'title' => 'The Little Prince',
     *     'author' => 'Antoine de Saint-Exupéry',
     *     'category_id' => 1,
     * ]);
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'accession_number',   // Library's unique ID (e.g., "2024-0001")
        'isbn',               // International Standard Book Number (optional)
        'title',              // Book title
        'author',             // Author name(s)
        'publisher',          // Publishing company (optional)
        'publication_year',   // Year published (optional)
        'category_id',        // Foreign key to categories table
        'edition',            // Edition (e.g., "1st Edition") (optional)
        'pages',              // Number of pages (optional)
        'copies_total',       // Total copies library owns
        'copies_available',   // Copies currently available
        'location',           // Physical location (e.g., "Shelf A-3")
        'condition',          // Book condition: excellent/good/fair/poor
        'description',        // Book description/summary (optional)
        'cover_image',        // Path to cover image file (optional)
        'status',             // Overall status: available/unavailable
    ];

    /**
     * The attributes that should be cast.
     *
     * Casting ensures consistent data types when accessing attributes.
     *
     * - publication_year as integer for math operations
     * - copies_total and copies_available as integers for counting
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'publication_year' => 'integer',    // Cast year to integer
            'copies_total' => 'integer',        // Cast to integer for math
            'copies_available' => 'integer',    // Cast to integer for math
            'pages' => 'integer',               // Cast to integer
        ];
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Get the category this book belongs to.
     *
     * Each book belongs to exactly one category (e.g., Fiction, Science).
     * This is the inverse of Category::hasMany(Book::class).
     *
     * Usage:
     * $book->category;           // Get the category model
     * $book->category->name;     // Get category name (e.g., "Fiction")
     *
     * In Blade templates:
     * {{ $book->category->name }}
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    /**
     * Get all borrowing transactions for this book.
     *
     * A book can be borrowed many times. Each borrowing creates a transaction.
     * This gives you the complete borrowing history of a book.
     *
     * Usage:
     * $book->transactions;                    // All transactions for this book
     * $book->transactions()->latest()->get(); // Most recent transactions first
     *
     * To see who has this book currently:
     * $book->transactions()->where('status', 'borrowed')->with('student')->get();
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope to filter only available books.
     *
     * A book is considered "available" if:
     * 1. Its status is 'available' (not withdrawn/lost)
     * 2. It has at least one copy available
     *
     * Usage:
     * Book::available()->get();                    // All available books
     * Book::available()->where('category_id', 1)->get(); // Available fiction books
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder
     * @return \Illuminate\Database\Eloquent\Builder Modified query builder
     */
    public function scopeAvailable(Builder $query): Builder
    {
        return $query->where('status', 'available')
                     ->where('copies_available', '>', 0);
    }

    /**
     * Scope to filter unavailable books.
     *
     * Books that cannot be borrowed (no copies available or status unavailable).
     *
     * Usage:
     * Book::unavailable()->get(); // Books that can't be borrowed
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder
     * @return \Illuminate\Database\Eloquent\Builder Modified query builder
     */
    public function scopeUnavailable(Builder $query): Builder
    {
        return $query->where(function ($q) {
            $q->where('status', 'unavailable')
              ->orWhere('copies_available', '<=', 0);
        });
    }

    /**
     * Scope to filter books by category.
     *
     * Usage:
     * Book::inCategory(1)->get();           // Books in category ID 1
     * Book::available()->inCategory(2)->get(); // Available books in category 2
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder
     * @param int $categoryId The category ID to filter by
     * @return \Illuminate\Database\Eloquent\Builder Modified query builder
     */
    public function scopeInCategory(Builder $query, int $categoryId): Builder
    {
        return $query->where('category_id', $categoryId);
    }

    /**
     * Scope to search books by title, author, ISBN, or accession number.
     *
     * Case-insensitive search using LIKE.
     *
     * Usage:
     * Book::search('Harry Potter')->get();    // Search by title
     * Book::search('Rowling')->get();         // Search by author
     * Book::available()->search('science')->get(); // Available books matching "science"
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder
     * @param string $searchTerm The term to search for
     * @return \Illuminate\Database\Eloquent\Builder Modified query builder
     */
    public function scopeSearch(Builder $query, string $searchTerm): Builder
    {
        return $query->where(function ($q) use ($searchTerm) {
            $q->where('title', 'like', "%{$searchTerm}%")
              ->orWhere('author', 'like', "%{$searchTerm}%")
              ->orWhere('isbn', 'like', "%{$searchTerm}%")
              ->orWhere('accession_number', 'like', "%{$searchTerm}%");
        });
    }

    /**
     * Scope to filter books by condition.
     *
     * Usage:
     * Book::inCondition('poor')->get(); // Books needing replacement
     * Book::inCondition('excellent')->get(); // Books in best condition
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder
     * @param string $condition The condition to filter by
     * @return \Illuminate\Database\Eloquent\Builder Modified query builder
     */
    public function scopeInCondition(Builder $query, string $condition): Builder
    {
        return $query->where('condition', $condition);
    }

    // =========================================================================
    // AVAILABILITY METHODS
    // =========================================================================

    /**
     * Check if this book is available for borrowing.
     *
     * A book is available if:
     * 1. Its status is 'available'
     * 2. There's at least one copy available
     *
     * Usage:
     * if ($book->isAvailable()) {
     *     // Allow borrowing
     * } else {
     *     // Show "not available" message
     * }
     *
     * @return bool True if book can be borrowed, false otherwise
     */
    public function isAvailable(): bool
    {
        return $this->status === 'available' && $this->copies_available > 0;
    }

    /**
     * Decrease the available copies by 1 (when book is borrowed).
     *
     * This method is called when a student borrows this book.
     * It decrements copies_available and saves to database.
     *
     * Safety: Won't go below 0 copies available.
     *
     * Usage (in BorrowingService):
     * $book->decrementCopy();
     *
     * @return bool True if decremented successfully, false if no copies available
     */
    public function decrementCopy(): bool
    {
        // Don't go below 0
        if ($this->copies_available <= 0) {
            return false;
        }

        // Decrement and save
        $this->copies_available -= 1;
        $this->save();

        return true;
    }

    /**
     * Increase the available copies by 1 (when book is returned).
     *
     * This method is called when a student returns this book.
     * It increments copies_available and saves to database.
     *
     * Safety: Won't exceed copies_total.
     *
     * Usage (in BorrowingService):
     * $book->incrementCopy();
     *
     * @return bool True if incremented successfully, false if already at max
     */
    public function incrementCopy(): bool
    {
        // Don't exceed total copies
        if ($this->copies_available >= $this->copies_total) {
            return false;
        }

        // Increment and save
        $this->copies_available += 1;
        $this->save();

        return true;
    }

    /**
     * Get the number of copies currently borrowed.
     *
     * Calculated as: copies_total - copies_available
     *
     * Usage:
     * $borrowed = $book->copiesBorrowed();
     * echo "{$borrowed} copies are currently out.";
     *
     * @return int Number of copies currently borrowed
     */
    public function copiesBorrowed(): int
    {
        return $this->copies_total - $this->copies_available;
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * Get the full path to the book's cover image.
     *
     * Returns the URL to the cover image, or a default placeholder
     * if no cover image is set.
     *
     * Cover images are stored in: storage/app/public/book-covers/
     * Accessed via: /storage/book-covers/filename.jpg
     *
     * Usage in Blade:
     * <img src="{{ $book->cover_image_url }}" alt="{{ $book->title }}">
     *
     * @return string URL to cover image or default placeholder
     */
    public function getCoverImageUrlAttribute(): string
    {
        if ($this->cover_image) {
            return asset('storage/' . $this->cover_image);
        }

        // Return a default placeholder image
        // You can replace this with an actual placeholder image path
        return asset('images/book-placeholder.png');
    }

    /**
     * Get a formatted display of the publication info.
     *
     * Combines publisher and year into a readable string.
     * Example: "Penguin Books, 2020" or "2020" or "Penguin Books"
     *
     * Usage:
     * {{ $book->publication_info }}
     *
     * @return string Formatted publication information
     */
    public function getPublicationInfoAttribute(): string
    {
        $parts = [];

        if ($this->publisher) {
            $parts[] = $this->publisher;
        }

        if ($this->publication_year) {
            $parts[] = $this->publication_year;
        }

        return implode(', ', $parts) ?: 'N/A';
    }
}
