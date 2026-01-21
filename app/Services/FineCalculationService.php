<?php

/**
 * FineCalculationService.php
 *
 * This service handles all business logic related to fine calculation and management.
 * It calculates fines for overdue books based on the library's fine policy:
 * - Fine per day (default: ₱5.00)
 * - Grace period (default: 1 day)
 *
 * The fine formula is:
 * fine = max(0, days_overdue - grace_period) × fine_per_day
 *
 * Example:
 * - Book is 5 days overdue
 * - Grace period is 1 day
 * - Fine per day is ₱5.00
 * - Fine = (5 - 1) × 5 = ₱20.00
 *
 * @package App\Services
 * @author  Library Management System
 * @version 1.0
 */

namespace App\Services;

use App\Models\Transaction;
use App\Models\Setting;
use Carbon\Carbon;

/**
 * Class FineCalculationService
 *
 * Handles fine calculation for overdue books.
 * Uses settings from the database for fine rates and grace periods,
 * making the system configurable without code changes.
 */
class FineCalculationService
{
    /**
     * Calculate the fine amount for an overdue book transaction
     *
     * This method checks how many days a book is overdue and calculates
     * the fine based on the school's fine policy (stored in settings).
     * It applies a grace period before charging fines.
     *
     * @param Transaction $transaction The borrowing transaction to calculate fine for
     * @return float The calculated fine amount in Philippine Pesos
     *
     * @example
     * $fineService = new FineCalculationService();
     * $fine = $fineService->calculateFine($transaction);
     * echo "Fine amount: ₱" . number_format($fine, 2);
     */
    public function calculateFine(Transaction $transaction): float
    {
        // If the book has been returned, use the return date for calculation
        // Otherwise, use today's date
        $endDate = $transaction->returned_date
            ? Carbon::parse($transaction->returned_date)
            : Carbon::now();

        // Get the due date
        $dueDate = Carbon::parse($transaction->due_date);

        // If the book is not overdue, no fine
        if ($endDate->lte($dueDate)) {
            return 0.00;
        }

        // Calculate how many days the book is overdue
        // diffInDays() returns the absolute difference in days
        $daysOverdue = $dueDate->diffInDays($endDate);

        // Get fine settings from database
        // These can be changed by admin without modifying code
        $finePerDay = (float) Setting::getValue('fine_per_day', 5.00);
        $gracePeriod = (int) Setting::getValue('grace_period', 1);

        // Calculate chargeable days (subtract grace period, minimum 0)
        // Example: If 3 days overdue with 1 day grace = 2 chargeable days
        $chargeableDays = max(0, $daysOverdue - $gracePeriod);

        // Calculate total fine (chargeable days × rate per day)
        $totalFine = $chargeableDays * $finePerDay;

        return round($totalFine, 2);
    }

    /**
     * Get the number of days a book is overdue
     *
     * @param Transaction $transaction The transaction to check
     * @return int Number of days overdue (0 if not overdue)
     */
    public function getDaysOverdue(Transaction $transaction): int
    {
        // If already returned, use return date; otherwise use today
        $endDate = $transaction->returned_date
            ? Carbon::parse($transaction->returned_date)
            : Carbon::now();

        $dueDate = Carbon::parse($transaction->due_date);

        // If not past due date, return 0
        if ($endDate->lte($dueDate)) {
            return 0;
        }

        return $dueDate->diffInDays($endDate);
    }

    /**
     * Check if a transaction is currently overdue
     *
     * @param Transaction $transaction The transaction to check
     * @return bool True if the book is overdue and not yet returned
     */
    public function isOverdue(Transaction $transaction): bool
    {
        // If already returned, it's no longer overdue
        if ($transaction->returned_date) {
            return false;
        }

        // Check if current date is past the due date
        return Carbon::now()->gt(Carbon::parse($transaction->due_date));
    }

    /**
     * Get fine rate information
     *
     * Returns the current fine policy settings for display purposes.
     *
     * @return array Contains 'fine_per_day' and 'grace_period'
     *
     * @example
     * $policy = $fineService->getFinePolicy();
     * echo "Fine: ₱{$policy['fine_per_day']} per day after {$policy['grace_period']} day grace period";
     */
    public function getFinePolicy(): array
    {
        return [
            'fine_per_day' => (float) Setting::getValue('fine_per_day', 5.00),
            'grace_period' => (int) Setting::getValue('grace_period', 1),
            'currency' => '₱', // Philippine Peso
        ];
    }

    /**
     * Mark a fine as paid
     *
     * Updates the transaction record to indicate the fine has been paid.
     *
     * @param Transaction $transaction The transaction with the fine
     * @param float|null $amountPaid The amount paid (optional, defaults to full fine)
     * @return Transaction The updated transaction
     */
    public function markFinePaid(Transaction $transaction, ?float $amountPaid = null): Transaction
    {
        // If no amount specified, assume full payment
        if ($amountPaid === null) {
            $amountPaid = $transaction->fine_amount;
        }

        // Update the transaction
        $transaction->update([
            'fine_paid' => true,
        ]);

        return $transaction->fresh();
    }

    /**
     * Get total unpaid fines for a student
     *
     * Calculates the sum of all unpaid fines for a given student.
     *
     * @param int $studentId The student's ID
     * @return float Total unpaid fine amount
     */
    public function getTotalUnpaidFines(int $studentId): float
    {
        return Transaction::where('student_id', $studentId)
            ->where('fine_amount', '>', 0)
            ->where('fine_paid', false)
            ->sum('fine_amount');
    }

    /**
     * Waive a fine (admin only)
     *
     * Sets the fine amount to zero for special circumstances.
     * This should only be used by administrators with proper authorization.
     *
     * @param Transaction $transaction The transaction with the fine to waive
     * @param string $reason The reason for waiving the fine
     * @return Transaction The updated transaction
     */
    public function waiveFine(Transaction $transaction, string $reason): Transaction
    {
        $transaction->update([
            'fine_amount' => 0,
            'fine_paid' => true,
            'notes' => ($transaction->notes ? $transaction->notes . "\n" : '') .
                       "Fine waived: {$reason}",
        ]);

        return $transaction->fresh();
    }
}
