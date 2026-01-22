<?php

/**
 * Category Model
 *
 * This model represents book categories/genres used to organize
 * the library's book collection. Each book belongs to exactly one category.
 *
 * Common Categories for Elementary School Library:
 * - Fiction: Storybooks, novels, fairy tales
 * - Non-Fiction: Informational books, biographies
 * - Reference: Dictionaries, encyclopedias, atlases
 * - Filipino: Tagalog/Filipino literature
 * - Science: Science books, nature, experiments
 * - Math: Mathematics books, puzzles
 * - History: Historical books, social studies
 * - Arts: Art, music, crafts
 *
 * Categories help students and librarians:
 * - Find books by topic/genre
 * - Organize shelves in the library
 * - Generate category-based reports
 *
 * @see database/migrations/2026_01_22_004924_create_categories_table.php
 * @see docs/TECHNICAL_SPEC.md - Section 3.1 (categories table)
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    /**
     * Use HasFactory trait to enable model factories for testing.
     *
     * Factories allow you to create fake category data for testing:
     * Category::factory()->create(['name' => 'Fiction']);
     */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * Only 'name' and 'description' can be mass assigned.
     * The 'id' and timestamps are managed by Laravel automatically.
     *
     * Usage:
     * Category::create(['name' => 'Fiction', 'description' => 'Story books']);
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',         // Category name (e.g., "Fiction", "Science")
        'description',  // Optional description of the category
    ];

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Get all books in this category.
     *
     * A category can have many books. This relationship allows you to
     * easily get all books belonging to a specific category.
     *
     * Usage:
     * $category->books;                          // All books in category
     * $category->books()->count();               // Count books in category
     * $category->books()->available()->get();    // Only available books
     *
     * Example in controller:
     * $fiction = Category::where('name', 'Fiction')->first();
     * $fictionBooks = $fiction->books;
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function books(): HasMany
    {
        return $this->hasMany(Book::class);
    }

    // =========================================================================
    // HELPER METHODS
    // =========================================================================

    /**
     * Get the count of books in this category.
     *
     * Returns the total number of book titles (not copies) in this category.
     *
     * Usage:
     * $count = $category->bookCount();
     * echo "This category has {$count} books.";
     *
     * @return int Number of books in this category
     */
    public function bookCount(): int
    {
        return $this->books()->count();
    }

    /**
     * Get the count of available books in this category.
     *
     * Returns only books that are currently available for borrowing.
     *
     * Usage:
     * $available = $category->availableBookCount();
     *
     * @return int Number of available books in this category
     */
    public function availableBookCount(): int
    {
        return $this->books()
            ->where('status', 'available')
            ->where('copies_available', '>', 0)
            ->count();
    }

    /**
     * Check if this category has any books.
     *
     * Useful for checking before deletion - we might want to prevent
     * deleting categories that contain books.
     *
     * Usage:
     * if ($category->hasBooks()) {
     *     // Warn user or prevent deletion
     * }
     *
     * @return bool True if category has books, false otherwise
     */
    public function hasBooks(): bool
    {
        return $this->books()->exists();
    }
}
