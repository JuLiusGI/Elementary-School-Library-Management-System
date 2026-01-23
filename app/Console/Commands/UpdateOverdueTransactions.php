<?php

/**
 * UpdateOverdueTransactions Command
 *
 * This Artisan command updates the status of transactions that are past their due date
 * from 'borrowed' to 'overdue'. It should be run daily via the Laravel scheduler.
 *
 * The command:
 * 1. Finds all transactions with status 'borrowed' where due_date < today
 * 2. Updates their status to 'overdue'
 * 3. Logs the number of transactions updated
 *
 * Usage:
 * - Manual: php artisan transactions:update-overdue
 * - Scheduled: Automatically runs daily at midnight
 *
 * @see App\Models\Transaction
 */

namespace App\Console\Commands;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class UpdateOverdueTransactions extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'transactions:update-overdue
                            {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Update transactions that are past their due date to overdue status';

    /**
     * Execute the console command.
     *
     * This method finds all borrowed transactions that are past their due date
     * and updates their status to 'overdue'.
     *
     * @return int Command exit code (0 for success)
     */
    public function handle(): int
    {
        $this->info('Checking for overdue transactions...');

        // Get today's date at the start of the day
        $today = Carbon::today();

        // Find all transactions that:
        // - Have status 'borrowed' (not already marked as overdue or returned)
        // - Have a due_date before today
        $overdueQuery = Transaction::where('status', 'borrowed')
            ->whereDate('due_date', '<', $today);

        // Get the count before updating
        $count = $overdueQuery->count();

        if ($count === 0) {
            $this->info('No overdue transactions found.');
            return Command::SUCCESS;
        }

        // If dry run, just show what would be updated
        if ($this->option('dry-run')) {
            $this->warn("DRY RUN: Would update {$count} transaction(s) to overdue status.");

            // Show details of transactions that would be updated
            $transactions = $overdueQuery->with(['student', 'book'])->get();
            $this->table(
                ['ID', 'Student', 'Book', 'Due Date', 'Days Overdue'],
                $transactions->map(function ($t) use ($today) {
                    return [
                        $t->id,
                        $t->student->full_name,
                        Str::limit($t->book->title, 30),
                        $t->due_date->format('M d, Y'),
                        $t->due_date->diffInDays($today) . ' days',
                    ];
                })
            );

            return Command::SUCCESS;
        }

        // Update the transactions
        $updated = $overdueQuery->update(['status' => 'overdue']);

        $this->info("Updated {$updated} transaction(s) to overdue status.");

        // Log the update for auditing purposes
        Log::info("Overdue transactions update: {$updated} transaction(s) marked as overdue", [
            'date' => $today->toDateString(),
            'count' => $updated,
        ]);

        return Command::SUCCESS;
    }
}
