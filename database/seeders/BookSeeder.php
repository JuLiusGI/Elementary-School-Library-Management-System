<?php

/**
 * BookSeeder
 *
 * Seeds sample book data for the library management system using BookFactory.
 * Creates 100 realistic book records appropriate for an elementary school library.
 *
 * Book Distribution:
 * - Distributed across all 11 categories
 * - Mix of available/unavailable status
 * - Various copy quantities (1-5 copies per book)
 * - Different book conditions (excellent, good, fair, poor)
 *
 * Accession Number Format: YEAR-#### (e.g., 2026-0001)
 *
 * @package Database\Seeders
 * @see App\Models\Book
 * @see Database\Factories\BookFactory
 */

namespace Database\Seeders;

use App\Models\Book;
use App\Models\Category;
use Database\Factories\BookFactory;
use Illuminate\Database\Seeder;

class BookSeeder extends Seeder
{
    /**
     * The number of books to create.
     *
     * @var int
     */
    protected int $bookCount = 100;

    /**
     * Run the database seeds.
     *
     * Creates 100 books using the BookFactory.
     * Distributes books across all categories.
     *
     * @return void
     */
    public function run(): void
    {
        // Reset the factory counter to ensure consistent accession numbers
        BookFactory::resetCounter();

        // Get all categories
        $categories = Category::all();

        if ($categories->isEmpty()) {
            $this->command->error('No categories found. Please run CategorySeeder first.');
            return;
        }

        // Calculate books per category (approximately 9 per category for 100 books / 11 categories)
        $booksPerCategory = (int) ceil($this->bookCount / $categories->count());

        $totalCreated = 0;

        foreach ($categories as $category) {
            // Determine how many to create for this category
            $remaining = $this->bookCount - $totalCreated;
            $toCreate = min($booksPerCategory, $remaining);

            if ($toCreate <= 0) {
                break;
            }

            // Create books for this category
            Book::factory()
                ->count($toCreate)
                ->inCategory($category->id)
                ->create();

            $totalCreated += $toCreate;

            if ($totalCreated >= $this->bookCount) {
                break;
            }
        }

        $this->command->info("Sample books seeded successfully! ({$totalCreated} book titles)");

        // Show category distribution
        $this->command->table(
            ['Category', 'Books', 'Total Copies', 'Available'],
            $this->getCategoryDistribution()
        );

        // Show status summary
        $available = Book::where('status', 'available')->count();
        $unavailable = Book::where('status', 'unavailable')->count();
        $this->command->info("Status: {$available} available, {$unavailable} unavailable");
    }

    /**
     * Get the category distribution of created books.
     *
     * @return array<int, array<string, mixed>>
     */
    protected function getCategoryDistribution(): array
    {
        $distribution = [];

        $categories = Category::withCount('books')->get();

        foreach ($categories as $category) {
            $totalCopies = $category->books()->sum('copies_total');
            $availableCopies = $category->books()->sum('copies_available');

            $distribution[] = [
                'Category' => $category->name,
                'Books' => $category->books_count,
                'Total Copies' => $totalCopies,
                'Available' => $availableCopies,
            ];
        }

        return $distribution;
    }
}
