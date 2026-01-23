<?php

/**
 * BorrowBookForm Livewire Component
 *
 * This component handles the interactive book borrowing process.
 * It provides a step-by-step flow:
 * 1. Search and select a student
 * 2. Check student eligibility (limits, fines, overdue)
 * 3. Search and select a book
 * 4. Confirm borrowing details and due date
 * 5. Process the transaction
 *
 * Features:
 * - Real-time student search with eligibility display
 * - Real-time book search with availability display
 * - Shows student's current borrowed books
 * - Shows any warnings (fines, overdue, limits)
 * - Calculates and displays due date
 *
 * @see App\Services\BorrowingService
 * @see resources/views/livewire/borrow-book-form.blade.php
 */

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student;
use App\Models\Book;
use App\Models\Setting;
use App\Services\BorrowingService;
use App\Services\FineCalculationService;
use Carbon\Carbon;

class BorrowBookForm extends Component
{
    // =========================================================================
    // COMPONENT PROPERTIES
    // =========================================================================

    /**
     * Current step in the borrowing process (1-4).
     * Step 1: Select student
     * Step 2: Review student info & select book
     * Step 3: Confirm details
     * Step 4: Success/Receipt
     *
     * @var int
     */
    public int $step = 1;

    /**
     * Student search query.
     *
     * @var string
     */
    public string $studentSearch = '';

    /**
     * Selected student ID.
     *
     * @var int|null
     */
    public ?int $selectedStudentId = null;

    /**
     * Book search query.
     *
     * @var string
     */
    public string $bookSearch = '';

    /**
     * Selected book ID.
     *
     * @var int|null
     */
    public ?int $selectedBookId = null;

    /**
     * Custom due date (optional).
     *
     * @var string|null
     */
    public ?string $dueDate = null;

    /**
     * The created transaction after successful borrowing.
     *
     * @var mixed
     */
    public $transaction = null;

    /**
     * Error message to display.
     *
     * @var string|null
     */
    public ?string $errorMessage = null;

    // =========================================================================
    // LIFECYCLE HOOKS
    // =========================================================================

    /**
     * Initialize component with default due date.
     *
     * @return void
     */
    public function mount(): void
    {
        // Set default due date based on borrowing period setting
        $borrowingPeriod = Setting::getInt('borrowing_period', 7);
        $this->dueDate = Carbon::now()->addDays($borrowingPeriod)->format('Y-m-d');
    }

    /**
     * Reset book search when student changes.
     *
     * @return void
     */
    public function updatedSelectedStudentId(): void
    {
        // Clear book selection when student changes
        $this->selectedBookId = null;
        $this->bookSearch = '';
        $this->errorMessage = null;
    }

    // =========================================================================
    // COMPUTED PROPERTIES
    // =========================================================================

    /**
     * Get students matching the search query.
     *
     * Returns active students matching the search term.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getStudentsProperty()
    {
        if (strlen($this->studentSearch) < 2) {
            return collect();
        }

        return Student::where('status', 'active')
            ->where(function ($query) {
                $query->where('first_name', 'like', "%{$this->studentSearch}%")
                    ->orWhere('last_name', 'like', "%{$this->studentSearch}%")
                    ->orWhere('student_id', 'like', "%{$this->studentSearch}%");
            })
            ->orderBy('last_name')
            ->limit(10)
            ->get();
    }

    /**
     * Get the selected student model.
     *
     * @return Student|null
     */
    public function getSelectedStudentProperty(): ?Student
    {
        if (!$this->selectedStudentId) {
            return null;
        }

        return Student::find($this->selectedStudentId);
    }

    /**
     * Get student eligibility information.
     *
     * @return array|null
     */
    public function getStudentEligibilityProperty(): ?array
    {
        if (!$this->selectedStudent) {
            return null;
        }

        $borrowingService = new BorrowingService();
        $fineService = new FineCalculationService();

        return [
            'can_borrow' => $borrowingService->canBorrow($this->selectedStudent),
            'current_books' => $borrowingService->getCurrentBorrowedBooks($this->selectedStudent),
            'remaining_capacity' => $borrowingService->getRemainingBorrowingCapacity($this->selectedStudent),
            'unpaid_fines' => $fineService->getTotalUnpaidFines($this->selectedStudent->id),
            'max_books' => Setting::getInt('max_books_per_student', 3),
        ];
    }

