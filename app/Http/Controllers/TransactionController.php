<?php

/**
 * TransactionController
 *
 * This controller handles all book borrowing and returning operations.
 * It is the central hub for the library's circulation system.
 *
 * Features:
 * - Book borrowing with eligibility checks
 * - Book returns with automatic fine calculation
 * - Transaction history with filtering
 * - Fine payment processing
 *
 * The controller works with:
 * - BorrowingService: Business logic for borrowing/returning
 * - FineCalculationService: Fine calculation logic
 * - Livewire components: Real-time search and form handling
 *
 * @see App\Services\BorrowingService
 * @see App\Services\FineCalculationService
 * @see App\Livewire\BorrowBookForm
 * @see App\Livewire\ReturnBookForm
 */

namespace App\Http\Controllers;

use App\Models\Transaction;
use App\Models\Student;
use App\Models\Book;
use App\Models\Setting;
use App\Services\BorrowingService;
use App\Services\FineCalculationService;
use Illuminate\Http\Request;
use Illuminate\Http\RedirectResponse;
use Illuminate\View\View;

class TransactionController extends Controller
{
    /**
     * The borrowing service instance.
     *
     * @var BorrowingService
     */
    protected BorrowingService $borrowingService;

    /**
     * The fine calculation service instance.
     *
     * @var FineCalculationService
     */
    protected FineCalculationService $fineService;

    /**
     * Create a new controller instance.
     *
     * Injects the required service classes via dependency injection.
     * Laravel automatically resolves these from the service container.
     *
     * @param BorrowingService $borrowingService
     * @param FineCalculationService $fineService
     */
    public function __construct(BorrowingService $borrowingService, FineCalculationService $fineService)
    {
        $this->borrowingService = $borrowingService;
        $this->fineService = $fineService;
    }

    // =========================================================================
    // BORROWING METHODS
    // =========================================================================

    /**
     * Display the book borrowing interface.
     *
     * Shows a form where the librarian can:
     * - Search and select a student
     * - Search and select books to borrow
     * - See student's current borrowed books
     * - See any restrictions (fines, overdue books)
     * - Set the due date
     *
     * @return \Illuminate\View\View
     */
    public function borrowIndex(): View
    {
        // Get library settings for display
        $settings = [
            'max_books' => Setting::getInt('max_books_per_student', 3),
            'borrowing_period' => Setting::getInt('borrowing_period', 7),
            'fine_per_day' => Setting::getFloat('fine_per_day', 5.00),
            'grace_period' => Setting::getInt('grace_period', 1),
        ];

        return view('transactions.borrow', compact('settings'));
    }

    /**
     * Process a book borrowing request.
     *
     * This method is called from the Livewire component after validation.
     * It uses the BorrowingService to handle the actual borrowing logic.
     *
     * Steps:
     * 1. Validate the request
     * 2. Check student eligibility
     * 3. Check book availability
     * 4. Create transaction record
     * 5. Update book availability
     *
     * @param Request $request Contains student_id, book_id, and optional due_date
     * @return \Illuminate\Http\RedirectResponse
     */
    public function borrowStore(Request $request): RedirectResponse
    {
        // Validate the incoming request
        $validated = $request->validate([
            'student_id' => 'required|exists:students,id',
            'book_id' => 'required|exists:books,id',
            'due_date' => 'nullable|date|after:today',
        ]);

        // Get the student and book models
        $student = Student::findOrFail($validated['student_id']);
        $book = Book::findOrFail($validated['book_id']);
        $librarian = auth()->user();

        // Parse due date if provided
        $dueDate = isset($validated['due_date'])
            ? \Carbon\Carbon::parse($validated['due_date'])
            : null;

        try {
            // Use the borrowing service to process the transaction
            $transaction = $this->borrowingService->borrowBook(
                $student,
                $book,
                $librarian,
                $dueDate
            );

            // Success! Redirect with success message
            return redirect()
                ->route('transactions.borrow')
                ->with('success', "Book '{$book->title}' has been borrowed by {$student->full_name}. Due date: {$transaction->due_date->format('M d, Y')}");

        } catch (\Exception $e) {
            // Something went wrong - show error message
            return redirect()
                ->route('transactions.borrow')
                ->with('error', $e->getMessage());
        }
    }

    // =========================================================================
    // RETURN METHODS
    // =========================================================================

