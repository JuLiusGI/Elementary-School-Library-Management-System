<?php

/**
 * ReturnBookForm Livewire Component
 *
 * This component handles the book return process.
 * It provides functionality to:
 * 1. Search for borrowed books by student or book
 * 2. Display borrowed books with due dates
 * 3. Calculate and display fines for overdue books
 * 4. Process returns with optional condition update
 * 5. Handle fine payment
 *
 * Features:
 * - Real-time search for borrowed transactions
 * - Automatic fine calculation
 * - Book condition update option
 * - Fine payment processing
 *
 * @see App\Services\BorrowingService
 * @see App\Services\FineCalculationService
 * @see resources/views/livewire/return-book-form.blade.php
 */

namespace App\Livewire;

use Livewire\Component;
use App\Models\Student;
use App\Models\Book;
use App\Models\Transaction;
use App\Models\Setting;
use App\Services\BorrowingService;
use App\Services\FineCalculationService;
use Carbon\Carbon;

class ReturnBookForm extends Component
{
    // =========================================================================
    // COMPONENT PROPERTIES
    // =========================================================================

    /**
     * Search query for finding borrowed books.
     * Can search by student name, student ID, book title, or accession number.
     *
     * @var string
     */
    public string $search = '';

    /**
     * Selected transaction ID for return.
     *
     * @var int|null
     */
    public ?int $selectedTransactionId = null;

    /**
     * Book condition on return.
     *
     * @var string|null
     */
    public ?string $condition = null;

    /**
     * Notes about the return.
     *
     * @var string
     */
    public string $notes = '';

    /**
     * Whether to mark fine as paid during return.
     *
     * @var bool
     */
    public bool $payFineNow = false;

    /**
     * The processed transaction after return.
     *
     * @var mixed
     */
    public $returnedTransaction = null;

    /**
     * Error message to display.
     *
     * @var string|null
     */
    public ?string $errorMessage = null;

    /**
     * Success message to display.
     *
     * @var string|null
     */
    public ?string $successMessage = null;

    // =========================================================================
    // COMPUTED PROPERTIES
    // =========================================================================

