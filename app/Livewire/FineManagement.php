<?php

/**
 * FineManagement Livewire Component
 *
 * This component provides a real-time interface for managing library fines.
 * It allows librarians to:
 * - View all unpaid and paid fines
 * - Search fines by student or book
 * - Record payments (full or partial)
 * - Waive fines (admin only)
 * - View fine calculation breakdowns
 *
 * The component uses Livewire for real-time updates without page refreshes.
 *
 * @see App\Services\FineCalculationService
 */

namespace App\Livewire;

use App\Models\Transaction;
use App\Models\Student;
use App\Services\FineCalculationService;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\Computed;
use Livewire\Attributes\Url;

class FineManagement extends Component
{
    use WithPagination;

    // =========================================================================
    // PROPERTIES
    // =========================================================================

    /**
     * Search term for filtering fines
     */
    #[Url(as: 'q')]
    public string $search = '';

    /**
     * Filter by payment status (all, unpaid, paid)
     */
    #[Url]
    public string $statusFilter = 'unpaid';

    /**
     * Sort field
     */
    #[Url]
    public string $sortField = 'returned_date';

    /**
     * Sort direction
     */
    #[Url]
    public string $sortDirection = 'desc';

    /**
     * Selected transaction ID for payment modal
     */
    public ?int $selectedTransactionId = null;

    /**
     * Payment amount for the modal
     */
    public ?float $paymentAmount = null;

    /**
     * Payment method
     */
    public string $paymentMethod = 'cash';

    /**
     * Show payment modal
     */
    public bool $showPaymentModal = false;

    /**
     * Show waive modal
     */
    public bool $showWaiveModal = false;

    /**
     * Waive reason
     */
    public string $waiveReason = '';

    /**
     * Show breakdown modal
     */
    public bool $showBreakdownModal = false;

    /**
     * Fine breakdown data
     */
    public array $fineBreakdown = [];

    // =========================================================================
    // COMPUTED PROPERTIES
    // =========================================================================

    /**
     * Get filtered and paginated fines
     */
    #[Computed]
    public function fines()
    {
        $query = Transaction::with(['student', 'book'])
            ->where('fine_amount', '>', 0);

        // Apply status filter
        if ($this->statusFilter === 'unpaid') {
            $query->where('fine_paid', false);
        } elseif ($this->statusFilter === 'paid') {
            $query->where('fine_paid', true);
        }

        // Apply search filter
        if ($this->search) {
            $search = $this->search;
            $query->where(function ($q) use ($search) {
                $q->whereHas('student', function ($sq) use ($search) {
                    $sq->where('first_name', 'like', "%{$search}%")
                       ->orWhere('last_name', 'like', "%{$search}%")
                       ->orWhere('student_id', 'like', "%{$search}%");
                })->orWhereHas('book', function ($bq) use ($search) {
                    $bq->where('title', 'like', "%{$search}%");
                });
            });
        }

        // Apply sorting
        $query->orderBy($this->sortField, $this->sortDirection);

        return $query->paginate(10);
    }

    /**
     * Get the selected transaction
     */
    #[Computed]
    public function selectedTransaction()
    {
        if (!$this->selectedTransactionId) {
            return null;
        }

        return Transaction::with(['student', 'book'])->find($this->selectedTransactionId);
    }

    /**
     * Get fine statistics
     */
    #[Computed]
    public function statistics()
    {
        $fineService = new FineCalculationService();
        return $fineService->getFineStatistics();
    }

    /**
     * Get students with unpaid fines (grouped)
     */
    #[Computed]
    public function studentsWithFines()
    {
        return Student::whereHas('transactions', function ($q) {
            $q->where('fine_amount', '>', 0)->where('fine_paid', false);
        })->withSum(['transactions as total_fines' => function ($q) {
            $q->where('fine_amount', '>', 0)->where('fine_paid', false);
        }], 'fine_amount')
        ->orderByDesc('total_fines')
        ->limit(10)
        ->get();
    }

    // =========================================================================
    // ACTIONS
    // =========================================================================

    /**
     * Open payment modal for a transaction
     */
    public function openPaymentModal(int $transactionId): void
    {
        $this->selectedTransactionId = $transactionId;
        $transaction = $this->selectedTransaction;

        if ($transaction) {
            $this->paymentAmount = $transaction->fine_amount;
            $this->paymentMethod = 'cash';
            $this->showPaymentModal = true;
        }
    }

