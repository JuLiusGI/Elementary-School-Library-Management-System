<?php

/**
 * BookController
 *
 * This controller handles all book catalog management operations including
 * listing, creating, viewing, editing, and deleting book records.
 *
 * Features:
 * - CRUD operations for books
 * - Image upload handling for book covers
 * - Auto-generation of accession numbers
 * - Book statistics for dashboard
 * - Flash messages for user feedback
 *
 * @see App\Models\Book
 * @see App\Http\Requests\BookRequest
 */

namespace App\Http\Controllers;

use App\Models\Book;
use App\Models\Category;
use App\Http\Requests\BookRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;
use Illuminate\Support\Facades\Storage;

class BookController extends Controller
{
    /**
     * Display a listing of books with statistics.
     *
     * Shows:
     * - Statistics cards (total, available, borrowed, by category)
     * - Livewire component for searchable/filterable book catalog
     *
     * @return \Illuminate\View\View
     */
    public function index(): View
    {
        // Calculate statistics for dashboard cards
        $statistics = $this->getBookStatistics();

        // Get categories for filter dropdown
        $categories = Category::orderBy('name')->get();

        return view('books.index', compact('statistics', 'categories'));
    }

    /**
     * Show the form for creating a new book.
     *
     * @return \Illuminate\View\View
     */
    public function create(): View
    {
        $categories = Category::orderBy('name')->get();
        $conditions = $this->getConditionOptions();
        $statuses = $this->getStatusOptions();

        return view('books.create', compact('categories', 'conditions', 'statuses'));
    }

    /**
     * Store a newly created book in the database.
     *
     * Handles:
     * - Auto-generation of accession number if not provided
     * - Image upload for book cover
     *
     * @param \App\Http\Requests\BookRequest $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(BookRequest $request): RedirectResponse
    {
        $validated = $request->validated();

        // Auto-generate accession number if not provided
        if (empty($validated['accession_number'])) {
            $validated['accession_number'] = $this->generateAccessionNumber();
        }

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            $validated['cover_image'] = $this->uploadCoverImage($request->file('cover_image'));
        }

        // Create the book record
        $book = Book::create($validated);

        return redirect()
            ->route('books.index')
            ->with('success', "Book '{$book->title}' has been added to the catalog successfully.");
    }

    /**
     * Display the specified book's details.
     *
     * Shows:
     * - Book information
     * - Category
     * - Borrowing history
     * - Current availability
     *
     * @param \App\Models\Book $book
     * @return \Illuminate\View\View
     */
    public function show(Book $book): View
    {
        // Load category and transactions with related data
        $book->load([
            'category',
            'transactions' => function ($query) {
                $query->with(['student', 'librarian'])
                      ->orderBy('created_at', 'desc');
            }
        ]);

        // Get current borrowers (transactions not yet returned)
        $currentBorrowers = $book->transactions()
            ->whereIn('status', ['borrowed', 'overdue'])
            ->with('student')
            ->get();

        // Get borrowing history with pagination
        $borrowingHistory = $book->transactions()
            ->with(['student', 'librarian'])
            ->orderBy('borrowed_date', 'desc')
            ->paginate(10);

        return view('books.show', compact('book', 'currentBorrowers', 'borrowingHistory'));
    }

    /**
     * Show the form for editing the specified book.
     *
     * @param \App\Models\Book $book
     * @return \Illuminate\View\View
     */
    public function edit(Book $book): View
    {
        $categories = Category::orderBy('name')->get();
        $conditions = $this->getConditionOptions();
        $statuses = $this->getStatusOptions();

        return view('books.edit', compact('book', 'categories', 'conditions', 'statuses'));
    }

    /**
     * Update the specified book in the database.
     *
     * Handles:
     * - Updating book information
     * - Replacing cover image if new one uploaded
     * - Deleting old cover image when replaced
     *
     * @param \App\Http\Requests\BookRequest $request
     * @param \App\Models\Book $book
     * @return \Illuminate\Http\RedirectResponse
     */
    public function update(BookRequest $request, Book $book): RedirectResponse
    {
        $validated = $request->validated();

        // Handle cover image upload
        if ($request->hasFile('cover_image')) {
            // Delete old image if exists
            if ($book->cover_image) {
                Storage::disk('public')->delete($book->cover_image);
            }
            $validated['cover_image'] = $this->uploadCoverImage($request->file('cover_image'));
        }

        // Update the book record
        $book->update($validated);

        return redirect()
            ->route('books.show', $book)
            ->with('success', "Book '{$book->title}' has been updated successfully.");
    }