    /**
     * Get borrowed transactions matching the search query.
     *
     * Returns transactions that are currently borrowed or overdue.
     *
     * @return \Illuminate\Database\Eloquent\Collection
     */
    public function getTransactionsProperty()
    {
        if (strlen($this->search) < 2) {
            // Show recent borrowed books if no search
            return Transaction::with(['student', 'book'])
                ->whereIn('status', ['borrowed', 'overdue'])
                ->orderBy('due_date', 'asc')
                ->limit(10)
                ->get();
        }

        return Transaction::with(['student', 'book'])
            ->whereIn('status', ['borrowed', 'overdue'])
            ->where(function ($query) {
                // Search by student
                $query->whereHas('student', function ($sq) {
                    $sq->where('first_name', 'like', "%{$this->search}%")
                       ->orWhere('last_name', 'like', "%{$this->search}%")
                       ->orWhere('student_id', 'like', "%{$this->search}%");
                })
                // Or search by book
                ->orWhereHas('book', function ($bq) {
                    $bq->where('title', 'like', "%{$this->search}%")
                       ->orWhere('accession_number', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('due_date', 'asc')
            ->limit(20)
            ->get();
    }

    /**
     * Get the selected transaction for return.
     *
     * @return Transaction|null
     */
    public function getSelectedTransactionProperty(): ?Transaction
    {
        if (!$this->selectedTransactionId) {
            return null;
        }

        return Transaction::with(['student', 'book'])->find($this->selectedTransactionId);
    }

    /**
     * Calculate the fine for the selected transaction.
     *
     * @return float
     */
    public function getCalculatedFineProperty(): float
    {
        if (!$this->selectedTransaction) {
            return 0;
        }

        $fineService = new FineCalculationService();
        return $fineService->calculateFine($this->selectedTransaction);
    }

    /**
     * Get days overdue for the selected transaction.
     *
     * @return int
     */
    public function getDaysOverdueProperty(): int
    {
        if (!$this->selectedTransaction) {
            return 0;
        }

        $fineService = new FineCalculationService();
        return $fineService->getDaysOverdue($this->selectedTransaction);
    }

    /**
     * Get fine policy information.
     *
     * @return array
     */
    public function getFinePolicyProperty(): array
    {
        $fineService = new FineCalculationService();
        return $fineService->getFinePolicy();
    }

    /**
     * Get overdue transactions count for alert.
     *
     * @return int
     */
    public function getOverdueCountProperty(): int
    {
        return Transaction::where('status', 'overdue')->count();
    }

    // =========================================================================
    // ACTIONS
    // =========================================================================

    /**
     * Select a transaction for return.
     *
     * @param int $transactionId
     * @return void
     */
    public function selectTransaction(int $transactionId): void
    {
        $this->selectedTransactionId = $transactionId;
        $this->errorMessage = null;
        $this->successMessage = null;

        // Set default condition to book's current condition
        if ($this->selectedTransaction) {
            $this->condition = $this->selectedTransaction->book->condition;
        }
    }

    /**
     * Cancel selection and go back to list.
     *
     * @return void
     */
    public function cancelSelection(): void
    {
        $this->selectedTransactionId = null;
        $this->condition = null;
        $this->notes = '';
        $this->payFineNow = false;
        $this->errorMessage = null;
    }

    /**
     * Process the book return.
     *
     * @return void
     */
    public function processReturn(): void
    {
        $this->errorMessage = null;
        $this->successMessage = null;

        if (!$this->selectedTransactionId) {
            $this->errorMessage = 'Please select a transaction to return.';
            return;
        }

        try {
            $borrowingService = new BorrowingService();
            $fineService = new FineCalculationService();

            // Get the transaction
            $transaction = Transaction::findOrFail($this->selectedTransactionId);

            // Process the return
            $returnedTransaction = $borrowingService->returnBook(
                $transaction,
                $this->condition,
                $this->notes ?: null
            );

            // If pay fine now is selected and there's a fine
            if ($this->payFineNow && $returnedTransaction->fine_amount > 0) {
                $fineService->markFinePaid($returnedTransaction);
                $returnedTransaction->refresh();
            }

            // Store for display
            $this->returnedTransaction = $returnedTransaction->load(['student', 'book']);

            // Build success message
            $message = "Book '{$returnedTransaction->book->title}' returned successfully.";
            if ($returnedTransaction->fine_amount > 0) {
                $fineStatus = $returnedTransaction->fine_paid ? ' (Fine paid)' : ' (Fine pending)';
                $message .= " Fine: ₱" . number_format($returnedTransaction->fine_amount, 2) . $fineStatus;
            }

            $this->successMessage = $message;

            // Reset form
            $this->selectedTransactionId = null;
            $this->condition = null;
            $this->notes = '';
            $this->payFineNow = false;

            // Dispatch success event
            $this->dispatch('return-success', [
                'message' => $message
            ]);

        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    /**
     * Mark fine as paid for a transaction.
     *
     * @param int $transactionId
     * @return void
     */
    public function markFinePaid(int $transactionId): void
    {
        try {
            $transaction = Transaction::findOrFail($transactionId);
            $fineService = new FineCalculationService();
            $fineService->markFinePaid($transaction);

            $this->successMessage = "Fine of ₱" . number_format($transaction->fine_amount, 2) . " marked as paid.";

        } catch (\Exception $e) {
            $this->errorMessage = $e->getMessage();
        }
    }

    /**
     * Reset the form for a new return.
     *
     * @return void
     */
    public function resetForm(): void
    {
        $this->reset([
            'search',
            'selectedTransactionId',
            'condition',
            'notes',
            'payFineNow',
            'returnedTransaction',
            'errorMessage',
            'successMessage',
        ]);
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
        return view('livewire.return-book-form');
    }
}