    /**
     * Display the book return interface.
     *
     * Shows a form where the librarian can:
     * - Search for a student or book to return
     * - View currently borrowed books
     * - Process returns with fine calculation
     * - Update book condition
     *
     * @return \Illuminate\View\View
     */
    public function returnIndex(): View
    {
        // Get fine policy for display
        $finePolicy = $this->fineService->getFinePolicy();

        return view('transactions.return', compact('finePolicy'));
    }

    /**
     * Process a book return.
     *
     * This method handles returning a borrowed book:
     * 1. Marks the transaction as returned
     * 2. Calculates any overdue fines
     * 3. Updates book availability
     * 4. Optionally updates book condition
     *
     * @param Request $request Contains transaction_id, optional condition and notes
     * @return \Illuminate\Http\RedirectResponse
     */
    public function returnStore(Request $request): RedirectResponse
    {
        // Validate the request
        $validated = $request->validate([
            'transaction_id' => 'required|exists:transactions,id',
            'condition' => 'nullable|in:excellent,good,fair,poor',
            'notes' => 'nullable|string|max:500',
        ]);

        // Get the transaction
        $transaction = Transaction::with(['student', 'book'])->findOrFail($validated['transaction_id']);

        try {
            // Use the borrowing service to process the return
            $transaction = $this->borrowingService->returnBook(
                $transaction,
                $validated['condition'] ?? null,
                $validated['notes'] ?? null
            );

            // Build success message
            $message = "Book '{$transaction->book->title}' has been returned by {$transaction->student->full_name}.";

            // Add fine info if applicable
            if ($transaction->fine_amount > 0) {
                $message .= " Fine: ₱" . number_format($transaction->fine_amount, 2);
            }

            return redirect()
                ->route('transactions.return')
                ->with('success', $message);

        } catch (\Exception $e) {
            return redirect()
                ->route('transactions.return')
                ->with('error', $e->getMessage());
        }
    }

    // =========================================================================
    // TRANSACTION HISTORY
    // =========================================================================

