<?php

/**
 * CheckOverdueBooks Command
 *
 * Checks all borrowed transactions and updates those past their due date
 * to 'overdue' status. This command should be run daily, either manually
 * or through the Laravel scheduler.
 *
 * What it does:
 * 1. Finds all transactions with status 'borrowed' where due_date < today
 * 2. Updates their status to 'overdue'
 * 3. Displays a summary of changes
 *
 * Usage:
 * php artisan library:check-overdue              # Update overdue transactions
 * php artisan library:check-overdue --dry-run     # Preview without making changes
 *
 * @package App\Console\Commands
 * @see App\Models\Transaction
 */

namespace App\Console\Commands;

use App\Models\Transaction;
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;

class CheckOverdueBooks extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'library:check-overdue
                            {--dry-run : Show what would be updated without making changes}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check and update overdue book transactions';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $this->info('');
        $this->info('Checking for overdue transactions...');
        $this->info('Date: ' . Carbon::today()->format('M d, Y'));
        $this->info('');

        $today = Carbon::today();

        // Find all borrowed transactions that are past due date
        $overdueQuery = Transaction::where('status', 'borrowed')
            ->whereDate('due_date', '<', $today);

        $count = $overdueQuery->count();

        if ($count === 0) {
            $this->info('No overdue transactions found. All books are on time!');
            return Command::SUCCESS;
        }

        // Show the transactions that will be/would be updated
        $transactions = $overdueQuery->with(['student', 'book'])->get();

        $this->table(
            ['ID', 'Student', 'Book', 'Borrowed', 'Due Date', 'Days Overdue'],
            $transactions->map(function ($t) use ($today) {
                return [
                    $t->id,
                    $t->student ? $t->student->full_name : 'N/A',
                    $t->book ? Str::limit($t->book->title, 30) : 'N/A',
                    $t->borrowed_date->format('M d, Y'),
                    $t->due_date->format('M d, Y'),
                    $t->due_date->diffInDays($today) . ' days',
                ];
            })
        );

        // Dry run mode: preview only
        if ($this->option('dry-run')) {
            $this->warn("DRY RUN: Found {$count} transaction(s) that would be marked as overdue.");
            $this->info('Run without --dry-run to apply changes.');
            return Command::SUCCESS;
        }

        // Update the transactions
        $updated = Transaction::where('status', 'borrowed')
            ->whereDate('due_date', '<', $today)
            ->update(['status' => 'overdue']);

        $this->info("Updated {$updated} transaction(s) to overdue status.");

        // Log for auditing
        Log::info("Library overdue check: {$updated} transaction(s) marked as overdue", [
            'date' => $today->toDateString(),
            'count' => $updated,
        ]);

        return Command::SUCCESS;
    }
}