    /**
     * Remove the specified book from the database.
     *
     * Checks if book has active transactions before deletion.
     * Deletes cover image if exists.
     *
     * @param \App\Models\Book $book
     * @return \Illuminate\Http\RedirectResponse
     */
    public function destroy(Book $book): RedirectResponse
    {
        // Check if book has active (unreturned) transactions
        $activeTransactions = $book->transactions()
            ->whereIn('status', ['borrowed', 'overdue'])
            ->count();

        if ($activeTransactions > 0) {
            return redirect()
                ->route('books.index')
                ->with('error', "Cannot delete '{$book->title}'. This book has {$activeTransactions} unreturned copy(ies).");
        }

        // Store title for message
        $bookTitle = $book->title;

        // Delete cover image if exists
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
        }

        // Delete the book
        $book->delete();

        return redirect()
            ->route('books.index')
            ->with('success', "Book '{$bookTitle}' has been removed from the catalog.");
    }

    /**
     * Remove the cover image from a book.
     *
     * @param \App\Models\Book $book
     * @return \Illuminate\Http\RedirectResponse
     */
    public function removeCover(Book $book): RedirectResponse
    {
        if ($book->cover_image) {
            Storage::disk('public')->delete($book->cover_image);
            $book->update(['cover_image' => null]);
        }

        return redirect()
            ->route('books.edit', $book)
            ->with('success', 'Cover image has been removed.');
    }

    // =========================================================================
    // PRIVATE HELPER METHODS
    // =========================================================================

    /**
     * Generate a unique accession number.
     *
     * Format: YEAR-####
     * Example: 2026-0001, 2026-0002, etc.
     *
     * @return string Generated accession number
     */
    private function generateAccessionNumber(): string
    {
        $year = date('Y');
        $prefix = $year . '-';

        // Find the highest number for this year
        $lastBook = Book::where('accession_number', 'like', $prefix . '%')
            ->orderBy('accession_number', 'desc')
            ->first();

        if ($lastBook) {
            // Extract the number part and increment
            $lastNumber = (int) substr($lastBook->accession_number, strlen($prefix));
            $newNumber = $lastNumber + 1;
        } else {
            // Start from 1 for new year
            $newNumber = 1;
        }

        // Format with leading zeros (4 digits)
        return $prefix . str_pad($newNumber, 4, '0', STR_PAD_LEFT);
    }

    /**
     * Upload and store the book cover image.
     *
     * Stores in: storage/app/public/book-covers/
     * Accessible via: /storage/book-covers/filename
     *
     * @param \Illuminate\Http\UploadedFile $file
     * @return string Path to stored image
     */
    private function uploadCoverImage($file): string
    {
        // Generate unique filename
        $filename = time() . '_' . uniqid() . '.' . $file->getClientOriginalExtension();

        // Store in public disk under book-covers directory
        $path = $file->storeAs('book-covers', $filename, 'public');

        return $path;
    }

    /**
     * Get book statistics for the index page.
     *
     * @return array Statistics data
     */
    private function getBookStatistics(): array
    {
        // Total books (titles, not copies)
        $totalBooks = Book::count();

        // Total copies owned
        $totalCopies = Book::sum('copies_total');

        // Available copies
        $availableCopies = Book::sum('copies_available');

        // Currently borrowed (total - available)
        $borrowedCopies = $totalCopies - $availableCopies;

        // Books by category
        $booksByCategory = Category::withCount('books')
            ->orderBy('books_count', 'desc')
            ->take(5)
            ->get()
            ->map(function ($category) {
                return [
                    'name' => $category->name,
                    'count' => $category->books_count,
                ];
            });

        return [
            'total_books' => $totalBooks,
            'total_copies' => $totalCopies,
            'available_copies' => $availableCopies,
            'borrowed_copies' => $borrowedCopies,
            'by_category' => $booksByCategory,
        ];
    }

    /**
     * Get condition options for form dropdowns.
     *
     * @return array Condition options
     */
    private function getConditionOptions(): array
    {
        return [
            'excellent' => 'Excellent - Like new',
            'good' => 'Good - Normal wear',
            'fair' => 'Fair - Noticeable wear',
            'poor' => 'Poor - Damaged',
        ];
    }

    /**
     * Get status options for form dropdowns.
     *
     * @return array Status options
     */
    private function getStatusOptions(): array
    {
        return [
            'available' => 'Available',
            'unavailable' => 'Unavailable (Lost/Withdrawn)',
        ];
    }
}