    /**
     * Get available books matching the search query.
     *
     * Returns books with available copies matching the search term.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getBooksProperty()
    {
        if (strlen($this->bookSearch) < 2) {
            return collect();
        }

        return Book::with('category')
            ->where('copies_available', '>', 0)
            ->where('status', 'available')
            ->where(function ($query) {
                $query->where('title', 'like', "%{$this->bookSearch}%")
                    ->orWhere('author', 'like', "%{$this->bookSearch}%")
                    ->orWhere('accession_number', 'like', "%{$this->bookSearch}%")
                    ->orWhere('isbn', 'like', "%{$this->bookSearch}%");
            })
            ->orderBy('title')
            ->limit(10)
            ->get();
    }

    /**
     * Get the selected book model.
     *
     * @return Book|null
     */
    public function getSelectedBookProperty(): ?Book
    {
        if (!$this->selectedBookId) {
            return null;
        }

        return Book::with('category')->find($this->selectedBookId);
    }

    /**
     * Get library settings for display.
     *
     * @return array
     */
    public function getSettingsProperty(): array
    {
        return [
            'max_books' => Setting::getInt('max_books_per_student', 3),
            'borrowing_period' => Setting::getInt('borrowing_period', 7),
            'fine_per_day' => Setting::getFloat('fine_per_day', 5.00),
            'grace_period' => Setting::getInt('grace_period', 1),
        ];
    }

    // =========================================================================
    // ACTIONS
    // =========================================================================

    /**
     * Select a student and move to step 2.
     *
     * @param int $studentId
     * @return void
     */
    public function selectStudent(int $studentId): void
    {
        $this->selectedStudentId = $studentId;
        $this->studentSearch = '';
        $this->errorMessage = null;

        // Check eligibility before proceeding
        if ($this->studentEligibility && !$this->studentEligibility['can_borrow']['eligible']) {
            $this->errorMessage = $this->studentEligibility['can_borrow']['reason'];
            return;
        }

        $this->step = 2;
    }

    /**
     * Select a book and move to step 3.
     *
     * @param int $bookId
     * @return void
     */
    public function selectBook(int $bookId): void
    {
        $this->selectedBookId = $bookId;
        $this->bookSearch = '';
        $this->errorMessage = null;

        // Verify book is still available
        $book = Book::find($bookId);
        if (!$book || $book->copies_available <= 0) {
            $this->errorMessage = 'This book is no longer available.';
            $this->selectedBookId = null;
            return;
        }

        $this->step = 3;
    }

    /**
     * Go back to previous step.
     *
     * @return void
     */
    public function previousStep(): void
    {
        $this->errorMessage = null;

        if ($this->step > 1) {
            $this->step--;
        }

        // Clear selections when going back
        if ($this->step === 1) {
            $this->selectedStudentId = null;
            $this->selectedBookId = null;
        } elseif ($this->step === 2) {
            $this->selectedBookId = null;
        }
    }

    /**
     * Process the book borrowing.
     *
     * Creates the transaction and updates book availability.
     *
     * @return void
     */
    public function processBorrow(): void
    {
        $this->errorMessage = null;

        // Validate we have all required data
        if (!$this->selectedStudentId || !$this->selectedBookId) {
            $this->errorMessage = 'Please select both a student and a book.';
            return;
        }

        try {
            $borrowingService = new BorrowingService();

            // Get models
            $student = Student::findOrFail($this->selectedStudentId);
            $book = Book::findOrFail($this->selectedBookId);
            $librarian = auth()->user();

            // Parse due date
            $dueDate = $this->dueDate ? Carbon::parse($this->dueDate) : null;

            // Process the borrowing
            $this->transaction = $borrowingService->borrowBook(
                $student,
                $book,
                $librarian,
                $dueDate
            );

            // Load relationships for display
            $this->transaction->load(['student', 'book', 'librarian']);

            // Move to success step
            $this->step = 4;

            // Dispatch browser event for success notification
            $this->dispatch('borrow-success', [
                'message' => "Book borrowed successfully!"
            ]);

        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    /**
     * Reset the form to start a new transaction.
     *
     * @return void
     */
    public function startNew(): void
    {
        $this->reset([
            'step',
            'studentSearch',
            'selectedStudentId',
            'bookSearch',
            'selectedBookId',
            'transaction',
            'errorMessage',
        ]);

        // Reset due date to default
        $borrowingPeriod = Setting::getInt('borrowing_period', 7);
        $this->dueDate = Carbon::now()->addDays($borrowingPeriod)->format('Y-m-d');

        $this->step = 1;
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    /**
     * Render the component.
     *
     * @return \Illuminate\View\View
     */
    public function render()
    {
        return view('livewire.borrow-book-form');
    }
}