    /**
     * Close payment modal
     */
    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
        $this->selectedTransactionId = null;
        $this->paymentAmount = null;
        $this->paymentMethod = 'cash';
    }

    /**
     * Process payment
     */
    public function processPayment(): void
    {
        $this->validate([
            'paymentAmount' => 'required|numeric|min:0.01',
            'paymentMethod' => 'required|in:cash,gcash,maya,bank_transfer',
        ]);

        $transaction = $this->selectedTransaction;

        if (!$transaction) {
            session()->flash('error', 'Transaction not found.');
            $this->closePaymentModal();
            return;
        }

        $fineService = new FineCalculationService();
        $result = $fineService->recordPayment(
            $transaction,
            $this->paymentAmount,
            $this->paymentMethod
        );

        if ($result['success']) {
            session()->flash('success', $result['message']);
        } else {
            session()->flash('error', $result['message']);
        }

        $this->closePaymentModal();
    }

    /**
     * Quick pay - mark as fully paid
     */
    public function quickPay(int $transactionId): void
    {
        $transaction = Transaction::find($transactionId);

        if (!$transaction) {
            session()->flash('error', 'Transaction not found.');
            return;
        }

        $fineService = new FineCalculationService();
        $fineService->markFinePaid($transaction);

        session()->flash('success', "Fine of ₱" . number_format($transaction->fine_amount, 2) . " marked as paid.");
    }

    /**
     * Open waive modal
     */
    public function openWaiveModal(int $transactionId): void
    {
        $this->selectedTransactionId = $transactionId;
        $this->waiveReason = '';
        $this->showWaiveModal = true;
    }

    /**
     * Close waive modal
     */
    public function closeWaiveModal(): void
    {
        $this->showWaiveModal = false;
        $this->selectedTransactionId = null;
        $this->waiveReason = '';
    }

    /**
     * Process waive
     */
    public function processWaive(): void
    {
        $this->validate([
            'waiveReason' => 'required|string|min:5|max:255',
        ]);

        $transaction = $this->selectedTransaction;

        if (!$transaction) {
            session()->flash('error', 'Transaction not found.');
            $this->closeWaiveModal();
            return;
        }

        // Check if user is admin
        if (!auth()->user()->isAdmin()) {
            session()->flash('error', 'Only administrators can waive fines.');
            $this->closeWaiveModal();
            return;
        }

        $fineService = new FineCalculationService();
        $fineService->waiveFine($transaction, $this->waiveReason);

        session()->flash('success', 'Fine has been waived successfully.');
        $this->closeWaiveModal();
    }

    /**
     * Show fine breakdown
     */
    public function showBreakdown(int $transactionId): void
    {
        $transaction = Transaction::find($transactionId);

        if (!$transaction) {
            return;
        }

        $fineService = new FineCalculationService();
        $this->fineBreakdown = $fineService->getFineBreakdown($transaction);
        $this->selectedTransactionId = $transactionId;
        $this->showBreakdownModal = true;
    }

    /**
     * Close breakdown modal
     */
    public function closeBreakdownModal(): void
    {
        $this->showBreakdownModal = false;
        $this->fineBreakdown = [];
        $this->selectedTransactionId = null;
    }

    /**
     * Sort by a field
     */
    public function sortBy(string $field): void
    {
        if ($this->sortField === $field) {
            $this->sortDirection = $this->sortDirection === 'asc' ? 'desc' : 'asc';
        } else {
            $this->sortField = $field;
            $this->sortDirection = 'desc';
        }
    }

    /**
     * Reset filters
     */
    public function resetFilters(): void
    {
        $this->search = '';
        $this->statusFilter = 'unpaid';
        $this->sortField = 'returned_date';
        $this->sortDirection = 'desc';
        $this->resetPage();
    }

    /**
     * Update search - reset pagination
     */
    public function updatedSearch(): void
    {
        $this->resetPage();
    }

    /**
     * Update status filter - reset pagination
     */
    public function updatedStatusFilter(): void
    {
        $this->resetPage();
    }

    // =========================================================================
    // RENDER
    // =========================================================================

    /**
     * Render the component
     */
    public function render()
    {
        return view('livewire.fine-management');
    }
}
