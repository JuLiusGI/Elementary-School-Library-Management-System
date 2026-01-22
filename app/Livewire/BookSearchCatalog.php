<?php

/**
 * BookSearchCatalog Livewire Component
 *
 * This Livewire component provides a real-time searchable, filterable
 * book catalog with grid and list view options.
 *
 * Features:
 * - Real-time search by title, author, ISBN, accession number
 * - Filter by category, status, and condition
 * - Toggle between grid and list views
 * - Color-coded availability indicators
 * - Pagination
 *
 * @see resources/views/livewire/book-search-catalog.blade.php
 * @see App\Models\Book
 */

namespace App\Livewire;

use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Url;
use App\Models\Book;
use App\Models\Category;

class BookSearchCatalog extends Component
{
    use WithPagination;

    // =========================================================================
    // COMPONENT PROPERTIES
    // =========================================================================

    /**
     * Search term for filtering books.
     *
     * @var string
     */
    #[Url(history: true)]
    public string $search = '';

    /**
     * Filter by category ID.
     *
     * @var string
     */
    #[Url(history: true)]
    public string $categoryId = '';

    /**
     * Filter by status (available/unavailable).
     *
     * @var string
     */
    #[Url(history: true)]
    public string $status = '';

    /**
     * Filter by condition (excellent/good/fair/poor).
     *
     * @var string
     */
    #[Url(history: true)]
    public string $condition = '';

    /**
     * View mode: 'grid' or 'list'.
     *
     * @var string
     */
    #[Url(history: true)]
    public string $viewMode = 'grid';

    /**
     * Column to sort by.
     *
     * @var string
     */
    #[Url(history: true)]
    public string $sortField = 'title';

    /**
     * Sort direction.
     *
     * @var string
     */
    #[Url(history: true)]
    public string $sortDirection = 'asc';

    /**
     * Number of records per page.
     *
     * @var int
     */
    public int $perPage = 12;

    // =========================================================================
    // LIFECYCLE HOOKS
    // =========================================================================

    /**
     * Reset pagination when filters change.
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    public function updatedCategoryId(): void
    {
        $this->resetPage();
    }

    public function updatedStatus(): void
    {
        $this->resetPage();
    }

    public function updatedCondition(): void
    {
        $this->resetPage();
    }

    // =========================================================================
    // PUBLIC METHODS
    // =========================================================================

    /**
     * Set the view mode (grid or list).
     *
     * @param string $mode
     * @return void
     */
    public function setViewMode(string $mode): void
    {
        $this->viewMode = in_array($mode, ['grid', 'list']) ? $mode : 'grid';
    }

    /**
     * Sort the catalog by a field.
     *
     * @param string $field
     * @return void
     */
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'asc';
        }
    }

    /**
     * Clear all filters.
     *
     * @return void
     */
    public function clearFilters(): void
    {
        $this->search = '';
        $this->categoryId = '';
        $this->status = '';
        $this->condition = '';
        $this->resetPage();
    }

    /**
     * Delete a book.
     *
     * @param int $bookId
     * @return void
     */
    public function deleteBook(int $bookId): void
    {
        $book = Book::find($bookId);

        if (!$book) {
            session()->flash('error', 'Book not found.');
            return;
        }

        // Check for active transactions
        $activeTransactions = $book->transactions()
            ->whereIn('status', ['borrowed', 'overdue'])
            ->count();

        if ($activeTransactions > 0) {
            session()->flash('error', "Cannot delete '{$book->title}'. This book has unreturned copies.");
            return;
        }

        $bookTitle = $book->title;

        // Delete cover image if exists
        if ($book->cover_image) {
            \Storage::disk('public')->delete($book->cover_image);
        }

        $book->delete();

        session()->flash('success', "Book '{$bookTitle}' has been removed from the catalog.");
    }

    /**
     * Get the availability status class for a book.
     *
     * @param Book $book
     * @return string CSS classes for the availability badge
     */
    public function getAvailabilityClass(Book $book): string
    {
        if ($book->status === 'unavailable') {
            return 'bg-gray-100 text-gray-800'; // Unavailable (lost/withdrawn)
        }

        if ($book->copies_available === 0) {
            return 'bg-danger-100 text-danger-800'; // No copies available
        }

        if ($book->copies_available <= 2) {
            return 'bg-warning-100 text-warning-800'; // Few copies left
        }

        return 'bg-success-100 text-success-800'; // Available
    }

    /**
     * Get the availability text for a book.
     *
     * @param Book $book
     * @return string
     */
    public function getAvailabilityText(Book $book): string
    {
        if ($book->status === 'unavailable') {
            return 'Unavailable';
        }

        if ($book->copies_available === 0) {
            return 'All borrowed';
        }

        return "{$book->copies_available} of {$book->copies_total} available";
    }

    /**
     * Get categories for filter dropdown.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getCategoriesProperty()
    {
        return Category::orderBy('name')->get();
    }

    // =========================================================================
    // RENDER METHOD
    // =========================================================================

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        // Build query
        $query = Book::with('category');

        // Apply search filter
        if ($this->search) {
            $query->search($this->search);
        }

        // Apply category filter
        if ($this->categoryId) {
            $query->where('category_id', $this->categoryId);
        }

        // Apply status filter
        if ($this->status) {
            $query->where('status', $this->status);
        }

        // Apply condition filter
        if ($this->condition) {
            $query->where('condition', $this->condition);
        }

        // Apply sorting
        $allowedSortFields = ['title', 'author', 'accession_number', 'copies_available', 'created_at'];
        $sortField = in_array($this->sortField, $allowedSortFields) ? $this->sortField : 'title';
        $query->orderBy($sortField, $this->sortDirection);

        // Get paginated results
        $books = $query->paginate($this->perPage);

        return view('livewire.book-search-catalog', [
            'books' => $books,
            'categories' => $this->categories,
        ]);
    }
}