    /**
     * Display the transaction history.
     *
     * Shows all borrowing transactions with filters for:
     * - Status (borrowed, returned, overdue)
     * - Date range
     * - Student
     * - Book
     *
     * @param Request $request Contains optional filter parameters
     * @return \Illuminate\View\View
     */
    public function history(Request $request): View
    {
        // Build query with eager loading to prevent N+1 queries
        $query = Transaction::with(['student', 'book', 'librarian'])
            ->orderBy('created_at', 'desc');

        // Apply status filter if provided
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Apply date range filter if provided
        if ($request->filled('date_from')) {
            $query->whereDate('borrowed_date', '>=', $request->date_from);
        }
        if ($request->filled('date_to')) {
            $query->whereDate('borrowed_date', '<=', $request->date_to);
        }

        // Apply search filter (student name or book title)
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%")
                       ->orWhere('student_id', 'like', "%{$search}%");
                })->orWhereHas('book', function ($bq) use ($search) {
                    $bq->where('title', 'like', "%{$search}%")
                       ->orWhere('accession_number', 'like', "%{$search}%");
                });
            });
        }

        // Get paginated results
        $transactions = $query->paginate(15)->withQueryString();

        // Get statistics for the header
        $statistics = [
            'total' => Transaction::count(),
            'borrowed' => Transaction::where('status', 'borrowed')->count(),
            'overdue' => Transaction::where('status', 'overdue')->count(),
            'returned_today' => Transaction::where('status', 'returned')
                ->whereDate('returned_date', today())
                ->count(),
        ];

        return view('transactions.history', compact('transactions', 'statistics'));
    }

    // =========================================================================
    // FINE MANAGEMENT
    // =========================================================================

    /**
     * Display the fines management page.
     *
     * Shows all students with fines, with options to:
     * - View unpaid fines
     * - View paid fines
     * - Record payments
     * - Waive fines (admin only)
     *
     * @param Request $request Contains optional filter parameters
     * @return \Illuminate\View\View
     */
    public function fineIndex(Request $request): View
    {
        // Get fine statistics
        $statistics = $this->fineService->getFineStatistics();

        // Get fine policy for display
        $finePolicy = $this->fineService->getFinePolicy();

        return view('transactions.fines', compact('statistics', 'finePolicy'));
    }

    /**
     * Mark a fine as paid.
     *
     * Updates the transaction to indicate the fine has been settled.
     *
     * @param Transaction $transaction The transaction with the fine
     * @return \Illuminate\Http\RedirectResponse
     */
    public function payFine(Transaction $transaction): RedirectResponse
    {
        // Check if there's actually a fine to pay
        if ($transaction->fine_amount <= 0) {
            return back()->with('error', 'This transaction has no fine to pay.');
        }

        // Check if fine is already paid
        if ($transaction->fine_paid) {
            return back()->with('error', 'This fine has already been paid.');
        }

        // Mark as paid
        $this->fineService->markFinePaid($transaction);

        return back()->with('success', "Fine of ₱" . number_format($transaction->fine_amount, 2) . " has been marked as paid.");
    }

    /**
     * Record a payment for a fine.
     *
     * Allows recording partial or full payment with payment method.
     *
     * @param Request $request Contains amount and optional payment_method
     * @param Transaction $transaction The transaction with the fine
     * @return \Illuminate\Http\RedirectResponse
     */
    public function recordPayment(Request $request, Transaction $transaction): RedirectResponse
    {
        // Validate the request
        $validated = $request->validate([
            'amount' => 'required|numeric|min:0.01',
            'payment_method' => 'nullable|string|in:cash,gcash,maya,bank_transfer',
        ]);

        // Record the payment
        $result = $this->fineService->recordPayment(
            $transaction,
            $validated['amount'],
            $validated['payment_method'] ?? 'cash'
        );

        if ($result['success']) {
            return back()->with('success', $result['message']);
        }

        return back()->with('error', $result['message']);
    }

    /**
     * Waive a fine (admin only).
     *
     * Sets the fine to zero for special circumstances.
     *
     * @param Request $request Contains the reason for waiving
     * @param Transaction $transaction The transaction with the fine
     * @return \Illuminate\Http\RedirectResponse
     */
    public function waiveFine(Request $request, Transaction $transaction): RedirectResponse
    {
        // Validate reason is provided
        $validated = $request->validate([
            'reason' => 'required|string|max:255',
        ]);

        // Check if there's a fine to waive
        if ($transaction->fine_amount <= 0) {
            return back()->with('error', 'This transaction has no fine to waive.');
        }

        // Waive the fine
        $this->fineService->waiveFine($transaction, $validated['reason']);

        return back()->with('success', 'Fine has been waived successfully.');
    }

    /**
     * Get fine breakdown for a transaction (API).
     *
     * Returns detailed breakdown of fine calculation.
     * Used by Livewire components for display.
     *
     * @param Transaction $transaction The transaction to get breakdown for
     * @return \Illuminate\Http\JsonResponse
     */
    public function getFineBreakdown(Transaction $transaction)
    {
        $breakdown = $this->fineService->getFineBreakdown($transaction);

        return response()->json($breakdown);
    }

    // =========================================================================
    // API ENDPOINTS (for Livewire components)
    // =========================================================================

    /**
     * Get student eligibility status.
     *
     * Returns JSON with eligibility information for a student.
     * Used by Livewire components for real-time validation.
     *
     * @param Student $student The student to check
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkStudentEligibility(Student $student)
    {
        $eligibility = $this->borrowingService->canBorrow($student);
        $currentBooks = $this->borrowingService->getCurrentBorrowedBooks($student);
        $remainingCapacity = $this->borrowingService->getRemainingBorrowingCapacity($student);
        $unpaidFines = $this->fineService->getTotalUnpaidFines($student->id);

        return response()->json([
            'eligible' => $eligibility['eligible'],
            'reason' => $eligibility['reason'],
            'current_books_count' => $currentBooks->count(),
            'remaining_capacity' => $remainingCapacity,
            'unpaid_fines' => $unpaidFines,
            'current_books' => $currentBooks->map(function ($t) {
                return [
                    'id' => $t->id,
                    'book_title' => $t->book->title,
                    'due_date' => $t->due_date->format('M d, Y'),
                    'is_overdue' => $t->status === 'overdue' || $t->due_date->isPast(),
                    'days_until_due' => $t->due_date->diffInDays(now(), false),
                ];
            }),
        ]);
    }

    /**
     * Check book availability.
     *
     * Returns JSON with availability information for a book.
     * Used by Livewire components for real-time validation.
     *
     * @param Book $book The book to check
     * @return \Illuminate\Http\JsonResponse
     */
    public function checkBookAvailability(Book $book)
    {
        return response()->json([
            'available' => $book->copies_available > 0,
            'copies_total' => $book->copies_total,
            'copies_available' => $book->copies_available,
            'status' => $book->status,
        ]);
    }
}
