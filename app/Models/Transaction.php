<?php

/**
 * Transaction Model
 *
 * This model represents a book borrowing transaction. Each transaction
 * records one book being borrowed by one student.
 *
 * Transaction Lifecycle:
 * 1. Student borrows book -> Transaction created with status='borrowed'
 * 2. If due date passes -> Status changed to 'overdue' (by scheduled task)
 * 3. Student returns book -> Status changed to 'returned', fine calculated
 *
 * Key Fields:
 * - student_id: Who borrowed the book
 * - book_id: What book was borrowed
 * - librarian_id: Who processed the transaction
 * - borrowed_date: When the book was borrowed
 * - due_date: When the book must be returned
 * - returned_date: When the book was actually returned (null if not returned)
 * - status: Current state (borrowed, overdue, returned)
 * - fine_amount: Fine in PHP if returned late
 * - fine_paid: Whether the fine has been paid
 *
 * Fine Calculation:
 * - Fine = (days_overdue - grace_period) × fine_per_day
 * - Default: 1 day grace period, ₱5.00 per day
 *
 * @see database/migrations/2026_01_22_005337_create_transactions_table.php
 * @see App\Services\BorrowingService
 * @see App\Services\FineCalculationService
 * @see docs/TECHNICAL_SPEC.md - Section 3.1 (transactions table)
 */

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Transaction extends Model
{
    /**
     * Use HasFactory trait to enable model factories for testing.
     *
     * Usage:
     * Transaction::factory()->create(['status' => 'borrowed']);
     */
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * All transaction fields except 'id' and timestamps are fillable.
     *
     * Usage:
     * Transaction::create([
     *     'student_id' => 1,
     *     'book_id' => 5,
     *     'librarian_id' => auth()->id(),
     *     'borrowed_date' => now(),
     *     'due_date' => now()->addDays(7),
     *     'status' => 'borrowed',
     * ]);
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'student_id',     // FK: Student who borrowed the book
        'book_id',        // FK: Book that was borrowed
        'librarian_id',   // FK: User who processed the transaction
        'borrowed_date',  // Date book was borrowed
        'due_date',       // Date book must be returned
        'returned_date',  // Date book was returned (null if not returned)
        'status',         // borrowed, overdue, or returned
        'notes',          // Optional notes about the transaction
        'fine_amount',    // Fine amount in PHP (0 if no fine)
        'fine_paid',      // Whether fine has been paid
    ];

    /**
     * The attributes that should be cast.
     *
     * Casting ensures consistent data types:
     * - Dates are cast to Carbon objects for date manipulation
     * - fine_amount as decimal for currency calculations
     * - fine_paid as boolean for true/false checks
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'borrowed_date' => 'date',    // Cast to Carbon date object
            'due_date' => 'date',         // Cast to Carbon date object
            'returned_date' => 'date',    // Cast to Carbon date object (nullable)
            'fine_amount' => 'decimal:2', // Cast to decimal with 2 places
            'fine_paid' => 'boolean',     // Cast to true/false
        ];
    }

    // =========================================================================
    // RELATIONSHIPS
    // =========================================================================

    /**
     * Get the student who borrowed the book.
     *
     * Each transaction belongs to one student.
     *
     * Usage:
     * $transaction->student;           // Get the student model
     * $transaction->student->full_name; // Get student's name
     *
     * In Blade:
     * {{ $transaction->student->full_name }}
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(Student::class);
    }

    /**
     * Get the book that was borrowed.
     *
     * Each transaction is for one book.
     *
     * Usage:
     * $transaction->book;        // Get the book model
     * $transaction->book->title; // Get book title
     *
     * In Blade:
     * {{ $transaction->book->title }}
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function book(): BelongsTo
    {
        return $this->belongsTo(Book::class);
    }

    /**
     * Get the librarian/user who processed this transaction.
     *
     * Records who processed the borrowing for accountability.
     *
     * Usage:
     * $transaction->librarian;       // Get the user model
     * $transaction->librarian->name; // Get librarian's name
     *
     * @return \Illuminate\Database\Eloquent\Relations\BelongsTo
     */
    public function librarian(): BelongsTo
    {
        // 'librarian_id' is the foreign key (not default 'user_id')
        return $this->belongsTo(User::class, 'librarian_id');
    }

    // =========================================================================
    // SCOPES
    // =========================================================================

    /**
     * Scope to filter currently borrowed (not yet returned) transactions.
     *
     * Books with status 'borrowed' are currently with students.
     *
     * Usage:
     * Transaction::borrowed()->get();                 // All currently borrowed
     * Transaction::borrowed()->with('student')->get(); // With student info
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder
     * @return \Illuminate\Database\Eloquent\Builder Modified query builder
     */
    public function scopeBorrowed(Builder $query): Builder
    {
        return $query->where('status', 'borrowed');
    }

    /**
     * Scope to filter returned transactions.
     *
     * Books with status 'returned' have been given back.
     *
     * Usage:
     * Transaction::returned()->get();           // All returned transactions
     * Transaction::returned()->latest()->get(); // Most recent returns first
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder
     * @return \Illuminate\Database\Eloquent\Builder Modified query builder
     */
    public function scopeReturned(Builder $query): Builder
    {
        return $query->where('status', 'returned');
    }

    /**
     * Scope to filter overdue transactions.
     *
     * Books with status 'overdue' are past their due date and not returned.
     *
     * Usage:
     * Transaction::overdue()->get();                  // All overdue transactions
     * Transaction::overdue()->with('student')->get(); // With student info for follow-up
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder
     * @return \Illuminate\Database\Eloquent\Builder Modified query builder
     */
    public function scopeOverdue(Builder $query): Builder
    {
        return $query->where('status', 'overdue');
    }

    /**
     * Scope to filter transactions that are currently active (not returned).
     *
     * Active means either 'borrowed' or 'overdue' status.
     *
     * Usage:
     * Transaction::active()->get(); // All books currently out
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder
     * @return \Illuminate\Database\Eloquent\Builder Modified query builder
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->whereIn('status', ['borrowed', 'overdue']);
    }

    /**
     * Scope to filter transactions with unpaid fines.
     *
     * Usage:
     * Transaction::withUnpaidFines()->get(); // All transactions with unpaid fines
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder
     * @return \Illuminate\Database\Eloquent\Builder Modified query builder
     */
    public function scopeWithUnpaidFines(Builder $query): Builder
    {
        return $query->where('fine_amount', '>', 0)
                     ->where('fine_paid', false);
    }

    /**
     * Scope to filter transactions due on a specific date.
     *
     * Usage:
     * Transaction::dueOn(today())->get();    // Books due today
     * Transaction::dueOn(tomorrow())->get(); // Books due tomorrow
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder
     * @param \Carbon\Carbon|string $date The date to filter by
     * @return \Illuminate\Database\Eloquent\Builder Modified query builder
     */
    public function scopeDueOn(Builder $query, $date): Builder
    {
        return $query->whereDate('due_date', $date);
    }

    /**
     * Scope to filter transactions due before a specific date.
     *
     * Useful for finding all potentially overdue items.
     *
     * Usage:
     * Transaction::dueBefore(today())->borrowed()->get(); // Overdue books
     *
     * @param \Illuminate\Database\Eloquent\Builder $query The query builder
     * @param \Carbon\Carbon|string $date The date to compare against
     * @return \Illuminate\Database\Eloquent\Builder Modified query builder
     */
    public function scopeDueBefore(Builder $query, $date): Builder
    {
        return $query->whereDate('due_date', '<', $date);
    }

    // =========================================================================
    // STATUS & FINE METHODS
    // =========================================================================

    /**
     * Check if this transaction is overdue.
     *
     * A transaction is overdue if:
     * 1. It hasn't been returned yet (status is not 'returned')
     * 2. The due date has passed
     *
     * Usage:
     * if ($transaction->isOverdue()) {
     *     // Show overdue warning
     * }
     *
     * @return bool True if overdue, false otherwise
     */
    public function isOverdue(): bool
    {
        // Already returned, not overdue
        if ($this->status === 'returned') {
            return false;
        }

        // Check if due date has passed
        return $this->due_date->isPast();
    }

    /**
     * Get the number of days this transaction is overdue.
     *
     * Returns 0 if not overdue.
     *
     * Usage:
     * $days = $transaction->daysOverdue();
     * if ($days > 0) {
     *     echo "This book is {$days} days overdue.";
     * }
     *
     * @return int Number of days overdue (0 if not overdue)
     */
    public function daysOverdue(): int
    {
        // If returned, calculate based on returned_date
        if ($this->returned_date) {
            $compareDate = $this->returned_date;
        } else {
            $compareDate = Carbon::today();
        }

        // Calculate difference
        $diff = $this->due_date->diffInDays($compareDate, false);

        // Return 0 if not overdue (negative means not due yet)
        return max(0, $diff);
    }

    /**
     * Calculate the fine amount for this transaction.
     *
     * Fine Formula: (days_overdue - grace_period) × fine_per_day
     *
     * Uses settings from database:
     * - fine_per_day: Amount charged per day (default: ₱5.00)
     * - grace_period: Days before fines start (default: 1)
     *
     * Usage:
     * $fine = $transaction->calculateFine();
     * echo "Fine amount: ₱{$fine}";
     *
     * @return float Calculated fine amount in PHP
     */
    public function calculateFine(): float
    {
        $daysOverdue = $this->daysOverdue();

        // No fine if not overdue
        if ($daysOverdue <= 0) {
            return 0.00;
        }

        // Get settings (with defaults)
        $finePerDay = (float) Setting::get('fine_per_day', 5.00);
        $gracePeriod = (int) Setting::get('grace_period', 1);

        // Calculate chargeable days (after grace period)
        $chargeableDays = max(0, $daysOverdue - $gracePeriod);

        // Calculate fine
        return round($chargeableDays * $finePerDay, 2);
    }

    /**
     * Mark this transaction as returned.
     *
     * This method:
     * 1. Sets returned_date to today
     * 2. Changes status to 'returned'
     * 3. Calculates and sets fine_amount if overdue
     * 4. Saves the transaction
     *
     * Note: This does NOT increment book copies. That should be done
     * separately using $book->incrementCopy() in the BorrowingService.
     *
     * Usage:
     * $transaction->markAsReturned();
     *
     * With custom return date:
     * $transaction->markAsReturned('2024-01-20');
     *
     * @param \Carbon\Carbon|string|null $returnDate The return date (defaults to today)
     * @return bool True if saved successfully
     */
    public function markAsReturned($returnDate = null): bool
    {
        // Set return date (default to today)
        $this->returned_date = $returnDate ? Carbon::parse($returnDate) : Carbon::today();

        // Update status
        $this->status = 'returned';

        // Calculate fine if overdue
        $this->fine_amount = $this->calculateFine();

        // Save and return success status
        return $this->save();
    }

    /**
     * Mark the fine as paid for this transaction.
     *
     * Usage:
     * $transaction->markFinePaid();
     *
     * @return bool True if saved successfully
     */
    public function markFinePaid(): bool
    {
        $this->fine_paid = true;
        return $this->save();
    }

    /**
     * Check if the transaction has an unpaid fine.
     *
     * Usage:
     * if ($transaction->hasUnpaidFine()) {
     *     echo "Fine due: ₱{$transaction->fine_amount}";
     * }
     *
     * @return bool True if there's an unpaid fine, false otherwise
     */
    public function hasUnpaidFine(): bool
    {
        return $this->fine_amount > 0 && !$this->fine_paid;
    }

    // =========================================================================
    // ACCESSORS
    // =========================================================================

    /**
     * Get a human-readable status label.
     *
     * Converts status codes to user-friendly labels.
     *
     * Usage in Blade:
     * <span class="badge">{{ $transaction->status_label }}</span>
     *
     * @return string Human-readable status
     */
    public function getStatusLabelAttribute(): string
    {
        return match($this->status) {
            'borrowed' => 'Borrowed',
            'returned' => 'Returned',
            'overdue' => 'Overdue',
            default => ucfirst($this->status),
        };
    }

    /**
     * Get the CSS class for the status badge.
     *
     * Returns a Tailwind CSS class based on status.
     *
     * Usage in Blade:
     * <span class="badge {{ $transaction->status_color }}">
     *     {{ $transaction->status_label }}
     * </span>
     *
     * @return string CSS class for status color
     */
    public function getStatusColorAttribute(): string
    {
        return match($this->status) {
            'borrowed' => 'bg-primary-100 text-primary-800',
            'returned' => 'bg-success-100 text-success-800',
            'overdue' => 'bg-danger-100 text-danger-800',
            default => 'bg-gray-100 text-gray-800',
        };
    }

    /**
     * Get formatted fine amount with currency symbol.
     *
     * Usage in Blade:
     * {{ $transaction->formatted_fine }}  // Outputs: ₱50.00
     *
     * @return string Formatted fine with PHP currency symbol
     */
    public function getFormattedFineAttribute(): string
    {
        return '₱' . number_format($this->fine_amount, 2);
    }

    /**
     * Get the number of days until due (or overdue).
     *
     * Positive = days remaining
     * Negative = days overdue
     * Zero = due today
     *
     * Usage:
     * $days = $transaction->days_until_due;
     * if ($days < 0) {
     *     echo abs($days) . " days overdue";
     * } elseif ($days == 0) {
     *     echo "Due today!";
     * } else {
     *     echo "{$days} days remaining";
     * }
     *
     * @return int Days until due (negative if overdue)
     */
    public function getDaysUntilDueAttribute(): int
    {
        if ($this->status === 'returned') {
            return 0;
        }

        return Carbon::today()->diffInDays($this->due_date, false);
    }
}
